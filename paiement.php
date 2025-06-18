<?php
session_start();
require_once 'includes/auth_check.php';
requireLogin();

include 'config/config.php';
include 'config/paypal_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
$quantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];

if (!$match_id || empty($quantities)) {
    die("Données invalides.");
}

// Récupération des informations du match
$stmt = $conn->prepare("
    SELECT m.*, e1.nom as equipe1, e2.nom as equipe2 
    FROM matchs m 
    LEFT JOIN equipes e1 ON m.equipe1_id = e1.id 
    LEFT JOIN equipes e2 ON m.equipe2_id = e2.id 
    WHERE m.id = ?
");
$stmt->bind_param("i", $match_id);
$stmt->execute();
$match = $stmt->get_result()->fetch_assoc();

if (!$match) {
    die("Match introuvable.");
}

// Récupération des tickets sélectionnés
$tickets = [];
$prix_total = 0;

foreach ($quantities as $ticket_id => $quantite) {
    if ($quantite > 0) {
        $stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ? AND match_id = ?");
        $stmt->bind_param("ii", $ticket_id, $match_id);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        
        if ($ticket) {
            $tickets[] = [
                'id' => $ticket_id,
                'type' => $ticket['type'],
                'prix_base' => $ticket['prix_base'],
                'quantite' => $quantite,
                'total' => $ticket['prix_base'] * $quantite
            ];
            $prix_total += $ticket['prix_base'] * $quantite;
        }
    }
}

if (empty($tickets)) {
    die("Aucun ticket sélectionné.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/paiement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=EUR"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="payment-container">
        <div class="payment-summary">
            <h1>Récapitulatif de votre commande</h1>
            
            <div class="match-details">
                <h2>Détails du match</h2>
                <p><strong>Match :</strong> <?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($match['date_match']) ?> à <?= htmlspecialchars($match['heure_match']) ?></p>
                <p><strong>Lieu :</strong> <?= htmlspecialchars($match['lieu']) ?></p>
            </div>

            <div class="ticket-details">
                <h2>Détails des tickets</h2>
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-item">
                        <p><strong>Type de ticket :</strong> <?= htmlspecialchars($ticket['type']) ?></p>
                        <p><strong>Quantité :</strong> <?= $ticket['quantite'] ?></p>
                        <p><strong>Prix unitaire :</strong> <?= number_format($ticket['prix_base'], 2) ?> €</p>
                        <p><strong>Sous-total :</strong> <?= number_format($ticket['total'], 2) ?> €</p>
                    </div>
                <?php endforeach; ?>
                <p class="total-price"><strong>Prix total :</strong> <?= number_format($prix_total, 2) ?> €</p>
            </div>

            <div class="payment-method">
                <h2>Méthode de paiement</h2>
                <div class="payment-info">
                    <div class="payment-logo">
                        <img src="images/paypal-logo.png" alt="PayPal" class="paypal-logo">
                    </div>
                    <div class="payment-description">
                        <h3>Paiement sécurisé via PayPal</h3>
                        <p>PayPal est une méthode de paiement rapide et sécurisée. Vous pouvez payer avec votre compte PayPal ou par carte bancaire.</p>
                        <ul class="payment-benefits">
                            <li>Paiement 100% sécurisé</li>
                            <li>Protection de l'acheteur</li>
                            <li>Confirmation immédiate</li>
                            <li>Reçu envoyé par email</li>
                        </ul>
                    </div>
                </div>

                <div class="payment-steps">
                    <h3>Comment payer :</h3>
                    <ol>
                        <li>Cliquez sur le bouton "Payer avec PayPal" ci-dessous</li>
                        <li>Connectez-vous à votre compte PayPal ou payez par carte bancaire</li>
                        <li>Confirmez votre paiement</li>
                        <li>Vous serez redirigé vers la page de confirmation</li>
                    </ol>
                </div>

                <div class="paypal-button-container">
                    <div id="paypal-button-container"></div>
                </div>

                <div class="payment-security">
                    <p class="security-note">
                        <i class="fas fa-lock"></i> 
                        Votre paiement est sécurisé par PayPal. Aucune information de carte bancaire n'est stockée sur nos serveurs.
                    </p>
                </div>
            </div>
        <div class="confirmation-container">

        </div>
            <div class="receipt-options">
                <h2>Options de reçu</h2>
                <p>Après votre paiement, vous pourrez :</p>
                <div class="receipt-buttons">
                    <button class="btn btn-primary" onclick="downloadReceipt()">
                        <i class="fas fa-download"></i> Télécharger le reçu
                    </button>
                    <button class="btn btn-secondary" onclick="sendReceiptByEmail()">
                        <i class="fas fa-envelope"></i> Envoyer par email
                    </button>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Configuration PayPal
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= $prix_total ?>'
                        },
                        description: 'Tickets pour <?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
        const ticketsData = <?= json_encode($tickets) ?>.map(ticket => ({
            ticket_id: parseInt(ticket.id),
            quantite: parseInt(ticket.quantite),
            prix: parseFloat(ticket.prix_base)
        }));

        fetch('process_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: details.id,
                match_id: <?= $match_id ?>,
                tickets: ticketsData,
                prix_total: <?= $prix_total ?>,
                paypal_details: details
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Stocker reservationId globalement
                window.reservationId = data.reservation_ids[0];

                // Charger la confirmation dans le div confirmation-container
                fetch('confirmation_content.php?id=' + window.reservationId)
                    .then(resp => {
                        if (!resp.ok) {
                            throw new Error('Erreur lors du chargement de la confirmation');
                        }
                        return resp.text();
                    })
                    .then(html => {
                        document.querySelector('.confirmation-container').innerHTML = html;

                        // Optionnel : masquer la partie paiement après confirmation
                        document.querySelector('.payment-method').style.display = 'none';
                        document.querySelector('.paypal-button-container').style.display = 'none';
                        document.querySelector('.payment-steps').style.display = 'none';

                        // Afficher un message ou scroll vers la confirmation
                        window.scrollTo({ top: document.querySelector('.confirmation-container').offsetTop, behavior: 'smooth' });
                    })
                    .catch(err => {
                        alert('Erreur lors de l\'affichage de la confirmation : ' + err.message);
                    });
            } else {
                alert('Erreur lors du traitement du paiement : ' + (data.error || 'Une erreur inconnue est survenue'));
            }
        })
        .catch(error => {
            alert('Une erreur est survenue lors du traitement du paiement. Détails : ' + error.message);
        });
    });
},

            onError: function(err) {
                console.error('Erreur PayPal:', err);
                alert('Une erreur est survenue avec PayPal. Veuillez réessayer.');
            }
        }).render('#paypal-button-container');

        // Fonctions pour le reçu
        function downloadReceipt() {
            if (!window.reservationId) {
                alert('Veuillez d\'abord effectuer le paiement.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'download');
            formData.append('reservation_id', window.reservationId);

            fetch('receipt_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Erreur lors du téléchargement');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'recu_reservation.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
            })
            .catch(error => {
                alert('Erreur lors du téléchargement du reçu : ' + error.message);
            });
        }

        function sendReceiptByEmail() {
            if (!window.reservationId) {
                alert('Veuillez d\'abord effectuer le paiement.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'email');
            formData.append('reservation_id', window.reservationId);

            fetch('receipt_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Réponse du serveur:', text);
                        throw new Error('Réponse invalide du serveur');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    alert('Le reçu a été envoyé à votre adresse email.');
                } else {
                    alert('Erreur lors de l\'envoi du reçu : ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi du reçu : ' + error.message);
            });
        }
    </script>
</body>
</html> 