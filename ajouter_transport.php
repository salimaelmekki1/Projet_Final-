<?php
session_start();
include 'config/config.php';

$message = '';
$stades = $conn->query("SELECT id, nom FROM stades");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_transport = $conn->real_escape_string($_POST['nom_transport']);
    $description = $conn->real_escape_string($_POST['description']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $stade_id = (int) $_POST['stade_id'];

    $sql = "INSERT INTO transports (nom_transport, description, contact, stade_id) 
            VALUES ('$nom_transport', '$description', '$contact', $stade_id)";
    if ($conn->query($sql)) {
        $message = "✅ Transport ajouté avec succès.";
    } else {
        $message = "❌ Erreur : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un transport</title>
</head>
<body>
    <h2>Ajouter un nouveau transport</h2>
    <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>
    <form method="POST">
        <label>Nom du transport :</label><br>
        <input type="text" name="nom_transport" required><br><br>

        <label>Description :</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Contact :</label><br>
        <input type="text" name="contact" required><br><br>

        <label>Stade associé :</label><br>
        <select name="stade_id" required>
            <option value="">-- Choisir un stade --</option>
            <?php while ($s = $stades->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
