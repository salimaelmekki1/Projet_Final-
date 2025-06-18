<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Message envoyé</title>
    <link rel="stylesheet" href="css/traitement_contact.css">
</head>
<body>

<div class="message-box-container">
    <?php
    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo '<div class="message-box error">Vous devez être connecté pour envoyer un message.</div>';
        exit;
    }

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=match_reservation', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $utilisateur_id = $_SESSION['user_id'];
        $nom = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['phone'] ?? '';
        $message = $_POST['message'] ?? '';

        if ($nom && $email && $message) {
            $stmt = $pdo->prepare("INSERT INTO messages (utilisateur_id, nom, email, telephone, message, date_envoi) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$utilisateur_id, $nom, $email, $telephone, $message]);

            echo '<div class="message-box success">🎉 Message envoyé avec succès.</div>';
        } else {
            echo '<div class="message-box error">❗ Veuillez remplir les champs requis.</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="message-box error">Erreur : ' . $e->getMessage() . '</div>';
    }
    ?>
</div>

</body>
</html>
