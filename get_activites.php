<?php
require_once 'config/config.php';

if (!isset($_GET['lieu'])) {
    echo "<p style='color:white;'>Aucun pays sélectionné.</p>";
    exit;
}

$lieu = $_GET['lieu'];
$stmt = $conn->prepare("SELECT ville, titre, description FROM activites WHERE lieu = ? ORDER BY ville, titre");
$stmt->bind_param("s", $lieu);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $ville = $row['ville'];
    if (!isset($data[$ville])) {
        $data[$ville] = [];
    }
    $data[$ville][] = [
        'titre' => $row['titre'],
        'description' => $row['description']
    ];
}

if (empty($data)) {
    echo "<p style='color:white;'>Aucune activité trouvée pour ce pays.</p>";
    exit;
}

echo "<table style='width:100%; border-collapse: collapse; color: white;'>";
echo "<tr style='background-color: #007BFF; color: white;'>
        <th style='padding: 10px; border: 1px solid #ccc;'>Ville</th>
        <th style='padding: 10px; border: 1px solid #ccc;'>Activité</th>
        <th style='padding: 10px; border: 1px solid #ccc;'>Description</th>
      </tr>";

foreach ($data as $ville => $activites) {
    $rowspan = count($activites);
    foreach ($activites as $index => $act) {
        echo "<tr>";
        if ($index === 0) {
            echo "<td rowspan='$rowspan' style='border: 1px solid #ccc; padding: 10px;'>$ville</td>";
        }
        echo "<td style='border: 1px solid #ccc; padding: 10px;'>{$act['titre']}</td>
              <td style='border: 1px solid #ccc; padding: 10px;'>{$act['description']}</td>";
        echo "</tr>";
    }
}
echo "</table>";
