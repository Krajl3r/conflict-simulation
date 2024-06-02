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
        <h1>Tworzenie Scenariusza Bitwy</h1>
        <nav>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="manage_units.php">Zarządzanie Jednostkami</a></li>
                <li><a href="simulate_conflict.php">Symulacja Konfliktu</a></li>
                <li><a href="analyze_results.php">Analiza Wyników</a></li>
            </ul>
        </nav>
    </header>
    <?php
include 'db.php';

$action = $_POST['action'];

switch ($action) {
    case 'add':
        $unit_name = $_POST['unit_name'];
        $unit_type = $_POST['unit_type'];
        $readiness = $_POST['readiness'];
        $equipment = $_POST['equipment'];
        $personnel = $_POST['personnel'];
        $scenario_id = $_POST['scenario_id'];

        $sql = "INSERT INTO units (name, type, readiness, equipment, personnel, scenario_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $unit_name, $unit_type, $readiness, $equipment, $personnel, $scenario_id);

        if ($stmt->execute()) {
            echo "Jednostka została dodana pomyślnie.";
        } else {
            echo "Błąd: " . $stmt->error;
        }
        $stmt->close();
        break;

    case 'edit':
        $unit_id = $_POST['unit_id'];
        $unit_name = $_POST['unit_name'];
        $unit_type = $_POST['unit_type'];
        $readiness = $_POST['readiness'];
        $equipment = $_POST['equipment'];
        $personnel = $_POST['personnel'];

        $sql = "UPDATE units SET name = ?, type = ?, readiness = ?, equipment = ?, personnel = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $unit_name, $unit_type, $readiness, $equipment, $personnel, $unit_id);

        if ($stmt->execute()) {
            echo "Jednostka została zaktualizowana pomyślnie.";
        } else {
            echo "Błąd: " . $stmt->error;
        }
        $stmt->close();
        break;

    case 'delete':
        $unit_id = $_POST['unit_id'];

        $sql = "DELETE FROM units WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $unit_id);

        if ($stmt->execute()) {
            echo "Jednostka została usunięta pomyślnie.";
        } else {
            echo "Błąd: " . $stmt->error;
        }
        $stmt->close();
        break;

    default:
        echo "Nieprawidłowa akcja.";
        break;
}

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
</body>
</html>
