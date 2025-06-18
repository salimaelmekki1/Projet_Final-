<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=match_reservation', 'root', '');
    
    // Le mot de passe actuel
    $mot_de_passe = "admin&é\"'(-è_çà";
    
    // Création du hash sécurisé
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    
    // Mise à jour du mot de passe dans la base de données
    $stmt = $pdo->prepare("UPDATE admin SET mot_de_passe = ? WHERE email = ?");
    $stmt->execute([$hash, 'admin@site.com']);
    
    echo "Le mot de passe a été mis à jour avec succès!";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?> 