<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="form-container">
        <h2>Connexion Admin</h2>
        <form method="post">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=match_reservation', 'root', '');
                $email = $_POST['email'];
                $mot_de_passe = $_POST['mot_de_passe'];

                $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin) {
                    if (password_verify($mot_de_passe, $admin['mot_de_passe'])) {
                        $_SESSION['user_id'] = $admin['id'];
                        $_SESSION['nom'] = $admin['nom'];
                        $_SESSION['email'] = $admin['email'];
                        $_SESSION['is_admin'] = true;

                        header("Location: admin_profile.php");
                        exit;
                    } else {
                        echo "<div class='message'>Mot de passe incorrect</div>";
                    }
                } else {
                    echo "<div class='message'>Email non trouvé</div>";
                }
            } catch (PDOException $e) {
                echo "<div class='message'>Erreur de base de données</div>";
            }
        }
        ?>
    </div>
</body>
</html>
