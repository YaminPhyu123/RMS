<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

$con = mysqli_connect("localhost", "root", "", "project");
if (!$con) {
    echo "Problem in database connection! Contact administrator!" . mysqli_error();
}

require_once('partials/_head.php');
?>
<style>
 
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0px;
    }

    th{
        background-color: #263748;
    color: white;
        padding: 8px;
        text-align: left; 
        border-bottom: none; 
    }

    tr{
        background-color:green;
        color: white;
        padding: 8px;
        text-align: left; 
        border-bottom: none;
    }

   
    td {
        padding: 8px;
        border-bottom: none; 
    }

    .img-responsive {
        border-radius: 50%;
        border: 2px solid #ddd;
        padding: 5px; 
    }
    .img-curve {
    border-radius: 50%;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.img-curve:hover {
    transform: scale(1.1);
    opacity: 0.8; 
}

    .card {
        margin-top: 20px;
        border-radius: 10px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: none;
        padding: 10px 20px;
    }

    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-outline-success:hover {
        background-color: #28a745;
        color: #fff;
    }

    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Delightful:wght@700&display=swap');

    .page-header {
        font-family: 'Delightful', serif;
        font-size: 2.5em;
        font-weight: 700;
        text-align: center;
        color: darkgreen;
    }

    .page-header span {
        display: inline-block;
        position: relative;
    }

    .page-header span:before {
        content: "";
        position: absolute;
        width: 100%;
        height: 5px;
        bottom: 0;
        left: 0;
        background-color: #28a745;
        visibility: hidden;
        transform: scaleX(0);
        transition: all 0.3s ease-in-out 0s;
    }

    .page-header span:hover:before {
        visibility: visible;
        transform: scaleX(1);
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
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
        
        </div>
        <div class="container-fluid mt--8">
    
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                        <button class="btn btn-outline-success ml-2" onclick="showAllItems()">Show over all</button>

                            <h2 class="page-header text-center"><span>Top 5 Best-Selling Foods </span></h2>
                            <div class="text-center mt-4">
                                <label for="period">Select Period:</label>
                                <select class="form-control" id="period" onchange="fetchTopSelling(this.value)">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <!-- Additional inputs for specific day, month, or year -->
                                <div id="specific-inputs" class="mt-3">
                                    <!-- Input for specific day -->
                                    <div id="day-input" style="display: none;">
                                        <label for="day">Select Day:</label>
                                        <input type="date" class="form-control" id="day" onchange="fetchTopSelling('daily', this.value)">
                                    </div>
                                    <!-- Input for specific month -->
                                    <div id="month-input" style="display: none;">
                                        <label for="month">Select Month:</label>
                                        <input type="month" class="form-control" id="month" onchange="fetchTopSelling('monthly', this.value)">
                                    </div>
                                    <!-- Input for specific year -->
                                    <div id="year-input" style="display: none;">
                                        <label for="year">Select Year:</label>
                                        <input type="number" class="form-control" id="year" onchange="fetchTopSelling('yearly', this.value)">
                                    </div>
                                </div>
                            </div>
                          
                        </div>
                        <!-- Top 5 Best-Selling Foods Table -->
                        <div class="table-responsive">
                            <table class="table table-flush">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Food Name</th>
                                        <th>Image</th>
                                        <th>Total Quantity Sold</th>
                                    </tr>
                                </thead>
                                <tbody id="top-selling-body">
                                    <?php
                                    // Default query for top 5 best-selling foods
                                    $food_query = "SELECT f.food_name, f.image, SUM(so.quantity) AS total_quantity
                                                    FROM selection_order so
                                                    INNER JOIN foods f ON so.food_id = f.food_id
                                                    INNER JOIN transaction t ON so.t_id = t.tid
                                                    WHERE t.payment_status = 'Paid'
                                                    GROUP BY so.food_id
                                                    ORDER BY total_quantity DESC
                                                    LIMIT 10";
                                    $food_result = mysqli_query($con, $food_query);
                                    $serial_number = 1;
                                    while ($food_row = mysqli_fetch_array($food_result)) {
                                        $food_name = $food_row['food_name'];
                                        $image_name = $food_row['image'];
                                        $total_quantity = $food_row['total_quantity'];

                           
                                        echo "<tr>
                                                <td>{$serial_number}</td>
                                                <td>{$food_name}</td>
                                                <td><img src='assets/img/menu/{$image_name}' alt='{$food_name}'
                                                        class='img-responsive img-curve' width='100px' height='100px'>
                                                </td>
                                                <td>{$total_quantity}</td>
                                              </tr>";
                                        $serial_number++; 
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    function fetchTopSelling(period, specificValue = null) {
        var xhr = new XMLHttpRequest();
        var url = 'fetch_best_selling.php?period=' + period;
        
        if (specificValue !== null) {
            url += '&value=' + specificValue;
        }
        
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById('top-selling-body').innerHTML = this.responseText;
            }
        }
        xhr.send();
    }

  
    document.getElementById('period').addEventListener('change', function () {
        var period = this.value;
        var specificInputs = document.getElementById('specific-inputs');

    
        document.getElementById('day-input').style.display = 'none';
        document.getElementById('month-input').style.display = 'none';
        document.getElementById('year-input').style.display = 'none';

       
        switch (period) {
            case 'daily':
                document.getElementById('day-input').style.display = 'block';
                break;
            case 'monthly':
                document.getElementById('month-input').style.display = 'block';
                break;
            case 'yearly':
                document.getElementById('year-input').style.display = 'block';
                break;
            default:
                break;
        }
    });
    function showAllItems() {
            fetchTopSelling('all');
        }
</script>
<?php require_once('partials/_scripts.php'); ?>
    </body>
</html>
