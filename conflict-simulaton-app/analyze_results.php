<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <audio controls autoplay>
  <source src="music.mp3" type="audio/mp3">
  Twoja przeglądarka nie obsługuje elementu audio.
</audio>
    <title>Analiza Wyników</title>
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
    <main>
    <?php
            include 'db.php';

            // Pobierz dostępne scenariusze
            $sql_scenarios = "SELECT id FROM results";
            $result_scenarios = $conn->query($sql_scenarios);

            if ($result_scenarios->num_rows > 0) {
                // Wyświetl każdy scenariusz w tabeli
                while ($row = $result_scenarios->fetch_assoc()) {
                    echo "<tr>";
                    echo "<br><td> " . $row["id"] . " </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Brak dostępnych scenariuszy.</td></tr>";
            }
            ?>
        <h2>Wyniki Bitew</h2>
        <form id="resultsForm" action="view_results.php" method="GET">
            <label for="results_id">ID raportu:</label>
            <input type="number" id="results_id" name="results_id" required>
            <button type="submit">Pokaż Wyniki</button>
        </form>
    </main>
</body>
</html>
