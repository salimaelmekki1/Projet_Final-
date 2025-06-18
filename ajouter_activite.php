<?php
session_start();
include 'config/config.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $conn->real_escape_string($_POST['titre']);
    $description = $conn->real_escape_string($_POST['description']);
    $lieu = $conn->real_escape_string($_POST['lieu']);
    $ville = $conn->real_escape_string($_POST['ville']);

    $sql = "INSERT INTO activites (titre, description, lieu, ville) VALUES ('$titre', '$description', '$lieu', '$ville')";
    if ($conn->query($sql)) {
        $message = "✅ Activité ajoutée avec succès.";
    } else {
        $message = "❌ Erreur : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une activité</title>
</head>
<body>
    <h2>Ajouter une nouvelle activité</h2>
    <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>
    <form method="POST">
        <label>Titre :</label><br>
        <input type="text" name="titre" required><br><br>

        <label>Description :</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Lieu :</label><br>
        <input type="text" name="lieu" required><br><br>

        <label>Ville :</label><br>
        <input type="text" name="ville" required><br><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
