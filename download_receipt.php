<?php
// Démarrer la session si nécessaire
session_start();

// Nettoyer toute sortie précédente
ob_clean();

// Inclure les fichiers nécessaires
require_once 'includes/fpdf/fpdf.php';
require_once 'config/config.php';

// Vérifier si l'ID de la réservation est fourni
if (!isset($_GET['order_id'])) {
    die('ID de réservation non spécifié');
}

$reservation_id = intval($_GET['order_id']);

// Récupérer les informations de la réservation
$query = "SELECT r.*, u.nom, u.prenom, u.email 
          FROM reservations r 
          JOIN utilisateurs u ON r.utilisateur_id = u.id 
          WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if (!$reservation) {
    die('Réservation non trouvée');
}

// Récupérer les détails du match
$query = "SELECT m.*, e1.nom as equipe1, e2.nom as equipe2, t.type as ticket_type
          FROM reservations r
          JOIN tickets t ON r.ticket_id = t.id
          JOIN matchs m ON t.match_id = m.id
          LEFT JOIN equipes e1 ON m.equipe1_id = e1.id
          LEFT JOIN equipes e2 ON m.equipe2_id = e2.id
          WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$match_details = $stmt->get_result()->fetch_assoc();

// Créer le PDF
$pdf = new FPDF();
$pdf->AddPage();

// En-tête
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 15, utf8_decode('Reçu de Réservation'), 0, 1, 'C');
$pdf->Ln(5);

// Ligne de séparation
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

// Informations de la réservation
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Informations de la Réservation'), 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, utf8_decode('Numéro de réservation:'), 0);
$pdf->Cell(0, 8, '#' . $reservation['id'], 0, 1);
$pdf->Cell(60, 8, utf8_decode('Date de réservation:'), 0);
$pdf->Cell(0, 8, date('d/m/Y H:i', strtotime($reservation['date_reservation'])), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Client:'), 0);
$pdf->Cell(0, 8, utf8_decode($reservation['nom'] . ' ' . $reservation['prenom']), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Email:'), 0);
$pdf->Cell(0, 8, $reservation['email'], 0, 1);
$pdf->Ln(10);

// Détails du match
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Détails du Match'), 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode($match_details['equipe1'] . ' vs ' . $match_details['equipe2']), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, utf8_decode('Date du match:'), 0);
$pdf->Cell(0, 8, date('d/m/Y', strtotime($match_details['date_match'])), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Heure:'), 0);
$pdf->Cell(0, 8, date('H:i', strtotime($match_details['heure_match'])), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Lieu:'), 0);
$pdf->Cell(0, 8, utf8_decode($match_details['lieu']), 0, 1);
$pdf->Ln(10);

// Détails des tickets
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Détails des Tickets'), 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, utf8_decode('Type de ticket:'), 0);
$pdf->Cell(0, 8, utf8_decode($match_details['ticket_type']), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Quantité:'), 0);
$pdf->Cell(0, 8, $reservation['quantite'] . utf8_decode(' place(s)'), 0, 1);
$pdf->Cell(60, 8, utf8_decode('Prix unitaire:'), 0);
$pdf->Cell(0, 8, number_format($reservation['prix_total'] / $reservation['quantite'], 2) . ' EUR', 0, 1);
$pdf->Cell(60, 8, utf8_decode('Prix total:'), 0);
$pdf->Cell(0, 8, number_format($reservation['prix_total'], 2) . ' EUR', 0, 1);
$pdf->Ln(10);

// Ligne de séparation
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

// Message de remerciement
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, utf8_decode('Merci de votre confiance !'), 0, 1, 'C');

// Envoyer le PDF au navigateur
$pdf->Output('D', 'recu_reservation_' . $reservation_id . '.pdf'); 