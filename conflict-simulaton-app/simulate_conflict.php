<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Symulacja Konfliktu</title>
    <audio controls autoplay>
        <source src="music.mp3" type="audio/mp3">
        Twoja przeglądarka nie obsługuje elementu audio.
    </audio>
</head>
<body>
<header>
    <h1>Symulacja Konfliktu</h1>
    <nav>
        <ul>
            <li><a href="index.php">Strona Główna</a></li>
            <li><a href="manage_units.php">Zarządzanie Jednostkami</a></li>
            <li><a href="create_scenario.php">Tworzenie Scenariusza</a></li>
            <li><a href="analyze_results.php">Analiza Wyników</a></li>
        </ul>
    </nav>
</header>
<main>
    <table>
        <tr>
            <th>ID Scenariusza</th>
            <th>Nazwa Scenariusza</th>
        </tr>
        <?php
        include 'db.php';

        // Pobierz dostępne scenariusze
        $sql_scenarios = "SELECT id, name FROM scenarios";
        $result_scenarios = $conn->query($sql_scenarios);

        if ($result_scenarios->num_rows > 0) {
            // Wyświetl każdy scenariusz w tabeli
            while ($row = $result_scenarios->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Brak dostępnych scenariuszy.</td></tr>";
        }
        ?>
    </table>

    <form id="simulateForm">
        <label for="scenario_id">ID Scenariusza:</label>
        <input type="text" id="scenario_id" name="scenario_id" required>
        <button type="button" onclick="startSimulation()">Rozpocznij Symulację</button>
    </form>
    <a href="analyze_results.php">
        <button>Dalej</button>
    </a>

    <div id="battle-log"></div>
    <div id="chart-container">
        <canvas id="casualtiesChart"></canvas>
        <canvas id="woundedChart"></canvas>
        <canvas id="prisonersChart"></canvas>
    </div>
</main>

<!-- Dodaj skrypt Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
    let casualtiesChart, woundedChart, prisonersChart;

    function destroyCharts() {
        if (casualtiesChart) {
            casualtiesChart.destroy();
        }
        if (woundedChart) {
            woundedChart.destroy();
        }
        if (prisonersChart) {
            prisonersChart.destroy();
        }
    }

    function startSimulation() {
        const scenarioId = document.getElementById('scenario_id').value;
        if (scenarioId) {
            simulateBattle(scenarioId);
        } else {
            alert('Proszę wprowadzić ID scenariusza.');
        }
    }

    function simulateBattle(scenarioId) {
        const logDiv = document.getElementById('battle-log');
        logDiv.innerHTML = 'Rozpoczynanie symulacji...<br>';

        // Symulacja bitwy za pomocą fetch
        fetch('simulate_battle_action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({scenario_id: scenarioId})
        })
        .then(response => response.json())
        .then(data => {
            logDiv.innerHTML += data.log + '<br>';
            logDiv.scrollTop = logDiv.scrollHeight;

            // Jeśli symulacja zakończona, wyświetl odpowiedni komunikat
            if (data.finished) {
                logDiv.innerHTML += 'Symulacja zakończona.<br>';
                // Inicjalizacja wykresów po zakończeniu symulacji
                initializeCharts();
            }

            // Aktualizacja danych na wykresach
            updateCharts(data);
        })
        .catch(error => {
            logDiv.innerHTML += 'Błąd symulacji: ' + error + '<br>';
        });
    }

    function initializeCharts() {
        // Inicjalizacja wykresów
        casualtiesChart = new Chart(document.getElementById('casualtiesChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ofiary',
                    data: [],
                    fill: false,
                    borderColor: 'red'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Czas (sekundy)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Liczba ofiar'
                        }
                    }
                }
            }
        });

        woundedChart = new Chart(document.getElementById('woundedChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ranni',
                    data: [],
                    fill: false,
                    borderColor: 'blue'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Czas (sekundy)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Liczba rannych'
                        }
                    }
                }
            }
        });

        prisonersChart = new Chart(document.getElementById('prisonersChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Jeńcy',
                    data: [],
                    fill: false,
                    borderColor: 'green'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Czas (sekundy)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Liczba jeńców'
                        }
                    }
                }
            }
        });
    }

    function updateCharts(data) {
        // Aktualizacja danych na wykresie ofiar
        if (casualtiesChart) {
            casualtiesChart.data.labels = data.timeLabels;
            casualtiesChart.data.datasets[0].data = data.casualtiesData;
            casualtiesChart.update();
        }

        // Aktualizacja danych na wykresie rannych
        if (woundedChart) {
            woundedChart.data.labels = data.timeLabels;
            woundedChart.data.datasets[0].data = data.woundedData;
            woundedChart.update();
        }

        // Aktualizacja danych na wykresie jeńców
        if (prisonersChart) {
            prisonersChart.data.labels = data.timeLabels;
            prisonersChart.data.datasets[0].data = data.prisonersData;
            prisonersChart.update();
        }
    }
</script>
</body>
</html>

