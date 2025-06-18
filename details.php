<?php
session_start();
include 'config/config.php';

// Récupérer les matchs à venir
$today = date("Y-m-d");
$matchs = $conn->query("
    SELECT 
        m.date_match, m.heure_match, 
        e1.nom AS equipe1, 
        e2.nom AS equipe2, 
        s.nom AS stade, 
        s.ville
    FROM matchs m
    JOIN equipes e1 ON m.equipe1_id = e1.id
    JOIN equipes e2 ON m.equipe2_id = e2.id
    JOIN stades s ON m.stade_id = s.id
    WHERE m.date_match >= '$today'
    ORDER BY m.date_match, m.heure_match
");

// Récupérer les groupes
$groupes = $conn->query("SELECT DISTINCT groupe FROM equipes WHERE groupe IS NOT NULL AND groupe <> '' ORDER BY groupe");

// Récupérer les stades
$stades = $conn->query("SELECT * FROM stades");
// Récupérer les lieux distincts pour les activités
$lieux = $conn->query("SELECT DISTINCT lieu FROM activites ORDER BY lieu");

// Récupérer les stades pour filtrage des transports
$stades_transports = $conn->query("SELECT id, nom FROM stades");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails des Matchs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/image.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            color: white;
        }

        nav {
            background: rgb(3, 73, 148);
            display: flex;
            justify-content: center;
            padding: 10px 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        nav button {
            background: transparent;
            border: none;
            color: white;
            padding: 14px 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        nav button.active, nav button:hover {
            background: #0056b3;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: rgba(0, 0, 0, 0.7);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffc107;
            text-shadow: 1px 1px 3px black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #999;
            text-align: center;
        }

        th {
            background: #007BFF;
            color: white;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        ul li {
            background: rgba(255,255,255,0.2);
            padding: 10px 15px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            box-shadow: 0 0 5px rgba(255,255,255,0.2);
        }

        p {
            text-align: center;
            color: #ddd;
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
        .accueil { background-color:rgb(226, 205, 112); }
     
        .details { background-color:rgb(42, 120, 208); }
    </style>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('nav button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            document.getElementById(tabId + '-btn').classList.add('active');
        }

        window.onload = function () {
            showTab('matchs');
        };

        
function fetchActivites() {
    const lieu = document.getElementById("lieu-select").value;
    const container = document.getElementById("activites-content");
    if (!lieu) {
        container.innerHTML = "";
        return;
    }

    fetch(`get_activites.php?lieu=${encodeURIComponent(lieu)}`)
        .then(res => res.text())
        .then(data => {
            container.innerHTML = data;
        });
}

function fetchTransports() {
    const stade = document.getElementById("stade-select").value;
    const container = document.getElementById("transports-content");
    if (!stade) {
        container.innerHTML = "";
        return;
    }

    fetch(`get_transports.php?stade_id=${encodeURIComponent(stade)}`)
        .then(res => res.text())
        .then(data => {
            container.innerHTML = data;
        });
}


    </script>
</head>
<body>

<nav>
    <button id="matchs-btn" onclick="showTab('matchs')"> Matchs à venir</button>
    <button id="groupes-btn" onclick="showTab('groupes')"> Groupes</button>
    <button id="stades-btn" onclick="showTab('stades')"> Stades</button>
    <button id="activites-btn" onclick="showTab('activites')"> Activités</button>
    <button id="transports-btn" onclick="showTab('transports')"> Transports</button>

</nav>

<div class="container">

    <!-- Matchs -->
    <div id="matchs" class="tab-content">
        <h2>Matchs à venir</h2>
        <?php if ($matchs->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Équipe 1</th>
                    <th>Équipe 2</th>
                    <th>Stade</th>
                    <th>Ville</th>
                </tr>
                <?php while ($m = $matchs->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['date_match']) ?></td>
                        <td><?= htmlspecialchars($m['heure_match']) ?></td>
                        <td><?= htmlspecialchars($m['equipe1']) ?></td>
                        <td><?= htmlspecialchars($m['equipe2']) ?></td>
                        <td><?= htmlspecialchars($m['stade']) ?></td>
                        <td><?= htmlspecialchars($m['ville']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Aucun match à venir.</p>
        <?php endif; ?>
    </div>

    <!-- Groupes -->
    <div id="groupes" class="tab-content">
        <h2>Groupes existants</h2>
        <?php if ($groupes->num_rows > 0): ?>
            <ul>
                <?php while ($g = $groupes->fetch_assoc()): ?>
                    <li>Groupe <?= htmlspecialchars($g['groupe']) ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Aucun groupe défini.</p>
        <?php endif; ?>
    </div>

    <!-- Stades -->
    <div id="stades" class="tab-content">
        <h2>Stades disponibles</h2>
        <?php if ($stades->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Ville</th>
                    <th>Capacité</th>
                </tr>
                <?php while ($s = $stades->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nom']) ?></td>
                        <td><?= htmlspecialchars($s['ville']) ?></td>
                        <td><?= htmlspecialchars($s['capacite']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Aucun stade enregistré.</p>
        <?php endif; ?>
    </div>
    <div class="button-group">
        <a href="index.php"><button type="button" class="accueil">Accueil</button></a>
       
        <a href="calendrier1.php"><button type="button" class="details">Voir Calendrier</button></a>
    </div>

    <!-- Activités -->
<div id="activites" class="tab-content">
    <h2>Activités par pays</h2>
    <label for="lieu-select" style="color:white;">Choisissez un pays :</label>
    <select id="lieu-select" onchange="fetchActivites()" style="margin: 10px;">
        <option value="">-- Sélectionnez --</option>
        <?php while ($l = $lieux->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($l['lieu']) ?>"><?= htmlspecialchars($l['lieu']) ?></option>
        <?php endwhile; ?>
    </select>
    <div id="activites-content" style="margin-top: 20px;"></div>
</div>

 <div id="transports" class="tab-content">
    <h2>Transports par Stade</h2>
    <label for="stade-select" style="color:white;">Choisissez un stade :</label>
    <select id="stade-select" onchange="fetchTransports()" style="margin: 10px;">
        <option value="">-- Sélectionnez --</option>
        <?php while ($st = $stades_transports->fetch_assoc()): ?>
            <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['nom']) ?></option>
        <?php endwhile; ?>
    </select>
    <div id="transports-content"></div>
</div>


</div>

</body>
</html>
