<?php
include 'config/config.php';
if (!isset($_GET['stade_id'])) exit;

$stade_id = (int) $_GET['stade_id'];
$result = $conn->query("SELECT nom_transport, description, contact FROM transports WHERE stade_id = $stade_id");

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><strong>" . htmlspecialchars($row['nom_transport']) . "</strong><br><em>" . htmlspecialchars($row['description']) . "</em><br>Contact: " . htmlspecialchars($row['contact']) . "</li><br>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucun transport trouvé pour ce stade.</p>";
}
?>
