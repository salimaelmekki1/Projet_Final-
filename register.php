<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base
include 'config/config.php';

// Initialisation des variables
$erreur = '';
$succes = '';

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'];

    // Vérification des champs vides
    if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($confirmer_mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } elseif ($mot_de_passe !== $confirmer_mot_de_passe) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $verif = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $verif->bind_param("s", $email);
        $verif->execute();
        $verif->store_result();

        if ($verif->num_rows > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion dans la base
            $insert = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $nom, $email, $hashed_password);

            if ($insert->execute()) {
                $succes = "Inscription réussie. Vous pouvez vous connecter.";
            } else {
                $erreur = "Erreur lors de l'inscription.";
            }
        }

        $verif->close();
        $insert->close();
    }
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="form-container">
        <h2>Inscription</h2>
        <?php if ($erreur): ?>
            <div class="message"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <?php if ($succes): ?>
            <div class="message" style="background-color: #d4edda; color: #155724;"><?php echo $succes; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div>
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <div>
                <label for="confirmer_mot_de_passe">Confirmer le mot de passe</label>
                <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
            </div>
            <button type="submit">S'inscrire</button>
            <a href="login.php">Vous avez déjà un compte ? Connectez-vous</a>
        </form>
    </div>
</body>
</html>
