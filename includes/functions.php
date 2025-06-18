<?php
// Fonction pour nettoyer les entrées utilisateur
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour rediriger avec un message
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION[$type . '_message'] = $message;
    header('Location: ' . $url);
    exit();
}

// Fonction pour afficher les messages
function displayMessage() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
}

// Fonction pour vérifier si l'email est valide
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fonction pour vérifier si le numéro de téléphone est valide
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
} 