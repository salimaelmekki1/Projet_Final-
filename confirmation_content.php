<?php
session_start();
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$reservation_id) {
    http_response_code(400);
    echo "ID de réservation manquant.";
    exit();
}

$stmt = $conn->prepare("
    SELECT r.*, t.type, t.numero_place, e1.nom as equipe1, e2.nom as equipe2, m.date_match, m.heure_match, m.lieu, p.transaction_id
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
    http_response_code(404);
    echo "Réservation introuvable.";
    exit();
}
?>

<div class="confirmation-header">
    <h1>Réservation confirmée !</h1>
    <div class="success-icon">✓</div>
</div>

<div class="confirmation-details">
    <h2>Détails de votre réservation</h2>
    <p><strong>Numéro de réservation :</strong> #<?= $reservation_id ?></p>
    <p><strong>Match :</strong> <?= htmlspecialchars($reservation['equipe1']) ?> vs <?= htmlspecialchars($reservation['equipe2']) ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($reservation['date_match']) ?> à <?= htmlspecialchars($reservation['heure_match']) ?></p>
    <p><strong>Lieu :</strong> <?= htmlspecialchars($reservation['lieu']) ?></p>
    <p><strong>Type de ticket :</strong> <?= htmlspecialchars($reservation['type']) ?></p>
    <p><strong>Numéro de place :</strong> <?= htmlspecialchars($reservation['numero_place']) ?></p>
    <p><strong>Quantité :</strong> <?= $reservation['quantite'] ?></p>
    <p><strong>Prix total :</strong> <?= number_format($reservation['prix_total'], 2) ?> €</p>
    <p><strong>Transaction PayPal :</strong> <?= htmlspecialchars($reservation['transaction_id']) ?></p>
</div>

<div class="confirmation-actions">
    <a href="profile.php" class="btn btn-primary">Voir mes réservations</a>
    <a href="index.php" class="btn btn-outline">Retour à l'accueil</a>
</div>
