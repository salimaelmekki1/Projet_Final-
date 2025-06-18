<?php
session_start();
require_once 'includes/auth_check.php';
requireAdmin();

include 'config/config.php';

$success = '';
$error = '';

// Traitement du formulaire de réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $reponse = trim($_POST['reponse'] ?? '');

    if ($id && !empty($reponse)) {
        $stmt = $conn->prepare("UPDATE messages SET reponse = ?, date_reponse = NOW() WHERE id = ?");
        if ($stmt->execute([$reponse, $id])) {
            $success = "Réponse envoyée avec succès.";
        } else {
            $error = "Erreur lors de l'envoi de la réponse.";
        }
    } else {
        $error = "La réponse ne peut pas être vide.";
    }
}

// Récupération des messages après mise à jour éventuelle
$messages = $conn->query("
    SELECT m.*, u.nom as utilisateur_nom, u.email as utilisateur_email, u.telephone as utilisateur_telephone 
    FROM messages m 
    LEFT JOIN utilisateurs u ON m.utilisateur_id = u.id 
    ORDER BY m.date_envoi DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Messages - Administration</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_messages.css">
    <style>
        .messages-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .messages-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .messages-table th,
        .messages-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .messages-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .messages-table tr:hover {
            background: #f8f9fa;
        }

        .message-content {
            max-width: 300px;
            white-space: pre-wrap;
        }

        .response-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .response-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 80px;
        }

        .response-form textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .response-form button {
            align-self: flex-end;
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .response-form button:hover {
            background: #0056b3;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="messages-container">
        <a href="admin_profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au profil administrateur
        </a>

        <h1>Gestion des Messages</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <table class="messages-table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Contact</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Réponse</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['utilisateur_nom']) ?></td>
                        <td>
                            <div>Email: <?= htmlspecialchars($msg['utilisateur_email']) ?></div>
                            <div>Tél: <?= htmlspecialchars($msg['utilisateur_telephone']) ?></div>
                        </td>
                        <td class="message-content"><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?></td>
                        <td class="message-content">
                            <?= $msg['reponse'] ? nl2br(htmlspecialchars($msg['reponse'])) : '<em>En attente</em>' ?>
                        </td>
                        <td>
                            <?php if (empty($msg['reponse'])): ?>
                                <form method="post" class="response-form">
                                    <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                    <textarea name="reponse" placeholder="Écrire une réponse..." required></textarea>
                                    <button type="submit">Envoyer la réponse</button>
                                </form>
                            <?php else: ?>
                                <em>Répondu le <?= date('d/m/Y H:i', strtotime($msg['date_reponse'])) ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
