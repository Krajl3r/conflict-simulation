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

$name = $_POST['name'];
$theater = $_POST['theater'];
$sides = $_POST['sides'];
$mission_objectives = $_POST['mission_objectives'];
$terrain_conditions = $_POST['terrain_conditions'];
$weather_conditions = $_POST['weather_conditions'];
$initial_conditions = $_POST['initial_conditions'];

$sql = "INSERT INTO scenarios (name, theater, sides, mission_objectives, terrain_conditions, weather_conditions, initial_conditions)
VALUES ('$name', '$theater', '$sides', '$mission_objectives', '$terrain_conditions', '$weather_conditions', '$initial_conditions')";

if ($conn->query($sql) === TRUE) {
    echo "<center><br><h3>Nowy scenariusz został utworzony pomyślnie</center>";
} else {
    echo "Błąd: " . $sql . "<br>" . $conn->error;
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