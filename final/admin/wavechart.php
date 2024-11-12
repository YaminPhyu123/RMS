<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

$con = mysqli_connect("localhost", "root", "", "project");
if (!$con) {
    die("Problem in database connection! Contact administrator!" . mysqli_error($con));
}

// Function to get daily data based on selected year and month
function getDailyData($year, $month) {
    global $con;
    $sql = "SELECT * FROM daily_record WHERE YEAR(date) = ? AND MONTH(date) = ? ORDER BY date DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $year, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_array($result)) {
        $data[] = $row;
    }
    return $data;
}

// Check this function to ensure correct SQL query and data retrieval
function getMonthlyData($year) {
  global $con;
  $sql = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(expense) AS purchaset, SUM(income) AS salet, SUM(profit) AS profitt
          FROM daily_record
          WHERE YEAR(date) = ?
          GROUP BY DATE_FORMAT(date, '%Y-%m')
          ORDER BY month DESC";
  $stmt = mysqli_prepare($con, $sql);
  mysqli_stmt_bind_param($stmt, 'i', $year);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $data = [];
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
      $data[] = $row;
  }
  return $data;
}


// Get year and month from GET parameters or default to current
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Fetch data
$daily_data = getDailyData($year, $month);
$monthly_data = getMonthlyData($year);

// Process and prepare data for charts and tables
$daily_dates = [];
$daily_expenses = [];
$daily_incomes = [];
$daily_profits = [];
$daily_table_rows = "";

foreach ($daily_data as $rowd) {
    $daily_dates[] = $rowd['date'];
    $daily_expenses[] = $rowd['expense'];
    $daily_incomes[] = $rowd['income'];
    $daily_profits[] = $rowd['profit'];
    $daily_table_rows .= "<tr>
                            <td>{$rowd['date']}</td>
                            <td>{$rowd['expense']}</td>
                            <td>{$rowd['income']}</td>
                            <td>{$rowd['profit']}</td>
                         </tr>";
}

$monthly_dates = [];
$monthly_expenses = [];
$monthly_incomes = [];
$monthly_profits = [];
$monthly_table_rows = "";

foreach ($monthly_data as $row) {
    $monthly_dates[] = $row['month'];
    $monthly_expenses[] = $row['purchaset'];
    $monthly_incomes[] = $row['salet'];
    $monthly_profits[] = $row['profitt'];
    $monthly_table_rows .= "<tr>
                                <td>{$row['month']}</td>
                                <td>{$row['purchaset']}</td>
                                <td>{$row['salet']}</td>
                                <td>{$row['profitt']}</td>
                             </tr>";
}

require_once('partials/_head.php');
?>
<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
th {
    background-color: #263748;
    color: white;
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd; 
}
td {
    padding: 8px;
    border-bottom: 1px solid #ddd; 
}
tr:nth-child(even) {
    background-color: #f9f9f8; 
}
.hidden {
    display: none;
}
  #daily_view_buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }

  #daily_view_buttons button {
    flex: 1;
    padding: 10px;
    margin: 0 5px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: black;
    background-color: white;
    color:black;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  #daily_view_buttons button.active {
    background-color: black;
    color:white;

  }

  #daily_view_buttons button:hover {
    background-color: #0056b3;
  }
  
  /* Optional: If you want to style the monthly buttons the same way */
  #monthly_view_buttons {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }

  #monthly_view_buttons button {
    flex: 1;
    padding: 10px;
    margin: 0 5px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: black;
    background-color: white;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  #monthly_view_buttons button.active {
    background-color: black;
    color:white;

  }

  #monthly_view_buttons button:hover {
    background-color: #0056b3;
  }


</style>
<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
          <!-- Removed Search Form -->
        </div>
      </div>
    </div>


