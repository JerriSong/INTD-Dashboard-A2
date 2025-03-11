<?php
// ////////////////////////
// example of how to use content from an external JSON file in PHP
// Transit Data Dashboard, March/2025
// ////////////////////////

$json = file_get_contents('data.json'); // get the external file
$array = json_decode($json, true); // transform JSON format into an Array in PHP

// Helper function to get the lowest areas for a specific year
function getLowestAreasForYear($data, $year) {
    $yearKey = "lowest_areas_" . $year;
    return isset($data[$yearKey]) ? $data[$yearKey] : [];
}

// Default selected year (can be changed via UI)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '2020';
$lowestAreas = getLowestAreasForYear($array, $selectedYear);

##########################
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transportation Mode Share Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="header">
        <h1>Open Data Portal</h1>
        <div class ="logo">
        <img src="logo.png" alt="logo">
        </div>
    </div>

    <div class="overview-card">
        <div class="title">See the overview</div>
        <div class="action-button"><!-- Action button would go here --></div>
    </div>

    <div class="main-section">
        <h2>Mode share (trips made by foot, bike, or transit)</h2>
        <p>The goal is to increase community services to <?= $array['goal']['target_percentage'] ?>% in <?= $array['goal']['year'] ?></p>

        <div class="year-tabs">
            <a href="?year=2018" class="year-tab <?= $selectedYear == '2018' ? 'active' : '' ?>">2018</a>
            <a href="?year=2019" class="year-tab <?= $selectedYear == '2019' ? 'active' : '' ?>">2019</a>
            <a href="?year=2020" class="year-tab <?= $selectedYear == '2020' ? 'active' : '' ?>">2020</a>
        </div>

        <div class="dashboard-container">
            <div class="chart-container">
                <h3>Comparison of Areas</h3>
                <div class="chart">
                    <canvas id="transportChart"></canvas>
                </div>
            </div>

            <div class="areas-lacking">
                <h3>Top 3 areas that lacking community services</h3>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th width="10%">Rank</th>
                            <th width="60%">Area</th>
                            <th width="15%">Year</th>
                            <th width="15%">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lowestAreas as $area): ?>
                        <tr>
                            <td><?= $area['ranking'] ?></td>
                            <td><?= $area['name'] ?></td>
                            <td><?= $area['year'] ?></td>
                            <td><?= $area['percentage'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="debug-section">
        <pre><?php // for debugging ////////////////////
// var_dump($json); // uncomment this if needed for debugging
// var_dump($array); // uncomment this if needed for debugging
// you can also look at the terminal on the server with the commands:
// tail -f /var/log/apache2/error.log
// tail -f /var/log/apache2/access.log
?></pre>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Parse the PHP data into JavaScript
            const transportData = <?php echo json_encode($array); ?>;
            const selectedYear = "<?php echo $selectedYear; ?>";
            
            // Prepare data for chart
            const areas = transportData.areas.map(area => area.name);
            const yearData = transportData.areas.map(area => area.data[selectedYear]);
            
            // Create chart
            const ctx = document.getElementById('transportChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: areas,
                    datasets: [{
                        label: selectedYear + ' Transport Usage (%)',
                        data: yearData,
                        backgroundColor: 'rgba(75, 75, 75, 0.7)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentage (%)'
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
