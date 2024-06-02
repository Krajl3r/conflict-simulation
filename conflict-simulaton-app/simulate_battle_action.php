<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $scenario_id = $data['scenario_id'];

    // Pobierz jednostki dla wybranego scenariusza
    $sql_units = "SELECT * FROM units WHERE scenario_id = ?";
    $stmt_units = $conn->prepare($sql_units);
    $stmt_units->bind_param("i", $scenario_id);
    $stmt_units->execute();
    $result_units = $stmt_units->get_result();

    if ($result_units->num_rows > 0) {
        $units = [];
        while ($row = $result_units->fetch_assoc()) {
            // Inicjalizacja statystyk jednostek
            $row['total_losses'] = 0;
            $row['total_wounded'] = 0;
            $row['total_prisoners'] = 0;
            $row['total_equipment_losses'] = 0;
            $row['remaining_personnel'] = $row['personnel'];
            $units[] = $row;
        }

        // Logika symulacji bitwy
        $log = "";
        $total_casualties = 0;
        $total_wounded = 0;
        $total_prisoners = 0;
        $material_losses = [];
        $winner = "";
        $min_losses_ratio = PHP_INT_MAX; // Zmiana na stosunek strat do liczby jednostek
        $battle_duration = rand(1, 10);
        $current_time = 0;
        $timeLabels = [];
        $casualtiesData = [];
        $woundedData = [];
        $prisonersData = [];

        while ($current_time < $battle_duration) {
            foreach ($units as &$unit) {
                $max_losses = max($unit['remaining_personnel'], 0);
                $unit_losses = rand(0, min($max_losses, ceil($max_losses * 0.5)));
                $unit_wounded = rand(0, min($unit_losses, ceil($unit_losses * 0.8)));
                $unit_prisoners = rand(0, min($unit_losses - $unit_wounded, ceil(($unit_losses - $unit_wounded) * 0.5)));
                

                $unit['remaining_personnel'] -= ($unit_losses + $unit_wounded + $unit_prisoners);
                $unit['remaining_personnel'] = max($unit['remaining_personnel'], 0);

                $unit['total_losses'] += $unit_losses;
                $unit['total_wounded'] += $unit_wounded;
                $unit['total_prisoners'] += $unit_prisoners;

                $total_casualties += $unit_losses;
                $total_wounded += $unit_wounded;
                $total_prisoners += $unit_prisoners;

                $equipment_losses = rand(0, 10);
                $unit['total_equipment_losses'] += $equipment_losses;

                $log .= $unit['name'] . " - Straty: " . $unit['total_losses'] . " poległych, " . $unit['total_wounded'] . " rannych, " . $unit['total_prisoners'] . " jeńców.<br>";
                $material_losses[$unit['name']] = $unit['total_equipment_losses'];

                if ($unit['personnel'] > 0) {
                    $losses_ratio = $unit['total_losses'] / $unit['personnel'];
                    // Dodajmy do dziennika komunikat, aby zobaczyć stosunek strat do liczby jednostek dla każdej jednostki
                    if ($losses_ratio < $min_losses_ratio) {
                        $min_losses_ratio = $losses_ratio;
                        $winner = $unit['name'];
                    }
                }
            }

            // Zapisz dane do wykresów
            $timeLabels[] = $current_time;
            $casualtiesData[] = $total_casualties;
            $woundedData[] = $total_wounded;
            $prisonersData[] = $total_prisoners;

            $random_event_chance = rand(1, 100);
            if ($random_event_chance <= 20) {
                $random_events = ["Przełamanie linii wroga!", "Nagły atak flanki!", "Kontuzje dowódców!", "Atak chemiczny!"];
                $random_event = $random_events[array_rand($random_events)];
                $unit_index = array_rand($units); // Losowy indeks jednostki
                $unit_name = $units[$unit_index]['name']; // Nazwa jednostki
                $casualties = rand(5, 20); // Losowa liczba poległych osób
                $log .= "Wydarzenie: $random_event od strony jednostki $unit_name. $casualties osób poległo.<br>";
            }
            

            $current_time += 2;
            sleep(2);
        }

        // Dodajmy do dziennika komunikat, aby zobaczyć, kto został wybrany jako zwycięzca i jaki był stosunek strat dla niego
        $log .= "Zwycięzca: " . $winner;

        $material_losses_log = "";
        foreach ($material_losses as $unit_name => $losses) {
            $material_losses_log .= $unit_name . ": " . $losses . " sprzętu, ";
        }
        $material_losses_log = rtrim($material_losses_log, ", ");

        $sql_insert_result = "INSERT INTO results (scenario_id, battle_log, casualties, wounded, prisoners, material_losses, battle_duration, winner) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert_result = $conn->prepare($sql_insert_result);
        $stmt_insert_result->bind_param("isiiisis", $scenario_id, $log, $total_casualties, $total_wounded, $total_prisoners, $material_losses_log, $battle_duration, $winner);
        $stmt_insert_result->execute();
        $stmt_insert_result->close();

        echo json_encode([
            'log' => $log,
            'timeLabels' => $timeLabels,
            'casualtiesData' => $casualtiesData,
            'woundedData' => $woundedData,
            'prisonersData' => $prisonersData,
            'battle_duration' => $battle_duration,
            'winner' => $winner,
            'finished' => true
        ]);
    } else {
        echo json_encode([
            'log' => 'Brak jednostek do symulacji dla wybranego scenariusza.',
            'finished' => true
        ]);
    }

    $stmt_units->close();
    $conn->close();
}
?>
