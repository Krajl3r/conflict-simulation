<?php
include 'db.php';

// Sprawdź, czy został przesłany identyfikator scenariusza do usunięcia
if(isset($_POST['scenario_id_to_delete'])) {
    // Przechwyć identyfikator scenariusza do usunięcia
    $scenario_id = $_POST['scenario_id_to_delete'];

    // Przygotuj zapytanie SQL do usunięcia jednostek powiązanych z tym scenariuszem
    $sql_units = "DELETE FROM units WHERE scenario_id = ?";
    $stmt_units = $conn->prepare($sql_units);
    $stmt_units->bind_param("i", $scenario_id);
    $stmt_units->execute();

    // Przygotuj zapytanie SQL do usunięcia wyników powiązanych z tym scenariuszem
    $sql_results = "DELETE FROM results WHERE scenario_id = ?";
    $stmt_results = $conn->prepare($sql_results);
    $stmt_results->bind_param("i", $scenario_id);
    $stmt_results->execute();

    // Przygotuj zapytanie SQL do usunięcia konfliktów powiązanych z tym scenariuszem
    $sql_conflicts = "DELETE FROM conflicts WHERE scenario_id = ?";
    $stmt_conflicts = $conn->prepare($sql_conflicts);
    $stmt_conflicts->bind_param("i", $scenario_id);
    $stmt_conflicts->execute();

    // Przygotuj zapytanie SQL do usunięcia scenariusza
    $sql_scenarios = "DELETE FROM scenarios WHERE id = ?";
    $stmt_scenarios = $conn->prepare($sql_scenarios);
    $stmt_scenarios->bind_param("i", $scenario_id);
    $stmt_scenarios->execute();

    echo "Scenariusz został pomyślnie usunięty.";

    // Zamknij zapytania
    $stmt_units->close();
    $stmt_results->close();
    $stmt_conflicts->close();
    $stmt_scenarios->close();
} else {
    echo "Nieprawidłowe dane przesłane z formularza.";
}

// Zamknij połączenie z bazą danych
$conn->close();
?>
<?php
header('Content-Type: text/html; charset=utf-8');

// HTML z przyciskiem powrotu
echo '<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Wynik Bitwy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<center>
    <button onclick="goBack()">Powrót</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>';
?>
