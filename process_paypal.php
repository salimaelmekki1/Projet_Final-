<?php
session_start();
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = intval($_POST['ticket_id']);
    $quantite = intval($_POST['quantite']);

    // Récupération des données du ticket
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();

    if (!$ticket) {
        die("Ticket introuvable.");
    }

    // Calcul du prix total (exemple : réduction pour réservation anticipée)
    $date_today = new DateTime();
    $match_stmt = $conn->prepare("SELECT date_match FROM matchs WHERE id = ?");
    $match_stmt->bind_param("i", $ticket['match_id']);
    $match_stmt->execute();
    $match = $match_stmt->get_result()->fetch_assoc();

    $date_match = new DateTime($match['date_match']);
    $interval = $date_today->diff($date_match)->days;
    $prix = $ticket['prix_base'];

    if ($interval >= 10) {
        $prix *= 0.9; // 10% de réduction
    }

    $prix_total = $quantite * $prix;

    // ⚠️ Ici tu devrais insérer dans reservations et rediriger vers PayPal
    // Simulation :
    echo "<h2>Simulation PayPal</h2>";
    echo "<p>Ticket : " . htmlspecialchars($ticket['type']) . "</p>";
    echo "<p>Quantité : $quantite</p>";
    echo "<p>Total à payer : " . number_format($prix_total, 2) . "€</p>";
    echo "<p><strong>Redirection vers PayPal...</strong></p>";

    // Simulation redirection (tu remplaces ici par lien réel PayPal)
    header("refresh:3;url=index.php");
} else {
    echo "Méthode non autorisée.";
}
?>
