<?php
$pdo = new PDO('mysql:host=localhost;dbname=match_reservation', 'root', '');
$mot_de_passe = password_hash("admin123", PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO admin (nom, email, mot_de_passe) VALUES (?, ?, ?)")
    ->execute(["Admin", "admin@site.com", $mot_de_passe]);
echo "Admin créé.";
?>
