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
        $min_losses = PHP_INT_MAX;

        // Losowy czas trwania bitwy między 1 a 10 sekund
        $battle_duration = rand(1, 10);
        $current_time = 0;

        while ($current_time < $battle_duration) {
            foreach ($units as &$unit) {
                // Obliczanie maksymalnych strat na podstawie pozostałego personelu
                $max_losses = max($unit['remaining_personnel'], 0);

                // Obliczanie strat
                $unit_losses = rand(0, $max_losses);
                $unit_wounded = rand(0, $unit_losses);
                $unit_prisoners = rand(0, max($unit_losses - $unit_wounded, 0));

                // Aktualizacja jednostki
                $unit['remaining_personnel'] -= ($unit_losses + $unit_wounded + $unit_prisoners);
                $unit['remaining_personnel'] = max($unit['remaining_personnel'], 0);

                // Aktualizacja sum jednostek
                $unit['total_losses'] += $unit_losses;
                $unit['total_wounded'] += $unit_wounded;
                $unit['total_prisoners'] += $unit_prisoners;

                // Aktualizacja sum ogólnych
                $total_casualties += $unit_losses;
                $total_wounded += $unit_wounded;
                $total_prisoners += $unit_prisoners;

                // Losowe straty sprzętu
                $equipment_losses = rand(0, 10);
                $unit['total_equipment_losses'] += $equipment_losses;

                // Logi z jednostki
                $log .= $unit['name'] . " - Straty: " . $unit['total_losses'] . " poległych, " . $unit['total_wounded'] . " rannych, " . $unit['total_prisoners'] . " jeńców.<br>";
            }

                        // Losowe wydarzenia w czasie bitwy
                        $random_event_chance = rand(1, 100);
                        if ($random_event_chance <= 20) { // 20% szansy na wydarzenie losowe
                            $random_events = ["Przełamanie linii wroga!", "Nagły atak flanki!", "Kontuzje dowódców!", "Atak chemiczny!"];
                            $random_event = $random_events[array_rand($random_events)];
                            $log .= "Wydarzenie: " . $random_event . "<br>";
                        }
            
                        $current_time += 2; // Aktualizacja czasu co 2 sekundy
                        sleep(2); // Czekaj 2 sekundy przed następną iteracją
                    }
            
                    // Ustalenie zwycięzcy na podstawie najmniejszych strat
                    $total_losses_all = 0;
                    foreach ($units as $unit) {
                        $total_losses_all += $unit['total_losses'];
                    }
            
                    foreach ($units as $unit) {
                        if ($unit['total_losses'] < $min_losses) {
                            $min_losses = $unit['total_losses'];
                            $winner = $unit['name'];
                        }
                    }
            
                    // Przygotowanie logu strat sprzętu
                    $material_losses_log = "";
                    foreach ($units as $unit) {
                        $material_losses_log .= $unit['name'] . ": " . $unit['total_equipment_losses'] . " sprzętu, ";
                    }
                    $material_losses_log = rtrim($material_losses_log, ", ");
            
                    // Zapisz wynik bitwy do bazy danych
                    $sql_insert_result = "INSERT INTO results (scenario_id, battle_log, casualties, wounded, prisoners, material_losses, battle_duration, winner) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_result = $conn->prepare($sql_insert_result);
                    $stmt_insert_result->bind_param("isiiisis", $scenario_id, $log, $total_casualties, $total_wounded, $total_prisoners, $material_losses_log, $battle_duration, $winner);
                    $stmt_insert_result->execute();
                    $stmt_insert_result->close();
            
                    // Zwróć odpowiedź JSON z raportem bitwy
                    echo json_encode([
                        'log' => $log,
                        'casualties' => $total_casualties,
                        'wounded' => $total_wounded,
                        'prisoners' => $total_prisoners,
                        'material_losses' => $material_losses_log,
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
            
