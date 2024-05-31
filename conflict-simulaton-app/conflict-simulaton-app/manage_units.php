<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Zarządzanie Jednostkami</title>
    <audio controls autoplay>
  <source src="music.mp3" type="audio/mp3">
  Twoja przeglądarka nie obsługuje elementu audio.
</audio>
</head>
</head>
<body>

    <header>
        <h1>Zarządzanie Jednostkami</h1>
        <nav>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="create_scenario.php">Tworzenie Scenariusza</a></li>
                <li><a href="simulate_conflict.php">Symulacja Konfliktu</a></li>
                <li><a href="analyze_results.php">Analiza Wyników</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <?php
            include 'db.php';

            // Pobierz dostępne scenariusze
            $sql_scenarios = "SELECT id, name FROM scenarios";
            $result_scenarios = $conn->query($sql_scenarios);

            if ($result_scenarios->num_rows > 0) {
                // Wyświetl każdy scenariusz w tabeli
                echo "<p>Scenariusze ID:</p>";
                while ($row = $result_scenarios->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><br><p> " . $row["id"] . "|</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Brak dostępnych scenariuszy.</td></tr>";
            }
            ?>
        <h2>Dodaj Jednostkę</h2>
        <form id="unitForm" action="manage_units_action.php" method="POST">
            <input type="hidden" name="action" value="add">
            <label for="unit_name">Nazwa Jednostki:</label>
            <input type="text" id="unit_name" name="unit_name" required>
            
            <label for="unit_type">Rodzaj Jednostki:</label>
            <input type="text" id="unit_type" name="unit_type" required>

            <label for="readiness">Stan Gotowości:</label>
            <input type="text" id="readiness" name="readiness" required>

            <label for="equipment">Wyposażenie:</label>
            <textarea id="equipment" name="equipment" required></textarea>

            <label for="personnel">Liczba Personelu:</label>
            <input type="number" id="personnel" name="personnel" required>

            <label for="scenario_id">ID Scenariusza:</label>
            <input type="number" id="scenario_id" name="scenario_id" required>

            <button type="submit">Dodaj Jednostkę</button>
        </form>

        <h2>Edytuj Jednostkę</h2>
        <form id="editUnitForm" action="manage_units_action.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <label for="unit_id">ID Jednostki:</label>
            <input type="number" id="unit_id" name="unit_id" required>
            
            <label for="unit_name">Nazwa Jednostki:</label>
            <input type="text" id="edit_unit_name" name="unit_name" required>
            
            <label for="unit_type">Rodzaj Jednostki:</label>
            <input type="text" id="edit_unit_type" name="unit_type" required>

            <label for="readiness">Stan Gotowości:</label>
            <input type="text" id="edit_readiness" name="readiness" required>

            <label for="equipment">Wyposażenie:</label>
            <textarea id="edit_equipment" name="equipment" required></textarea>

            <label for="personnel">Liczba Personelu:</label>
            <input type="number" id="edit_personnel" name="personnel" required>

            <button type="submit">Edytuj Jednostkę</button>
        </form>

        <h2>Usuń Jednostkę</h2>
        <form id="deleteUnitForm" action="manage_units_action.php" method="POST">
            <input type="hidden" name="action" value="delete">
            <label for="unit_id">ID Jednostki:</label>
            <input type="number" id="delete_unit_id" name="unit_id" required>
            
            <button type="submit">Usuń Jednostkę</button>
        </form>
        <?php
            include 'db.php';

            // Pobierz dostępne scenariusze
            $sql_scenarios = "SELECT id, name FROM units";
            $result_scenarios = $conn->query($sql_scenarios);

            if ($result_scenarios->num_rows > 0) {
                // Wyświetl każdy scenariusz w tabeli
                echo "<p>Jednostki ID:</p>";
                while ($row = $result_scenarios->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><br><p> " . $row["id"] . "|</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Brak dostępnych scenariuszy.</td></tr>";
            }
            ?>
    </main>
</body>
</html>
