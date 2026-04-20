<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function build_month_array(arr) {
        const newArr = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (let i = 0; i < arr.length; i++) {
            newArr[Number(arr[i].m) - 1] = Number(arr[i].v);
        }
        return newArr;
    }

    function find_max_graph_axis(arr) {
        let m = Math.max(...arr);
        m = Math.ceil(m + (5 - (m % 5)));
        return m;
    }
</script>
<body>
    <div class="graphs-wrapper">
        <div class="graph-card">
            <h2>Volunteer Hours by Month <?php echo isset($_GET['semester']) ? (substr($_GET['semester'], -4) == "All" ? date('Y') :  substr($_GET['semester'], -4)) : date('Y'); ?></h2>
            <canvas id="hoursChart"></canvas>
        </div>

        <div class="graph-card">
            <h2>Pounds of Food Rescued by Month <?php echo isset($_GET['semester']) ? substr($_GET['semester'], -4) : date('Y'); ?></h2>
            <canvas id="poundsChart"></canvas>
        </div>
    </div>
    <script>
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const hoursData = build_month_array(<?php echo json_encode(get_monthly_hours(isset($_GET['semester']) ? $_GET['semester'] : "All")); ?>);
    const poundsData = build_month_array(<?php echo json_encode(get_monthly_pounds(isset($_GET['semester']) ? $_GET['semester'] : "All")); ?>);

    const hoursChart = document.getElementById('hoursChart');
    const poundsChart = document.getElementById('poundsChart');

    new Chart(hoursChart, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Volunteer Hours',
                data: hoursData,
                backgroundColor: '#397eac' // 208-204, 45-50, 24-45
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: find_max_graph_axis(hoursData),
                    ticks: {
                        color: '#213e57',
                        font: {
                            size: 20
                        }
                    }
                },
                x: {
                    ticks: {
                        color: '#213e57',
                        font: {
                            size: 20
                        }
                    },
                    grid: {
                        display: false
                    }
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
                backgroundColor: '#63931f'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: find_max_graph_axis(poundsData),
                    ticks: {
                        color: '#213e57',
                        font: {
                            size: 20
                        }
                    }
                },
                x: {
                    ticks: {
                        color: '#213e57',
                        font: {
                            size: 20
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
</body>
</html>