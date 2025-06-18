<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['nom']);
}

// Fonction pour vérifier si l'utilisateur est un administrateur
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Fonction pour obtenir le nom de l'utilisateur
function getUserName() {
    return isset($_SESSION['nom']) ? $_SESSION['nom'] : '';
}

// Fonction pour obtenir l'ID de l'utilisateur
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Fonction pour obtenir l'email de l'utilisateur
function getUserEmail() {
    return isset($_SESSION['email']) ? $_SESSION['email'] : '';
}

// Fonction pour rediriger vers la page de connexion si non authentifié
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Fonction pour rediriger vers la page de connexion si non administrateur
function requireAdmin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: login.php");
        exit();
    }
}
?> 