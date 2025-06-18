<?php
session_start();
require_once 'config/config.php'; // Ce fichier fournit $conn
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_NUMBER_INT);

    $sql = "UPDATE utilisateurs SET 
                nom = ?, 
                prenom = ?, 
                email = ?, 
                telephone = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $telephone, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Profil mis à jour avec succès !";
        header('Location: profile.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Erreur lors de la préparation de la requête : " . $conn->error;
        header('Location: profile.php');
        exit();
    }
}

// Redirection si l'utilisateur accède à la page sans POST
header('Location: profile.php');
exit();
