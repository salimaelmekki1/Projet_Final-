<?php

include 'config/config.php';

$mois = date('m');
$annee = date('Y');

if (isset($_GET['mois']) && isset($_GET['annee'])) {
    $mois = str_pad($_GET['mois'], 2, "0", STR_PAD_LEFT);
    $annee = $_GET['annee'];
}

$debutMois = "$annee-$mois-01";
$finMois = date("Y-m-t", strtotime($debutMois));

$stmt = $conn->prepare("
    SELECT 
        m.date_match, m.heure_match, 
        e1.nom AS equipe1_nom, 
        e2.nom AS equipe2_nom, 
        s.nom AS stade_nom, 
        s.ville
    FROM matchs m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    JOIN stades s ON m.stade_id = s.id
    WHERE m.date_match BETWEEN ? AND ?
    AND m.equipe1_id IS NOT NULL AND m.equipe2_id IS NOT NULL
    ORDER BY m.date_match, m.heure_match
");
$stmt->bind_param("ss", $debutMois, $finMois);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier des Matchs</title>
    <link rel="stylesheet" href="css/header.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-image: url('images/image.jpg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.92);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        select, button {
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .button-group a button {
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        .accueil { background-color: #6c757d; }
        .reserver { background-color: #28a745; }
        .details { background-color: #17a2b8; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<p style="margin-top: 50px;">.</p>
<div class="container">
    <h2>Calendrier des Matchs</h2>

    <form method="GET">
        <label for="mois">Mois :</label>
        <select name="mois" id="mois">
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $selected = ($m == (int)$mois) ? "selected" : "";
                echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
            }
            ?>
        </select>

        <label for="annee">Année :</label>
        <select name="annee" id="annee">
            <?php
            for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++) {
                $selected = ($y == (int)$annee) ? "selected" : "";
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>

        <button type="submit">Afficher</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Équipe 1</th>
                    <th>Équipe 2</th>
                    <th>Stade</th>
                    <th>Ville</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date_match']) ?></td>
                        <td><?= htmlspecialchars($row['heure_match']) ?></td>
                        <td><?= htmlspecialchars($row['equipe1_nom']) ?></td>
                        <td><?= htmlspecialchars($row['equipe2_nom']) ?></td>
                        <td><?= htmlspecialchars($row['stade_nom']) ?></td>
                        <td><?= htmlspecialchars($row['ville']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Aucun match prévu pour ce mois.</p>
    <?php endif; ?>

    <div class="button-group">
        <a href="index.php"><button type="button" class="accueil">🏠 Accueil</button></a>
        <a href="calendrier.php"><button type="button" class="reserver">📝 Réserver maintenant</button></a>
        <a href="details.php"><button type="button" class="details">🔎 Voir détails</button></a>
    </div>
</div>

</body>
</html>
