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
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '2018';
$lowestAreas = getLowestAreasForYear($array, $selectedYear);

##########################
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transportation Mode Share Dashboard</title>
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
        width: 100%;
        background-color: #F1F5FC;
        color: #002360;
    }

    .header {
        background-color: #fff;
        padding:10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 6px 12px rgba(6, 96, 254, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }

    .logo-area {
        display: flex;
        align-items: center;
    }

    .logo-area img {
        width: 90px;
        height: auto;
      
    }

    .logo-area span {
        margin-left: 24px;
        font-size: 18px;
        color:#002360;
        font-weight: 560;

    }

    .user-profile {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }

    .user-profile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    h2, h3 {
        margin-top: 0;
        color:#002360;
    }

    .main-section {
    padding: 16px;
    background-color: #f0f7ff;
    margin-top: 75px; 
}

    .year-tabs {
        display: flex;
        margin-bottom: 20px;
        margin-top:30px;
      
    }

    .year-tab {
        
        padding: 8px 36px;
        background-color: #ffffff;
        margin-right: 10px;
        text-decoration: none;
        color: #333;
        text-align: center;
        min-width: 120px;
        border-radius: 8px;
        font-weight: bold;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease; 
    }
    .year-tab:hover {
    background-color:rgb(173, 205, 255); 
}
    .year-tab.active {
        background-color: #0066FF;
        color: white;
    }

    .dashboard-container {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .chart-container {
        flex: 1 1 65%;
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .chart {
        height: 400px;
        width: 100%;
    }

    .areas-lacking {
        flex: 1 1 30%;
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .ranking-table {
        width: 100%;
    }

    .ranking-table tbody tr {
        display: grid;
        grid-template-columns: 40px 1fr 80px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .ranking-table .rank {
        font-weight: bold;
        color: #4a6fa5;
    }

    .ranking-table .area {
        font-weight: 500;
    }

    .ranking-table .percentage {
        text-align: center;
        background-color: #e8f0ff;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
        color: #4a6fa5;
    }

    .empty-space {
        background-color: white;
        border-radius: 12px;
        height: 100px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .debug-section {
        display: none;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            flex-direction: column;
        }
    }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-area">
            <img src="logo.png" alt="City of Vancouver Logo">
            <span>Open Data Portal</span>
        </div>
        <div class="user-profile">
            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="User Profile">
        </div>
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
                    <tbody>
                        <?php foreach($lowestAreas as $index => $area): ?>
                        <tr>
                            <td class="rank"><?= $index + 1 ?></td>
                            <td class="area"><?= $area['name'] ?></td>
                            <td class="percentage"><?= $area['percentage'] ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="empty-space"></div>
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
                        hoverBackgroundColor: '#0066FF',
                        barPercentage: 0.8,
                        categoryPercentage: 0.7,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#E5E5E5'
                            },
                            title: {
                                display: true,
                                text: 'Percentage (%)',
                                color: '#666666'
                            }
                        },
                        x: {
                            grid: {
                               display: false,
                            },
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