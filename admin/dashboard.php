<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0"></script>
    <script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="images/favicon.ico">
    <style>
        .dashboard {
            display: flex;
            justify-content: space-between;
            margin: 20px;
            gap: 10px;
            /* Add some space between the cards */
        }

        .card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .card-large {
            flex: 1.5;
            /* Make this card 1.5 times larger */
        }

        .card .card-label {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .card .number {
            font-size: 32px;
            font-weight: bold;
            color: #f47235;
        }

        .card-title {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 400px;
            /* Adjust as necessary */
            height: 300px;
            /* Adjust as necessary */
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <?php include ('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include ('include/sidebar.php'); ?>
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Dashboard</h3>
                            </div>

                            <!-- Counting Dashboard -->
                            <div class="dashboard">
                                <div class="card">

                                    <div class="card-label">Total Orders</div>
                                    <?php
                                    $query = mysqli_query($con, "SELECT count(orderStatus) as totalorder FROM orders");
                                    $row = mysqli_fetch_array($query) ?>
                                    <div class="number"><?php echo $row['totalorder'] ?></div>
                                    </a>
                                </div>
                                <div class="card">
                                    <div class="card-label">Total Products</div>
                                    <?php
                                    $query = mysqli_query($con, "SELECT count(*) as totalproduct FROM products");
                                    $row = mysqli_fetch_array($query) ?>
                                    <div class="number"><?php echo $row['totalproduct'] ?></div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Total Users</div>
                                    <?php
                                    $query = mysqli_query($con, "SELECT count(*) as totaluser FROM users");
                                    $row = mysqli_fetch_array($query) ?>
                                    <div class="number"><?php echo $row['totaluser'] ?></div>
                                </div>
                            </div>

                            <!-- Payment and Rating AVG Dashboard -->
                            <div class="dashboard">
                                <div class="card card-large">
                                    <div class="card-label">Total Sales</div>
                                    <?php
                                    $query = mysqli_query($con, "SELECT sum(grandtotal) as grandtotal FROM orders WHERE orderStatus IS NOT NULL");
                                    $row = mysqli_fetch_array($query) ?>
                                    <div class="number">RM <?php echo $row['grandtotal'] ?></div>
                                </div>
                                <?php $query = mysqli_query($con, "SELECT avg(quality) AS quality, avg(price) AS price, avg(value) AS value FROM productreviews");
                                $row = mysqli_fetch_array($query) ?>
                                <div class="card">
                                    <div class="card-label">Quality Rate Avg</div>
                                    <div class="number"><i class="icon fa fa-solid fa-star"></i><b>
                                        </b><?php echo number_format($row['quality'], 2) ?></div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Price Rate Avg</div>
                                    <div class="number"><i class="fa-solid fa-money-bill-wave"></i><b>
                                        </b><?php echo number_format($row['price'], 2) ?></div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Value Rate Avg</div>
                                    <div class="number"><i class="icon fa fa-solid fa-face-grin-stars"></i><b>
                                        </b><?php echo number_format($row['value'], 2) ?></div>
                                </div>
                            </div>

                            <div class="dashboard">
                                <div class="card">
                                    <div class="card-title">Top Selling Categories</div>
                                    <div class="chart-container">
                                        <canvas id="myChart"></canvas>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-title">Order Status</div>
                                    <div class="chart-container orderChart">
                                        <canvas id="orderChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <?php

                            // Query the database
                            $query = "SELECT 
                            c.categoryName AS categoryName, 
                            SUM(od.quantity) AS totalQuantity
                            FROM 
                                orders o
                            JOIN 
                                orderdetails od ON o.id = od.orderId
                            JOIN 
                                products p ON od.productId = p.id
                            JOIN 
                                category c ON p.category = c.id
                            WHERE 
                                o.orderStatus IS NOT NULL
                            GROUP BY 
                                c.categoryName;";
                            $result = $con->query($query);

                            // Initialize arrays
                            $xValues = [];
                            $yValues = [];

                            // Fetch data from the database
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $xValues[] = $row['categoryName'];
                                    $yValues[] = $row['totalQuantity'];
                                }
                            } else {
                                echo "0 results";
                            }

                            //For orderChart
                            $query = "SELECT orderStatus, COUNT(*) as statusCount
                            FROM orders
                            WHERE orderStatus IS NOT NULL
                            GROUP BY orderStatus 
                            ORDER BY orderStatus DESC;";
                            $result = $con->query($query);

                            // Initialize arrays
                            $iValues = [];
                            $jValues = [];

                            // Fetch data from the database
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $iValues[] = $row['orderStatus'];
                                    $jValues[] = $row['statusCount'];
                                }
                            } else {
                                echo "0 results";
                            }

                            ?>

                            <div class="module-body" style="padding:0px !important; margin:20px;">
                                <!-- Filters -->
                                <div class="module">
                                    <div class="module-head">
                                        <h3>Filters</h3>
                                    </div>
                                    <div class="module-body">
                                        <form id="filterForm">
                                            <div class="control-group">
                                                <label class="control-label" for="filterYear">Select Year</label>
                                                <div class="controls">
                                                    <select id="filterYear" name="filterYear" class="span8">
                                                        <!-- Populate with available years -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label" for="filterMonth">Select Month</label>
                                                <div class="controls">
                                                    <select id="filterMonth" name="filterMonth" class="span8">
                                                        <option value="all">All</option>
                                                        <option value="01">January</option>
                                                        <option value="02">February</option>
                                                        <option value="03">March</option>
                                                        <option value="04">April</option>
                                                        <option value="05">May</option>
                                                        <option value="06">June</option>
                                                        <option value="07">July</option>
                                                        <option value="08">August</option>
                                                        <option value="09">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <div class="controls">
                                                    <button type="button" id="filterButton" class="btn">Filter</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row" style="display:flex;justify-content:center;">
                                    <div class="span4">
                                        <div class="module">
                                            <div class="module-head">
                                                <h3>Daily Total Sales Chart</h3>
                                            </div>
                                            <div class="module-body">
                                                <canvas id="dailyChart"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span4">
                                        <div class="module">
                                            <div class="module-head">
                                                <h3>Monthly Total Sales Chart</h3>
                                            </div>
                                            <div class="module-body">
                                                <canvas id="monthlyChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    // PHP arrays to JavaScript
                                    const xValues = <?php echo json_encode($xValues); ?>;
                                    const yValues = <?php echo json_encode($yValues); ?>;
                                    const iValues = <?php echo json_encode($iValues); ?>;
                                    const jValues = <?php echo json_encode($jValues); ?>;

                                    // Define bar colors
                                    const barColors = [

                                        "#FF4F03",
                                        "#FB9342",
                                        "#FFA532",
                                        "#FFAD89",
                                        "#FF7B42",
                                        "#FFCB89",
                                        "#F98C00"
                                    ];
                                    // Create chart
                                    new Chart("myChart", {
                                        type: "pie",
                                        data: {
                                            labels: xValues,
                                            datasets: [{
                                                backgroundColor: barColors.slice(0, xValues.length),
                                                data: yValues
                                            }]
                                        },
                                        options: {
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                            }
                                        }
                                    });

                                    new Chart("orderChart", {
                                        type: "bar",
                                        data: {
                                            labels: iValues,
                                            datasets: [{
                                                backgroundColor: barColors.slice(0, iValues.length),
                                                data: jValues
                                            }]
                                        },
                                        options: {
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                            }
                                        }
                                    });

                                    document.addEventListener('DOMContentLoaded', function () {
                                        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
                                        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

                                        // Populate year dropdown with available years
                                        const currentYear = new Date().getFullYear();
                                        const filterYear = document.getElementById('filterYear');
                                        for (let year = currentYear; year >= 2000; year--) {
                                            const option = document.createElement('option');
                                            option.value = year;
                                            option.textContent = year;
                                            filterYear.appendChild(option);
                                        }
                                        filterYear.value = currentYear; // Set default year to current year

                                        // Set default value of month dropdown to "All"
                                        const filterMonth = document.getElementById('filterMonth');
                                        filterMonth.value = 'all'; // Set default month to "All"

                                        const dailyData = {
                                            labels: [],
                                            datasets: [{
                                                label: 'Daily Total Sales',
                                                data: [],
                                                backgroundColor: 'rgba(255,103,0,0.4)',
                                                borderColor: 'rgba(255,88,15, 1)',
                                                borderWidth: 1
                                            }]
                                        };

                                        const dailyConfig = {
                                            type: 'bar',
                                            data: dailyData,
                                            options: {
                                                scales: {
                                                    y: {
                                                        beginAtZero: true
                                                    },
                                                    x: {
                                                        type: 'time',
                                                        time: {
                                                            unit: 'day'
                                                        }
                                                    }
                                                },
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    }
                                                }
                                            }
                                        };

                                        const dailyChart = new Chart(dailyCtx, dailyConfig);

                                        const monthlyData = {
                                            labels: [],
                                            datasets: [{
                                                label: 'Monthly Total Sales',
                                                data: [],
                                                backgroundColor: 'rgba(255, 184, 5, 0.4)',
                                                borderColor: 'rgba(255, 165, 5, 1)',
                                                borderWidth: 1
                                            }]
                                        };

                                        const monthlyConfig = {
                                            type: 'bar',
                                            data: monthlyData,
                                            options: {
                                                scales: {
                                                    y: {
                                                        beginAtZero: true
                                                    }
                                                },
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    }
                                                }
                                            }
                                        };

                                        const monthlyChart = new Chart(monthlyCtx, monthlyConfig);

                                        function fetchDataAndUpdateCharts() {
                                            const year = filterYear.value;
                                            const month = filterMonth.value; // Will be "all" if "All" is selected

                                            fetch('filter_data.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                },
                                                body: JSON.stringify({ year, month })
                                            })
                                                .then(response => response.json())
                                                .then(data => {
                                                    dailyChart.data.labels = data.daily.labels;
                                                    dailyChart.data.datasets[0].data = data.daily.data;
                                                    dailyChart.update();

                                                    monthlyChart.data.labels = data.monthly.labels;
                                                    monthlyChart.data.datasets[0].data = data.monthly.data;
                                                    monthlyChart.update();
                                                });
                                        }

                                        document.getElementById('filterButton').addEventListener('click', fetchDataAndUpdateCharts);

                                        // Fetch and display data on page load
                                        fetchDataAndUpdateCharts();
                                    });

                                </script>
                            </div>
                        </div>
                    </div><!--/.content-->
                </div><!--/.span9-->
            </div>
        </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include ('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('.datatable-1').dataTable();
            $('.dataTables_paginate').addClass("btn-group datatable-pagination");
            $('.dataTables_paginate > a').wrapInner('<span />');
            $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
            $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
        });
    </script>
</body>

</html>
