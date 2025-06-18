<?php
$host = 'localhost';         // L'adresse du serveur
$user = 'root';              // L'utilisateur MySQL (par défaut : root)
$pass = '';                  // Le mot de passe (vide si tu n'en as pas mis)
$dbname = 'match_reservation';  // Le nom de ta base

// Connexion
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification
if ($conn->connect_error) {
    error_log("Erreur de connexion à la base de données : " . $conn->connect_error);
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

// Définir le charset
if (!$conn->set_charset("utf8mb4")) {
    error_log("Erreur lors du chargement du charset utf8mb4 : " . $conn->error);
}

// Connexion réussie (optionnel : pour test)
// echo "Connexion réussie !";

// Définition du chemin de base
define('BASE_PATH', '/DevWeb_Project');

// Fonction pour obtenir le chemin correct des assets
function getAssetPath($path) {
    return BASE_PATH . '/' . ltrim($path, '/');
}
?>