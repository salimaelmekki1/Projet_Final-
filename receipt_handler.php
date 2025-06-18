<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';
session_start();
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';
require_once 'includes/PDFGenerator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

$action = $_POST['action'] ?? '';
$reservation_id = $_POST['reservation_id'] ?? 0;

if (!$reservation_id) {
    die(json_encode(['success' => false, 'message' => 'ID de réservation manquant']));
}

// Vérifier que la réservation appartient à l'utilisateur
$stmt = $conn->prepare("
    SELECT r.*, t.*, m.*, e1.nom as equipe1, e2.nom as equipe2, p.transaction_id
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
    die(json_encode(['success' => false, 'message' => 'Réservation introuvable']));
}

// Préparer les données pour le PDF
$match_data = [
    'equipe1' => $reservation['equipe1'],
    'equipe2' => $reservation['equipe2'],
    'date_match' => $reservation['date_match'],
    'heure_match' => $reservation['heure_match'],
    'lieu' => $reservation['lieu']
];

$ticket_data = [
    'type' => $reservation['type'],
    'numero_place' => $reservation['numero_place'],
    'prix_base' => $reservation['prix_base']
];

// Générer le PDF
$pdf = new PDFGenerator($reservation, $match_data, $ticket_data);

switch ($action) {
    case 'download':
        // Nettoyer la sortie
        if (ob_get_length()) ob_clean();
        
        // Définir les en-têtes HTTP appropriés
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="recu_reservation_' . $reservation_id . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Générer le PDF
        $pdf_content = $pdf->output('recu_reservation_' . $reservation_id . '.pdf');
        
        // Envoyer le contenu du PDF
        echo $pdf_content;
        exit();
        break;

        case 'email':
            if (ob_get_length()) ob_clean();
        
            // Générer le PDF dans une variable
            $pdfContent = $pdf->output('S'); // 'S' = string (pas fichier)
        
            // Créer une instance de PHPMailer
            $mail = new PHPMailer(true);
        
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'meryemmoumene11@gmail.com'; // Ton adresse Gmail
                $mail->Password = 'kjbw rdsi zgyl rjkf';       // Mot de passe d’application
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
        
                $mail->setFrom('meryemmoumene11@gmail.com', 'Meryem MOUMENE');
                $mail->addAddress($_SESSION['user_email'], $_SESSION['user_nom']); // Email utilisateur connecté
        
                $mail->isHTML(true);
                $mail->Subject = "Votre recu de reservation #" . $reservation_id;
                $mail->Body = "Bonjour " . $_SESSION['user_nom'] . ",<br><br>";
                $mail->Body .= "Veuillez trouver ci-joint votre reçu de réservation.<br><br>";
                $mail->Body .= "Cordialement,<br>L'équipe de réservation";
        
                // Ajouter le PDF comme pièce jointe (en mémoire)
                $mail->addStringAttachment($pdfContent, 'recu_reservation_' . $reservation_id . '.pdf');
        
                $mail->send();
                echo json_encode(['success' => true, 'message' => 'Reçu envoyé avec succès à ' . $_SESSION['user_email']]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du reçu : ' . $mail->ErrorInfo
                ]);
            }
            break;
        

    
        default:
        die(json_encode(['success' => false, 'message' => 'Action non reconnue']));
} 