<?php
session_start();
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';

header('Content-Type: application/json');

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$reservation_id) {
    echo json_encode(['error' => 'ID de réservation manquant']);
    exit();
}

// Récupérer les détails de la réservation
$stmt = $conn->prepare("
    SELECT r.*, t.type, e1.nom as equipe1, e2.nom as equipe2, m.date_match, m.heure_match, m.lieu, p.transaction_id
    FROM reservations r
    JOIN tickets t ON r.ticket_id = t.id
    JOIN matchs m ON t.match_id = m.id
    LEFT JOIN equipes e1 ON m.equipe1_id = e1.id
    LEFT JOIN equipes e2 ON m.equipe2_id = e2.id
    JOIN paiements p ON r.id = p.reservation_id
    WHERE r.id = ? AND r.utilisateur_id = ?
");
$stmt->bind_param("ii", $reservation_id, $_SESSION['user_id']);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();

if (!$reservation) {
    echo json_encode(['error' => 'Réservation introuvable']);
    exit();
}

// Retourner les données au format JSON
echo json_encode($reservation); 