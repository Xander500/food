<?php
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    // admin-only access
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Impact</title>
    <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">

    <?php
    $tailwind_mode = true;
    require_once('header.php');
    ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
.page-title {
    text-align: center;
    font-size: 42px;
    font-weight: bold;
    color: #23415A;
    margin-top: 120px;
    margin-bottom: 30px;
}

        .return-button {
            display: inline-block;
            background-color: #23415A;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .return-button:hover {
            opacity: 0.9;
        }
        .graphs-wrapper {
    max-width: 1100px;
    margin: 40px auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    padding: 0 20px;
}

.graph-card {
    background: white;
    border-radius: 14px;
    padding: 25px 40px 35px 40px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-align: center;
    min-height: 420px;
}

.graph-card h2 {
    text-align: center;
    color: #23415A;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
}
canvas {
    display: block;
    width: 100% !important;
    max-width: 900px;
    height: 320px !important;
    margin: 0 auto;
}
    </style>
</head>
<body>
    <h1 class="page-title">Monthly Impact</h1>

    <div style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <a href="analyticsDashboard.php" class="return-button">Return to Analytics Dashboard</a>
    </div>

    <div class="graphs-wrapper">
        <div class="graph-card">
            <h2>Monthly Volunteer Hours</h2>
            <canvas id="hoursChart"></canvas>
        </div>

        <div class="graph-card">
            <h2>Monthly Pounds of Food Rescued</h2>
            <canvas id="poundsChart"></canvas>
        </div>
    </div>
    <script>
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    const hoursData = [4, 6, 3, 5, 2, 4];
    const poundsData = [20, 35, 18, 40, 12, 25];

    const hoursChart = document.getElementById('hoursChart');
    const poundsChart = document.getElementById('poundsChart');

    new Chart(hoursChart, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Volunteer Hours',
                data: hoursData,
                backgroundColor: '#9BCB46',
                borderColor: '#23415A',
                borderWidth: 1
            }]
        },
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            align: 'center'
        }
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
}
    });

    new Chart(poundsChart, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Pounds of Food Rescued',
                data: poundsData,
                backgroundColor: '#379dc1',
                borderColor: '#23415A',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>