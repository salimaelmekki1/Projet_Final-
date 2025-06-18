<?php
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';

// Récupérer les réservations de l'utilisateur
$stmt = $conn->prepare("
    SELECT r.*, t.type, t.prix_base, t.numero_place, e1.nom as equipe1, e2.nom as equipe2, m.date_match, m.heure_match, m.lieu
    FROM reservations r
    JOIN tickets t ON r.ticket_id = t.id
    JOIN matchs m ON t.match_id = m.id
    LEFT JOIN equipes e1 ON m.equipe1_id = e1.id
    LEFT JOIN equipes e2 ON m.equipe2_id = e2.id
    WHERE r.utilisateur_id = ?
    ORDER BY m.date_match DESC, m.heure_match DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <h1>Mes Réservations</h1>

        <?php if (empty($reservations)): ?>
            <p>Vous n'avez pas encore de réservations.</p>
        <?php else: ?>
            <div class="reservations-list">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <h3><?= htmlspecialchars($reservation['equipe1']) ?> vs <?= htmlspecialchars($reservation['equipe2']) ?></h3>
                        <p>Date : <?= htmlspecialchars($reservation['date_match']) ?> à <?= htmlspecialchars($reservation['heure_match']) ?></p>
                        <p>Lieu : <?= htmlspecialchars($reservation['lieu']) ?></p>
                        <p>Type de ticket : <?= htmlspecialchars($reservation['type']) ?></p>
                        <p>Numéro de place : <?= htmlspecialchars($reservation['numero_place']) ?></p>
                        <p>Quantité : <?= htmlspecialchars($reservation['quantite']) ?></p>
                        <p>Prix total : <?= number_format($reservation['prix_base'] * $reservation['quantite'], 2) ?> €</p>
                        <p>Date de réservation : <?= htmlspecialchars($reservation['date_reservation']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 