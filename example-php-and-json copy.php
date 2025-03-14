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
        width: 95%;
        background-color: #F1F5FC;
        color: #002360;
        margin-left: 2.2%
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
    margin-top: 90px; 
}
.goal-text {
    margin-top: -10px; /* 负边距可以让文字向上移动 */
    padding-top: 0;
}
    .year-tabs {
        display: flex;
        margin-bottom: 20px;
        margin-top:30px;
      
    }

    .year-tab {
        
        padding: 8px 55px;
        background-color: #ffffff;
        margin-right: 10px;
        text-decoration: none;
        color: #333;
        text-align: center;
        min-width: 120px;
        border-radius: 8px;
        font-weight: bold;
        box-shadow: 0 1px 3px rgba(13, 54, 150, 0.1);
        transition: background-color 0.3s ease; 
        #002360
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
        margin-bottom: 22px;
    }

    .chart-container {
        flex: 1 1 69%;
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
        flex: 1 1 26%;
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .areas-lacking h3 {
  
   line-height:1.5;
}
    .ranking-table {
        width: 100%;
        padding-top:24px;
    }

    .ranking-table tbody tr {
        display: grid;
        grid-template-columns: 40px 1fr 80px;
        padding: 16px 0;
        border-bottom: 1px solid #eee;
        position: relative;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    /* Highlight style for the top 3 areas when corresponding bar is hovered */
    .ranking-table tbody tr.highlight {
        background-color:rgb(236, 236, 236);
        transition: background-color 0.2s ease;
    }
    
    /* Custom tooltip for the top 3 areas */
    #custom-tooltip {
        position: absolute;
        background-color: #002360;
        color: white;
        padding: 10px 14px;
        border-radius: 4px;
        font-size: 14px;
        z-index: 1000;
        pointer-events: none;
        display: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        font-weight: 500;
        line-height: 1.5;
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
        padding: 6px 8px;
        border-radius: 4px;
        font-weight: 600;
        color: #4a6fa5;
    }

    /* Dropdown styles for rationale */
    .dropdown-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .dropdown-header {
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        border-bottom: 1px solid transparent;
    }
    
    .dropdown-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #002360;
    }
    
    .dropdown-icon {
        transition: transform 0.3s ease;
    }
    
    .dropdown-icon.open {
        transform: rotate(180deg);
    }
    
    .dropdown-content {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    
    .dropdown-content.open {
        padding: 0 30px 40px 40px;
        max-width:90%;
        max-height: 2000px; /* Arbitrary large height */
        font-weight:390;
    }
    
    .dropdown-content h4 {
        margin-top: 30px;
        margin-bottom: 15px;
        color: #002360;
        font-size: 16px;
        font-weight:700;
    }
    
    .dropdown-content p {
        margin-top: 0;
        margin-bottom: 16px;
        line-height: 1.6;
        color: #002360;
    }
    
    .dropdown-content ul {
        margin-top: 0;
        margin-bottom: 20px;
        padding-left: 20px;
        color: #002360;
    }
    
    .dropdown-content li {
        margin-bottom: 10px;
        line-height: 1.6;
    }
    
    /* Add space at the bottom of the page */
    .page-bottom-space {
        height: 100px;
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
            <a href="index.html" style="display: flex; align-items: center; text-decoration: none;">
                <img src="logo.png" alt="City of Vancouver Logo">
                <span>Open Data Portal</span>
            </a>
        </div>
        <div class="user-profile">
            <img src="Snipaste_2025-03-13_16-57-01.png" alt="User Profile">
        </div>
    </div>

    <div class="main-section">
        <h2>Mode share (trips made by foot, bike, or transit)</h2>
        <p class="goal-text">The goal is to increase community services to <?= $array['goal']['target_percentage'] ?>% in <?= $array['goal']['year'] ?>
    </p>
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
                        <tr class="area-row" data-area="<?= $area['name'] ?>" data-percentage="<?= $area['percentage'] ?>">
                            <td class="rank"><?= $index + 1 ?></td>
                            <td class="area"><?= $area['name'] ?></td>
                            <td class="percentage"><?= $area['percentage'] ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Dropdown menu for rationale -->
        <div class="dropdown-container">
            <div class="dropdown-header" id="rationale-header">
                <h3>Rationale and analysis</h3>
                <div class="dropdown-icon">
                    <svg class="chevron-icon" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#002360" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 15 12 9 18 15"></polyline>
                    </svg>
                </div>
            </div>
            <div class="dropdown-content" id="rationale-content">
                <h4>Why we measure this</h4>
                <p>The <a href="#" style="color: #0066FF; text-decoration: none;">Climate Emergency Action Plan</a> and <a href="#" style="color: #0066FF; text-decoration: none;">Transportation 2040 Plan</a> set targets around our mode split to reduce our GHG emissions and ensure our transportation network plays a key role in shaping Vancouver's future growth. We measure mode share to track the effectiveness of:</p>
                
                <ul>
                    <li>Infrastructure investment that will encourage people to change their travel behaviour to sustainable modes of transportation such as walking, cycling, or taking public transit.</li>
                    <li>Policies and programs that encourage people to change their travel behaviour to sustainable modes of transportation such as walking, cycling, or taking public transit.</li>
                </ul>
                
                <h4>How we measure it</h4>
                <p>Our Engineering Services department conducts an annual Transportation Panel Survey recording the trips that respondents make using any mode of transportation. This indicator is based on all trips between two points, whether for work, school, shopping, socializing, or another purpose. It reports the percentage of these trips that are made by walking, cycling, or public transit on a typical weekday.</p>
            </div>
        </div>
        
        <!-- Add space at the bottom of the page -->
        <div class="page-bottom-space"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown functionality
            const dropdownHeader = document.getElementById('rationale-header');
            const dropdownContent = document.getElementById('rationale-content');
            const dropdownIcon = document.querySelector('.dropdown-icon');
            
            // Set initial state - closed by default
            dropdownContent.classList.remove('open');
            
            dropdownHeader.addEventListener('click', function() {
                dropdownContent.classList.toggle('open');
                dropdownIcon.classList.toggle('open');
                
                // Flip the chevron direction
                const chevron = document.querySelector('.chevron-icon');
                if (dropdownContent.classList.contains('open')) {
                    chevron.innerHTML = '<polyline points="6 15 12 9 18 15"></polyline>';
                } else {
                    chevron.innerHTML = '<polyline points="6 9 12 15 18 9"></polyline>';
                }
            });
            
            // Custom tooltip for Top 3 areas
            const customTooltip = document.getElementById('custom-tooltip');
            const areaRows = document.querySelectorAll('.area-row');
            
            areaRows.forEach(row => {
                row.addEventListener('mouseenter', function(e) {
                    const area = this.getAttribute('data-area');
                    const percentage = this.getAttribute('data-percentage');
                    
                    // Set tooltip content
                    customTooltip.innerHTML = `${area}<br>${selectedYear} Transport Usage (%): ${percentage}`;
                    
                    // Position the tooltip near the row
                    const rect = this.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    customTooltip.style.display = 'block';
                    customTooltip.style.top = (rect.top + scrollTop - customTooltip.offsetHeight - 10) + 'px';
                    customTooltip.style.left = (rect.left + rect.width/2 - 100) + 'px';
                });
                
                row.addEventListener('mouseleave', function() {
                    customTooltip.style.display = 'none';
                });
                
                // For mobile touch events
                row.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    const area = this.getAttribute('data-area');
                    const percentage = this.getAttribute('data-percentage');
                    
                    customTooltip.innerHTML = `${area}<br>${selectedYear} Transport Usage (%): ${percentage}`;
                    
                    const rect = this.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    customTooltip.style.display = 'block';
                    customTooltip.style.top = (rect.top + scrollTop - customTooltip.offsetHeight - 10) + 'px';
                    customTooltip.style.left = (rect.left + rect.width/2 - 100) + 'px';
                    
                    // Hide tooltip after a short delay
                    setTimeout(() => {
                        customTooltip.style.display = 'none';
                    }, 2000);
                });
            });
        
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
                    onHover: (event, chartElements) => {
                        // Reset all highlights first
                        document.querySelectorAll('.area-row').forEach(row => {
                            row.classList.remove('highlight');
                        });
                        
                        if (chartElements && chartElements.length > 0) {
                            // Get the area name from the hovered bar
                            const index = chartElements[0].index;
                            const hoveredArea = areas[index];
                            
                            // Find the corresponding row in the top 3 list and highlight it
                            document.querySelectorAll('.area-row').forEach(row => {
                                if (row.getAttribute('data-area') === hoveredArea) {
                                    row.classList.add('highlight');
                                }
                            });
                        }
                    },
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
                        },
                        tooltip: {
                            backgroundColor: '#002360',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 10,
                            cornerRadius: 4,
                            displayColors: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>