<div class="container-fluid mt--8">
  <!-- All Reservations -->
  <div class="row mt-5">
    <div class="col">
      <div class="card shadow">
      <h2 class="page-header" style="width:100%; height:20%; text-align:center">Analytical Sales Report</h2>
        <select name="view_type" id="view_type" onchange="toggleViews()">
            <option value="daily">Daily</option>
            <option value="monthly">Monthly</option>
        </select>

        <!-- Daily View -->
        <div id="daily" class="hidden">
            <select id="daily_year" onchange="updateDailyData()">
                <!-- Options will be populated by JavaScript -->
            </select>
            <select id="daily_month" onchange="updateDailyData()">
                <!-- Options will be populated by JavaScript -->
            </select>
            <div id="daily_view_buttons">
                <button type="button" onclick="showDailyTable()">Table View</button>
                <button type="button" onclick="showDailyGraph()">Graph View</button>
            </div>
            <div id="daily_table"></div>
            <div id="daily_graph" class="hidden">
                <canvas id="chartjsd_line"></canvas>
            </div>
        </div>

        <!-- Monthly View -->
        <div id="monthly" class="hidden">
            <select id="monthly_year" onchange="updateMonthlyData()">
                <!-- Options will be populated by JavaScript -->
            </select>
            <div id="monthly_view_buttons">
                <button type="button" onclick="showMonthlyTable()">Table View</button>
                <button type="button" onclick="showMonthlyGraph()">Graph View</button>
            </div>
            <div id="monthly_table"></div>
            <div id="monthly_graph" class="hidden">
                <canvas id="chartjs_line"></canvas>
            </div>
        </div>
    </div>

    <!-- JavaScript and Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        populateDropdowns();
        toggleViews();
        showDailyTable();
        showMonthlyTable();
    });

    function populateDropdowns() {
    fetch('data_fetcher.php?action=years')
        .then(response => response.json())
        .then(years => {
            const dailyYearSelect = document.getElementById('daily_year');
            const monthlyYearSelect = document.getElementById('monthly_year');
            years.forEach(year => {
                dailyYearSelect.add(new Option(year, year));
                monthlyYearSelect.add(new Option(year, year));
            });

            // Populate months for daily view
            const dailyMonthSelect = document.getElementById('daily_month');
            const months = {
                1: 'January', 2: 'February', 3: 'March',
                4: 'April', 5: 'May', 6: 'June',
                7: 'July', 8: 'August', 9: 'September',
                10: 'October', 11: 'November', 12: 'December'
            };
            for (const [key, value] of Object.entries(months)) {
                dailyMonthSelect.add(new Option(value, key));
            }
        });
}


    function updateDailyData() {
        const year = document.getElementById('daily_year').value;
        const month = document.getElementById('daily_month').value;
        fetch(`data_fetcher.php?type=daily&year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('daily_table').innerHTML = `<table>
                    <thead>
                        <tr><th>Date</th><th>Expense</th><th>Income</th><th>Profit</th></tr>
                    </thead>
                    <tbody>${data.table}</tbody>
                </table>`;
                createChart('chartjsd_line', data.labels, data.expenses, data.incomes, data.profits);
            });
    }

    function updateMonthlyData() {
    const year = document.getElementById('monthly_year').value;
    fetch(`data_fetcher.php?type=monthly&year=${year}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            document.getElementById('monthly_table').innerHTML = `<table>
                <thead>
                    <tr><th>Month</th><th>Expense</th><th>Income</th><th>Profit</th></tr>
                </thead>
                <tbody>${data.table}</tbody>
            </table>`;
            createChart('chartjs_line', data.labels, data.expenses, data.incomes, data.profits);
        })
        .catch(error => console.error('Error fetching data:', error));
}


    function createChart(chartId, labels, expenses, incomes, profits) {
        var ctx = document.getElementById(chartId).getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Expenses',
                    backgroundColor: '#e74c3c',
                    borderColor: '#e74c3c',
                    data: expenses,
                    fill: false
                }, {
                    label: 'Income',
                    backgroundColor: '#3498db',
                    borderColor: '#3498db',
                    data: incomes,
                    fill: false
                }, {
                    label: 'Profits',
                    backgroundColor: '#67ec25',
                    borderColor: '#67ec25',
                    data: profits,
                    fill: false
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontColor: '#333',
                        fontFamily: 'Arial',
                        fontSize: 14
                    }
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Month/Date',
                            font: {
                                weight: 'bold',
                                size: 25
                            }
                        },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Amount ( kyats )',
                            font: {
                                weight: 'bold',
                                size: 14,
                                family: 'Arial',
                                color: '#33FF57'
                            }
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return value.toLocaleString();
                            },
                            suggestedMin: Math.min(...profits) < 0 ? Math.min(...profits) : 0
                        }
                    }]
                }
            }
        });
    }

    function toggleViews() {
    var viewType = document.getElementById('view_type').value;
    var dailyView = document.getElementById('daily');
    var monthlyView = document.getElementById('monthly');

    if (viewType === 'daily') {
        dailyView.classList.remove('hidden');
        monthlyView.classList.add('hidden');
        updateDailyData(); // Ensure daily data is updated when switching to daily view
    } else if (viewType === 'monthly') {
        dailyView.classList.add('hidden');
        monthlyView.classList.remove('hidden');
        updateMonthlyData(); // Ensure monthly data is updated when switching to monthly view
    }
}


    function showDailyTable() {
        document.getElementById('daily_table').classList.remove('hidden');
        document.getElementById('daily_graph').classList.add('hidden');
        setActiveButton('daily_table');
    }

    function showDailyGraph() {
        document.getElementById('daily_table').classList.add('hidden');
        document.getElementById('daily_graph').classList.remove('hidden');
        setActiveButton('daily_graph');
    }

    function showMonthlyTable() {
        document.getElementById('monthly_table').classList.remove('hidden');
        document.getElementById('monthly_graph').classList.add('hidden');
        setActiveButton('monthly_table');
    }

    function showMonthlyGraph() {
        document.getElementById('monthly_table').classList.add('hidden');
        document.getElementById('monthly_graph').classList.remove('hidden');
        setActiveButton('monthly_graph');
    }

    function setActiveButton(view) {
        var dailyButtons = document.querySelectorAll('#daily_view_buttons button');
        var monthlyButtons = document.querySelectorAll('#monthly_view_buttons button');

        dailyButtons.forEach(button => button.classList.remove('active'));
        monthlyButtons.forEach(button => button.classList.remove('active'));

        if (view === 'daily_table') {
            document.querySelector('#daily_view_buttons button[onclick="showDailyTable()"]').classList.add('active');
        } else if (view === 'daily_graph') {
            document.querySelector('#daily_view_buttons button[onclick="showDailyGraph()"]').classList.add('active');
        }

        if (view === 'monthly_table') {
            document.querySelector('#monthly_view_buttons button[onclick="showMonthlyTable()"]').classList.add('active');
        } else if (view === 'monthly_graph') {
            document.querySelector('#monthly_view_buttons button[onclick="showMonthlyGraph()"]').classList.add('active');
        }
    }
    </script>
</body>
</html>

