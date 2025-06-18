<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();     // Supprime toutes les variables de session
session_destroy();   // Détruit la session

// Rediriger vers la page d'accueil
header("Location: index.php");
exit;
