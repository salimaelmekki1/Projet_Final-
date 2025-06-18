<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'ticketing';
$username = 'root';
$password = '';

// Création de la connexion
$conn = new mysqli($host, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Définir le jeu de caractères
$conn->set_charset("utf8"); 