<?php
session_start();
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';

$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $nouveau_mot_de_passe = trim($_POST['nouveau_mot_de_passe']);
    $confirmer_mot_de_passe = trim($_POST['confirmer_mot_de_passe']);

    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = "Cette adresse email est déjà utilisée.";
    } else {
        // Mettre à jour les informations de base
        $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nom, $email, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $_SESSION['nom'] = $nom;
            $_SESSION['email'] = $email;
            $success = "Profil mis à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour du profil.";
        }

        // Si un nouveau mot de passe est fourni
        if (!empty($nouveau_mot_de_passe)) {
            if ($nouveau_mot_de_passe === $confirmer_mot_de_passe) {
                $hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
                $stmt->bind_param("si", $hash, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success .= " Mot de passe mis à jour.";
                } else {
                    $message .= " Erreur lors de la mise à jour du mot de passe.";
                }
            } else {
                $message = "Les mots de passe ne correspondent pas.";
            }
        }
    }
}

// Récupérer les informations actuelles de l'utilisateur
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Récupérer l'historique des réservations
$stmt = $conn->prepare("
    SELECT r.*, e1.nom as equipe1, e2.nom as equipe2, m.date_match, m.heure_match, m.lieu, t.type as ticket_type
    FROM reservations r
    JOIN tickets t ON r.ticket_id = t.id
    JOIN matchs m ON t.match_id = m.id
    LEFT JOIN equipes e1 ON m.equipe1_id = e1.id
    LEFT JOIN equipes e2 ON m.equipe2_id = e2.id
    WHERE r.utilisateur_id = ?
    ORDER BY r.date_reservation DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// ✅ Récupérer les réponses de l'administration avec utilisateur_id
$stmt = $conn->prepare("
    SELECT message, reponse, date_envoi, date_reponse 
    FROM messages 
    WHERE utilisateur_id = ? AND reponse IS NOT NULL 
    ORDER BY date_reponse DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$reponses_admin = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1>Mon Profil</h1>
        </div>

        <div class="profile-content">
            <div class="profile-section">
                <h2>Informations personnelles</h2>
                <div class="profile-info">
                    <div class="info-group">
                        <label>Nom</label>
                        <p><?= htmlspecialchars($user['nom']) ?></p>
                    </div>
                    <div class="info-group">
                        <label>Prénom</label>
                        <p><?= htmlspecialchars($user['prenom']) ?></p>
                    </div>
                    <div class="info-group">
                        <label>Email</label>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <div class="info-group">
                        <label>Téléphone</label>
                        <p><?= htmlspecialchars($user['telephone']) ?></p>
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn btn-primary" onclick="showEditForm()">
                        <i class="fas fa-edit"></i> Modifier mes informations
                    </button>
                </div>
            </div>

            <div class="profile-section">
                <h2>Mes réservations</h2>
                <?php if (empty($reservations)): ?>
                    <p class="no-reservations">Vous n'avez pas encore de réservations.</p>
                <?php else: ?>
                    <div class="reservations-list">
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="reservation-card">
                                <div class="reservation-header">
                                    <h3><?= htmlspecialchars($reservation['equipe1']) ?> vs <?= htmlspecialchars($reservation['equipe2']) ?></h3>
                                    <span class="reservation-date"><?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?></span>
                                </div>
                                <div class="reservation-details">
                                    <p><i class="fas fa-calendar"></i> <?= htmlspecialchars($reservation['date_match']) ?> à <?= htmlspecialchars($reservation['heure_match']) ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($reservation['lieu']) ?></p>
                                    <p><i class="fas fa-ticket-alt"></i> <?= htmlspecialchars($reservation['ticket_type']) ?> (<?= $reservation['quantite'] ?> place(s))</p>
                                    <p><i class="fas fa-euro-sign"></i> <?= number_format($reservation['prix_total'], 2) ?> €</p>
                                </div>
                                <div class="reservation-actions">
                                    <button class="btn btn-secondary" onclick="downloadReceipt(<?= $reservation['id'] ?>)">
                                        <i class="fas fa-download"></i> Télécharger le reçu
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Réponses de l'administration -->
        <div class="profile-section">
            <h2>Messages de l'administration</h2>
            <?php if (empty($reponses_admin)): ?>
                <p>Aucun message reçu de l'administration pour le moment.</p>
            <?php else: ?>
                <ul class="admin-messages">
                    <?php foreach ($reponses_admin as $msg): ?>
                        <li class="admin-message">
                            <p><strong>Votre message :</strong> <?= htmlspecialchars($msg['message']) ?></p>
                            <p><strong>Réponse de l'administration :</strong> <?= htmlspecialchars($msg['reponse']) ?></p>
                            <p><em>Envoyé le <?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?> — Réponse le <?= date('d/m/Y H:i', strtotime($msg['date_reponse'])) ?></em></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <!-- Formulaire de modification (caché par défaut) -->
        <div id="editForm" class="edit-form" style="display: none;">
            <h2>Modifier mes informations</h2>
            <form action="update_profile.php" method="post">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
                </div>
                <div class="form-group">
                    <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <button type="button" class="btn btn-secondary" onclick="hideEditForm()">Annuler</button>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function showEditForm() {
            document.getElementById('editForm').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function hideEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }

        function downloadReceipt(reservationId) {
            window.location.href = `download_receipt.php?order_id=${reservationId}`;
        }
    </script>
</body>
</html> 