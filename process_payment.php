<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

function sendJsonResponse($success, $message, $data = []) {
    ob_clean();
    header('Content-Type: application/json');
    $response = array_merge(['success' => $success, 'message' => $message], $data);
    echo json_encode($response);
    exit;
}

function handleError($message) {
    error_log($message);
    sendJsonResponse(false, $message);
}

try {
    session_start();

    $required_files = [
        'includes/auth_check.php',
        'config/config.php',
        'includes/PDFGenerator.php'
    ];

    foreach ($required_files as $file) {
        if (!file_exists($file)) {
            handleError("Fichier manquant : " . $file);
        }
    }

    require_once 'includes/auth_check.php';
    requireLogin();
    include 'config/config.php';
    require_once 'includes/PDFGenerator.php';

    if (!isset($conn) || !$conn) {
        handleError("Connexion à la base de données non établie");
    }

    if (!isset($_SESSION['user_id'])) {
        handleError("Session invalide - Veuillez vous reconnecter");
    }

    $json = file_get_contents('php://input');
    if (empty($json)) {
        handleError("Aucune donnée reçue");
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        handleError("Données JSON invalides : " . json_last_error_msg());
    }

    if (!isset($data['match_id']) || !isset($data['tickets']) || !is_array($data['tickets'])) {
        handleError("Données manquantes ou invalides");
    }

    $match_id = $data['match_id'];
    $tickets = $data['tickets'];
    $reservation_ids = [];

    $conn->begin_transaction();

    foreach ($tickets as $ticket) {
        if (!isset($ticket['ticket_id']) || !isset($ticket['quantite']) || !isset($ticket['prix'])) {
            throw new Exception("Données de ticket invalides : " . json_encode($ticket));
        }

        $ticket_id = $ticket['ticket_id'];
        $quantite = $ticket['quantite'];
        $prix = $ticket['prix'];
        $total = $quantite * $prix;

        // Vérifier l'existence du ticket
        $check_stmt = $conn->prepare("SELECT id FROM tickets WHERE id = ?");
        $check_stmt->bind_param("i", $ticket_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows === 0) {
            throw new Exception("Ticket introuvable : ID " . $ticket_id);
        }

        // Insérer dans reservations
        $stmt = $conn->prepare("INSERT INTO reservations (utilisateur_id, ticket_id, quantite, prix_total, date_reservation) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) throw new Exception("Erreur SQL réservation : " . $conn->error);

        $stmt->bind_param("iiid", $_SESSION['user_id'], $ticket_id, $quantite, $total);
        if (!$stmt->execute()) {
            throw new Exception("Échec insertion réservation : " . $stmt->error);
        }

        $reservation_id = $conn->insert_id;
        $reservation_ids[] = $reservation_id;

        // Insérer dans paiements
        $stmt = $conn->prepare("INSERT INTO paiements (reservation_id, montant, date_paiement, statut) VALUES (?, ?, NOW(), 'completed')");
        if (!$stmt) throw new Exception("Erreur SQL paiement : " . $conn->error);

        $stmt->bind_param("id", $reservation_id, $total);
        if (!$stmt->execute()) {
            throw new Exception("Échec insertion paiement : " . $stmt->error);
        }
    }

    $conn->commit();

    // Tu peux ici générer un PDF (fonction dans PDFGenerator.php)
    // Exemple : generateReceiptPDF($reservation_ids);

    sendJsonResponse(true, "Paiement traité avec succès", ['reservation_ids' => $reservation_ids]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }

    sendJsonResponse(false, "Erreur inattendue : " . $e->getMessage());
}
