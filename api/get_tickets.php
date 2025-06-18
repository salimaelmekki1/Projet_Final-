<?php
// Affichage des erreurs pour debug (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Réponse JSON
header('Content-Type: application/json');

// Inclusion config DB
include '../config/config.php';

// Vérifier connexion
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    exit();
}

// Vérifier si un match_id est fourni
$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if (!$match_id) {
    http_response_code(400);
    echo json_encode(["error" => "ID du match manquant"]);
    exit();
}

// Requête SQL pour récupérer les tickets du match
$sql = "SELECT id, type, prix_base, entree FROM tickets WHERE match_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur SQL : " . $conn->error]);
    exit();
}

// Récupérer les résultats dans un tableau
$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = [
        "id" => (int)$row["id"],
        "type" => $row["type"],
        "prix_base" => (float)$row["prix_base"],
        "entree" => $row["entree"]
    ];
}

// Envoi des données JSON
echo json_encode($tickets);
?> 