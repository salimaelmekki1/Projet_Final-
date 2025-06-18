<?php
// Affichage des erreurs pour debug (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Réponse JSON
header('Content-Type: application/json');

// Inclusion config DB (chemin à adapter selon ta structure)
include '../config/config.php';

// Vérifier connexion
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    exit();
}

// Requête SQL pour récupérer tous les matchs triés par date
$sql = "SELECT m.id, e1.nom as equipe1, e2.nom as equipe2, m.date_match, m.heure_match, m.lieu 
        FROM matchs m 
        LEFT JOIN equipes e1 ON m.equipe1_id = e1.id 
        LEFT JOIN equipes e2 ON m.equipe2_id = e2.id 
        WHERE m.equipe1_id IS NOT NULL AND m.equipe2_id IS NOT NULL
        ORDER BY m.date_match ASC, m.heure_match ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur SQL : " . $conn->error]);
    exit();
}

// Récupérer les résultats dans un tableau
$matchs = [];
while ($row = $result->fetch_assoc()) {
    // Formatage possible de la date/heure si besoin (exemple ici : date en yyyy-mm-dd, heure en hh:mm:ss)
    $matchs[] = [
        "id" => (int)$row["id"],
        "equipe1" => $row["equipe1"],
        "equipe2" => $row["equipe2"],
        "date_match" => $row["date_match"],
        "heure_match" => $row["heure_match"],
        "lieu" => $row["lieu"],
    ];
}

// Envoi des données JSON
echo json_encode($matchs);
?>
