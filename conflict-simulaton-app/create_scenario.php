<!DOCTYPE html>
<html lang="en">
<head>
<audio controls autoplay>
  <source src="music.mp3" type="audio/mp3">
  Twoja przeglądarka nie obsługuje elementu audio.
</audio>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <title>Tworzenie Scenariusza Bitwy</title>
    <style>
        #map {
            height: 400px;
        }
        #battle-log {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
        }
        #map {
            height: 500px;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
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
    <main>
        
        <form action="create_scenario_action.php" method="POST">
            <label for="name">Nazwa Scenariusza:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <br>
            <label for="theater">Teatr Działań Wojennych:</label>
            <h3>At first select sides(hold CTRL and click LPM), then click LPM to select battle side</h3>
            <div id="map"></div>
            <input type="text" id="theater" name="theater" required>
            <br>
            <br>
            <label for="sides">Strony Konfliktu:</label>
            <input type="text" id="sides" name="sides" required>
            <br>
            <br>
            <label for="mission_objectives">Cele Misji:</label>
            <input id="mission_objectives" name="mission_objectives" required></input>
            <br>
            <br>
            <label for="terrain_conditions">Warunki Terenowe:</label>
            <select id="terrain_conditions" name="terrain_conditions">
                <option value="Plains">Równiny</option>
                <option value="Mountains">Góry</option>
                <option value="Forest">Las</option>
                <option value="Desert">Pustynia</option>
            </select><br>
            <br>
            <label for="weather_conditions">Warunki Pogodowe:</label>
            <select id="weather_conditions" name="weather_conditions">
                <option value="Clear">Bezchmurnie</option>
                <option value="Rainy">Deszczowo</option>
                <option value="Snowy">Śnieżnie</option>
                <option value="Foggy">Mglisto</option>
            </select><br>
            <br>
            <label for="initial_conditions">Początkowe Warunki Bitwene:</label>
            <select id="initial_conditions" name="initial_conditions">
                <option value="Day">Dzień</option>
                <option value="Night">Noc</option>
                <option value="Morning">Ranek</option>
                <option value="Evening">Wieczór</option>
            </select><br>
            
            <button type="submit">Utwórz Scenariusz</button>
            <a href="manage_units.php">
    <button>Dalej</button>
</a>
        </form>
        <h2>Usuń Scenariusz:</h2>
    <form action="delete_scenario_action.php" method="POST">
        <label for="scenario">Wybierz scenariusz do usunięcia:</label>
        <select id="scenario_id_to_delete" name="scenario_id_to_delete">
            <option value="" selected disabled>Wybierz scenariusz</option>
            <?php
            // Połączenie z bazą danych (zmień dane na odpowiednie dla Twojej bazy danych)
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "conflict_simulation";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Sprawdzenie czy połączenie się udało
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pobranie wszystkich scenariuszy z bazy danych
            $sql = "SELECT id, name FROM scenarios";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Wyświetlenie opcji do wyboru scenariusza
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                }
            } else {
                echo "<option value=''>Brak dostępnych scenariuszy</option>";
            }
            $conn->close();
            ?>
        </select><br>
        <input type="submit" value="Usuń">
    </form>

        </table>
        <div id="map"></div>
        
        <script>
            var map = L.map('map').setView([51.505, -0.09], 3);
            var selectedCountries = [];
            var marker;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            function selectCountry(e) {
                var layer = e.target;
                var countryName = layer.feature.properties.name;
                if (e.originalEvent.ctrlKey) {
                    if (!selectedCountries.includes(countryName)) {
                        selectedCountries.push(countryName);
                        document.getElementById('sides').value = selectedCountries.join(', ');
                    }
                }
            }

            function addMarker(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('theater').value = e.latlng.lat + ', ' + e.latlng.lng;
            }

            function onEachFeature(feature, layer) {
                layer.on({
                    click: selectCountry
                });
            }

            map.on('click', addMarker);

            fetch('custom.geo.json')
                .then(response => response.json())
                .then(data => {
                    L.geoJSON(data, {
                        onEachFeature: onEachFeature
                    }).addTo(map);
                });
        </script>
    </main>
</body>
</html>
