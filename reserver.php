<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/config.php';

$match_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupération des infos du match
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

// Récupération des tickets pour ce match
$stmt = $conn->prepare("SELECT * FROM tickets WHERE match_id = ?");
$stmt->bind_param("i", $match_id);
$stmt->execute();
$tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Récupération activités et transports (limité à 5)
$activites = [];
$res_act = $conn->query("SELECT * FROM activites LIMIT 5");
while ($act = $res_act->fetch_assoc()) {
    $activites[] = $act;
}

$transports = [];
$res_trans = $conn->query("SELECT * FROM transports LIMIT 5");
while ($trans = $res_trans->fetch_assoc()) {
    $transports[] = $trans;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver - <?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/reserver.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AXclZqGenXlgul_HMXX3-qT1g1ZaI_d0sdukqWRT2RrV0IO5ZYu6weph4yaNkWUBfpaM_zOWqyb6SghM"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="reservation-container">
        <div class="reservation-header">
            <h1>Réserver vos places</h1>
            <div class="match-info">
                <h2><?= htmlspecialchars($match['equipe1']) ?> vs <?= htmlspecialchars($match['equipe2']) ?></h2>
                <div class="match-details">
                    <p><i class="fas fa-calendar"></i> <?= htmlspecialchars($match['date_match']) ?></p>
                    <p><i class="fas fa-clock"></i> <?= htmlspecialchars($match['heure_match']) ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($match['lieu']) ?></p>
                </div>
            </div>
        </div>

        <div class="reservation-content">
            <div class="tickets-section">
                <h3>Types de billets disponibles</h3>
                <div class="tickets-grid">
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <h4><?= htmlspecialchars($ticket['type']) ?></h4>
                                <span class="ticket-price"><?= number_format($ticket['prix_base'], 2) ?> €</span>
                            </div>
                            <div class="ticket-details">
                                <p><i class="fas fa-ticket-alt"></i> Entrée <?= htmlspecialchars($ticket['entree']) ?></p>
                                <p><i class="fas fa-chair"></i> Place <?= htmlspecialchars($ticket['numero_place']) ?></p>
                            </div>
                            <div class="ticket-quantity">
                                <label for="quantity_<?= $ticket['id'] ?>">Quantité :</label>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn" onclick="decrementQuantity(<?= $ticket['id'] ?>)">-</button>
                                    <input type="number" id="quantity_<?= $ticket['id'] ?>" name="quantities[<?= $ticket['id'] ?>]" 
                                           min="0" max="10" value="0" onchange="updateTotal()">
                                    <button type="button" class="quantity-btn" onclick="incrementQuantity(<?= $ticket['id'] ?>)">+</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="reservation-summary">
                <h3>Récapitulatif de la réservation</h3>
                <div class="summary-content">
                    <div id="selected-tickets">
                        <p class="no-tickets">Aucun billet sélectionné</p>
                    </div>
                    <div class="total-section">
                        <div class="total-row">
                            <span>Sous-total :</span>
                            <span id="subtotal">0.00 €</span>
                        </div>
                        <div class="total-row">
                            <span>Frais de service :</span>
                            <span id="fees">0.00 €</span>
                        </div>
                        <div class="total-row total">
                            <span>Total :</span>
                            <span id="total">0.00 €</span>
                        </div>
                    </div>
                    <form action="paiement.php" method="post" id="reservation-form">
                        <input type="hidden" name="match_id" value="<?= $match_id ?>">
                        <?php foreach ($tickets as $ticket): ?>
                            <input type="hidden" name="quantities[<?= $ticket['id'] ?>]" id="hidden_quantity_<?= $ticket['id'] ?>" value="0">
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary" id="reserve-btn" disabled>
                            <i class="fas fa-shopping-cart"></i> Procéder au paiement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Définir les fonctions avant leur utilisation
        function incrementQuantity(ticketId) {
            const input = document.getElementById('quantity_' + ticketId);
            const currentValue = parseInt(input.value) || 0;
            if (currentValue < 10) {
                input.value = currentValue + 1;
                updateTotal();
            }
        }

        function decrementQuantity(ticketId) {
            const input = document.getElementById('quantity_' + ticketId);
            const currentValue = parseInt(input.value) || 0;
            if (currentValue > 0) {
                input.value = currentValue - 1;
                updateTotal();
            }
        }

        function updateTotal() {
            let subtotal = 0;
            let hasTickets = false;
            const selectedTickets = document.getElementById('selected-tickets');
            selectedTickets.innerHTML = '';

            // Récupérer tous les tickets disponibles
            const tickets = [
                <?php foreach ($tickets as $ticket): ?>
                {
                    id: <?= $ticket['id'] ?>,
                    type: "<?= htmlspecialchars($ticket['type']) ?>",
                    price: <?= $ticket['prix_base'] ?>
                },
                <?php endforeach; ?>
            ];

            // Mettre à jour le total pour chaque ticket
            tickets.forEach(ticket => {
                const quantityInput = document.getElementById('quantity_' + ticket.id);
                const quantity = parseInt(quantityInput.value) || 0;
                
                // Mettre à jour la valeur cachée
                document.getElementById('hidden_quantity_' + ticket.id).value = quantity;
                
                if (quantity > 0) {
                    hasTickets = true;
                    const ticketTotal = quantity * ticket.price;
                    subtotal += ticketTotal;

                    const ticketElement = document.createElement('div');
                    ticketElement.className = 'selected-ticket';
                    ticketElement.innerHTML = `
                        <span>${quantity}x ${ticket.type}</span>
                        <span>${ticketTotal.toFixed(2)} €</span>
                    `;
                    selectedTickets.appendChild(ticketElement);
                }
            });

            if (!hasTickets) {
                selectedTickets.innerHTML = '<p class="no-tickets">Aucun billet sélectionné</p>';
            }

            const fees = subtotal * 0.05; // 5% de frais de service
            const total = subtotal + fees;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
            document.getElementById('fees').textContent = fees.toFixed(2) + ' €';
            document.getElementById('total').textContent = total.toFixed(2) + ' €';
            document.getElementById('reserve-btn').disabled = !hasTickets;
        }

        // Initialiser le total une fois que le DOM est chargé
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
</body>
</html>
