<?php
session_start();
require_once 'includes/auth_check.php';
requireAdmin();

include 'config/config.php';

$message = '';
$success = '';

// Traitement de l'ajout d'un match
if (isset($_POST['add_match'])) {
    $equipe1_id = $_POST['equipe1_id'];
    $equipe2_id = $_POST['equipe2_id'];
    $date_match = $_POST['date_match'];
    $heure_match = $_POST['heure_match'];
    $lieu = $_POST['lieu'];

    $stmt = $conn->prepare("INSERT INTO matchs (equipe1_id, equipe2_id, date_match, heure_match, lieu) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $equipe1_id, $equipe2_id, $date_match, $heure_match, $lieu);
    
    if ($stmt->execute()) {
        $success = "Match ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout du match.";
    }
}

// Traitement de l'ajout d'un ticket
if (isset($_POST['add_ticket'])) {
    $match_id = $_POST['match_id'];
    $type = $_POST['type'];
    $prix = $_POST['prix'];
    $quantite_disponible = $_POST['quantite_disponible'];

    $stmt = $conn->prepare("INSERT INTO tickets (match_id, type, prix, quantite_disponible) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdi", $match_id, $type, $prix, $quantite_disponible);
    
    if ($stmt->execute()) {
        $success = "Ticket ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout du ticket.";
    }
}

// Récupérer la liste des équipes
$equipes = $conn->query("SELECT * FROM equipes ORDER BY nom")->fetch_all(MYSQLI_ASSOC);

// Récupérer la liste des matchs
$matchs = $conn->query("
    SELECT m.*, e1.nom as equipe1, e2.nom as equipe2 
    FROM matchs m 
    LEFT JOIN equipes e1 ON m.equipe1_id = e1.id 
    LEFT JOIN equipes e2 ON m.equipe2_id = e2.id 
    ORDER BY m.date_match DESC
")->fetch_all(MYSQLI_ASSOC);

// Récupérer les messages non répondu
$messages_non_repondu = $conn->query("
    SELECT COUNT(*) as count 
    FROM messages 
    WHERE reponse IS NULL
")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin_profile.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1>Profil Administrateur</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="profile-content">
            <!-- Section Messages -->
            <div class="profile-section">
                <h2>Gestion des Messages</h2>
                <div class="messages-overview">
                    <div class="message-stats">
                        <p><i class="fas fa-envelope"></i> Messages non répondu : <strong><?= $messages_non_repondu ?></strong></p>
                    </div>
                    <a href="admin_messages.php" class="btn btn-primary">
                        <i class="fas fa-comments"></i> Gérer les messages
                    </a>
                </div>
            </div>

            <!-- Section Ajout de Match -->
            <div class="profile-section">
                <h2>Ajouter un Match</h2>
                <form action="" method="post" class="admin-form">
                    <div class="form-group">
                        <label for="equipe1_id">Équipe 1</label>
                        <select name="equipe1_id" id="equipe1_id" required>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="equipe2_id">Équipe 2</label>
                        <select name="equipe2_id" id="equipe2_id" required>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_match">Date du match</label>
                        <input type="date" name="date_match" id="date_match" required>
                    </div>
                    <div class="form-group">
                        <label for="heure_match">Heure du match</label>
                        <input type="time" name="heure_match" id="heure_match" required>
                    </div>
                    <div class="form-group">
                        <label for="lieu">Lieu</label>
                        <input type="text" name="lieu" id="lieu" required>
                    </div>
                    <button type="submit" name="add_match" class="btn btn-primary">Ajouter le match</button>
                </form>
            </div>

            <!-- Section Ajout de Ticket -->
            <div class="profile-section">
                <h2>Ajouter un Ticket</h2>
                <form action="" method="post" class="admin-form">
                    <div class="form-group">
                        <label for="match_id">Match</label>
                        <select name="match_id" id="match_id" required>
                            <?php foreach ($matchs as $match): ?>
                                <option value="<?= $match['id'] ?>">
                                    <?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?> 
                                    (<?= date('d/m/Y', strtotime($match['date_match'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Type de ticket</label>
                        <input type="text" name="type" id="type" required placeholder="ex: VIP, Standard, etc.">
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix</label>
                        <input type="number" name="prix" id="prix" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantite_disponible">Quantité disponible</label>
                        <input type="number" name="quantite_disponible" id="quantite_disponible" required>
                    </div>
                    <button type="submit" name="add_ticket" class="btn btn-primary">Ajouter le ticket</button>
                </form>
            </div>

            <!-- Liste des matchs -->
            <div class="profile-section">
                <h2>Liste des Matchs</h2>
                <div class="matches-list">
                    <?php foreach ($matchs as $match): ?>
                        <div class="match-card">
                            <h3><?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?></h3>
                            <p><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($match['date_match'])) ?></p>
                            <p><i class="fas fa-clock"></i> <?= $match['heure_match'] ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($match['lieu']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 