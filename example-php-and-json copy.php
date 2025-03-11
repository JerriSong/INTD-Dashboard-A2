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
   <style>
    /* Main styles for the transportation dashboard */
* {
    box-sizing: border-box;
}

body {
    font-family: sans-serif;
    margin: 0;
    padding: 0;
    width: 90%;
    background-color: #f5f5f5;
    color: #333;
}

.header {
    background-color: #fff;
    padding: 15px 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-size: 12px;
    font-weight: 600;
}


 h2, h3, h4 {
    margin-top: 0;
}

.overview-card {
    margin: 20px;
    padding: 15px;
    background-color: #eee;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.title {
    font-weight: bold;
    font-size: 2rem;
}

.main-section {
    margin: 20px;
    padding: 20px;
    background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.year-tabs {
    display: flex;
    margin-bottom: 20px;
}

.year-tab {
    padding: 8px 20px;
    background-color: #ffffff;
    margin-right: 5px;
    text-decoration: none;
    color: #333; /* 改为深色 */
    text-align: center;
    min-width: 80px;
    border: 1px solid #e0e0e0;
    border-radius: 8px; /* 添加圆角 */
}

.year-tab.active {
    background-color: #0660FE;
    color: white;
}

.dashboard-container {
    display: flex;
    flex-wrap: wrap;
}

.chart-container {
    flex: 1 1 65%;
    margin-bottom: 30px;
    min-width: 300px;
}

.chart {
    height: 400px;
    width: 100%;
}

.areas-lacking {
    flex: 1 1 30%;
    background-color: #eee;
    padding: 15px;
    border-radius: 4px;
    margin-left: 20px;
    min-width: 250px;
}

.ranking-table {
    width: 100%;
    border-collapse: collapse;
}

.ranking-table th,
.ranking-table td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.ranking-table tr:nth-child(even) {
    background-color: rgba(0,0,0,0.05);
}

.ranking-table th {
    background-color: #f0f0f0;
    font-weight: bold;
}

.debug-section {
    margin: 20px;
    padding: 10px;
    background-color: #f8f8f8;
    border: 1px solid #ddd;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }
    
    .areas-lacking {
        margin-left: 0;
        margin-top: 20px;
    }
}
   </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="logo" class="image" style="width:150px !important; height:auto !important; max-width:150px !important; object-fit:contain !important;">
        <p>Open Data Portal</p>
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
                <div class="title">See the overview</div>
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
                        backgroundColor: '#D9E7FF',
                        hoverBackgroundColor: '#0660FE',
                        barPercentage: 0.8,
                        categoryPercentage: 0.7,
                        borderRadius: 5
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
<!-- #D5EAFC -->