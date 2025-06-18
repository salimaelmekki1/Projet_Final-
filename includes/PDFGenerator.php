<?php
require_once('fpdf/fpdf.php');

class PDFGenerator {
    private $pdf;
    private $reservation;
    private $match;
    private $ticket;

    public function __construct($reservation, $match, $ticket) {
        $this->reservation = $reservation;
        $this->match = $match;
        $this->ticket = $ticket;
        $this->initPDF();
    }

    private function initPDF() {
        // Créer un nouveau PDF
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
    }

    public function generateReceipt() {
        // En-tête
        $this->pdf->SetFont('Arial', 'B', 20);
        $this->pdf->Cell(0, 15, utf8_decode('Reçu de Réservation'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // Ligne de séparation
        $this->pdf->SetDrawColor(200, 200, 200);
        $this->pdf->Line(10, $this->pdf->GetY(), 200, $this->pdf->GetY());
        $this->pdf->Ln(10);

        // Informations de la réservation
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, utf8_decode('Informations de la Réservation'), 0, 1);
        $this->pdf->Ln(5);

        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(60, 8, utf8_decode('Numéro de réservation:'), 0);
        $this->pdf->Cell(0, 8, '#' . $this->reservation['id'], 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Date de réservation:'), 0);
        $this->pdf->Cell(0, 8, date('d/m/Y H:i', strtotime($this->reservation['date_reservation'])), 0, 1);
        $this->pdf->Ln(10);

        // Détails du match
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, utf8_decode('Détails du Match'), 0, 1);
        $this->pdf->Ln(5);

        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->Cell(0, 10, utf8_decode($this->match['equipe1'] . ' vs ' . $this->match['equipe2']), 0, 1, 'C');
        $this->pdf->Ln(5);

        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(60, 8, utf8_decode('Date du match:'), 0);
        $this->pdf->Cell(0, 8, date('d/m/Y', strtotime($this->match['date_match'])), 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Heure:'), 0);
        $this->pdf->Cell(0, 8, date('H:i', strtotime($this->match['heure_match'])), 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Lieu:'), 0);
        $this->pdf->Cell(0, 8, utf8_decode($this->match['lieu']), 0, 1);
        $this->pdf->Ln(10);

        // Détails des tickets
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, utf8_decode('Détails des Tickets'), 0, 1);
        $this->pdf->Ln(5);

        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(60, 8, utf8_decode('Type de ticket:'), 0);
        $this->pdf->Cell(0, 8, utf8_decode($this->ticket['type']), 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Quantité:'), 0);
        $this->pdf->Cell(0, 8, $this->reservation['quantite'] . utf8_decode(' place(s)'), 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Prix unitaire:'), 0);
        $this->pdf->Cell(0, 8, number_format($this->ticket['prix_base'], 2) . ' EUR', 0, 1);
        $this->pdf->Cell(60, 8, utf8_decode('Prix total:'), 0);
        $this->pdf->Cell(0, 8, number_format($this->reservation['prix_total'], 2) . ' EUR', 0, 1);
        $this->pdf->Ln(10);

        // Ligne de séparation
        $this->pdf->Line(10, $this->pdf->GetY(), 200, $this->pdf->GetY());
        $this->pdf->Ln(10);

        // Message de remerciement
        $this->pdf->SetFont('Arial', 'I', 12);
        $this->pdf->Cell(0, 10, utf8_decode('Merci de votre confiance !'), 0, 1, 'C');
    }

    public function output($filename) {
        // Générer le PDF
        $this->generateReceipt();
        
        // Retourner le contenu du PDF
        return $this->pdf->Output('S');
    }
} 