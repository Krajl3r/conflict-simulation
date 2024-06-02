<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
<header>
        <h1>Analiza Wyników Bitew</h1>
        <nav>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="create_scenario.php">Tworzenie Scenariusza</a></li>
                <li><a href="manage_units.php">Zarządzanie Jednostkami</a></li>
                <li><a href="simulate_conflict.php">Symulacja Bitwy</a></li>
            </ul>
        </nav>
    </header>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $conflict_id = $_GET['results_id'];

    // Pobierz wyniki dla wybranego konfliktu
    $sql_results = "SELECT * FROM results WHERE id = ?";
    $stmt_results = $conn->prepare($sql_results);
    $stmt_results->bind_param("i", $conflict_id);
    $stmt_results->execute();
    $result_results = $stmt_results->get_result();

    if ($result_results->num_rows > 0) {
        $row = $result_results->fetch_assoc();
        echo "<h2>Raport z Bitwy</h2>";
        echo "<table>";
        echo "<tr><th>Polegli</th><td>" . $row['casualties'] . "</td></tr>";
        echo "<tr><th>Ranni</th><td>" . $row['wounded'] . "</td></tr>";
        echo "<tr><th>Jeńcy</th><td>" . $row['prisoners'] . "</td></tr>";
        echo "<tr><th>Ilość straconych sprzętów</th><td>" . $row['material_losses'] . "</td></tr>";
        echo "<tr><th>Raport bitwy</th><td>" . nl2br($row['battle_log']) . "</td></tr>";
        echo "<tr><th>Długość bitwy</th><td>" . $row['battle_duration'] . " seconds</td></tr>";
        echo "<tr><th>Zwycięstwo</th><td>" . $row['winner'] . "</td></tr>";
        echo "</table>";
    } else {
        echo "Brak wyników dla wybranego konfliktu.";
    }
    $stmt_results->close();
}

$conn->close();
?>

</body>
</html>