<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current date and time
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

// SQL query to retrieve reserved dishes that are to be served after 30 minutes
$sql = "
SELECT f.food_name, so.quantity, t.tid as transactionid, r.special_requests 
FROM selection_order AS so 
JOIN transaction AS t ON so.t_id = t.tid
JOIN reservations AS r ON so.r_id = r.id
JOIN foods AS f ON so.food_id = f.food_id
WHERE r.reservation_date = '$currentDate'
AND r.reservation_time BETWEEN DATE_SUB(CURTIME(), INTERVAL 30 MINUTE) AND DATE_ADD(CURTIME(), INTERVAL 30 MINUTE)
AND t.payment_status = 'paid'
AND r.reserve_status='pending'
ORDER BY t.tid"; // Ordering by transaction ID

$result = $conn->query($sql);

// Check if query ran successfully
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Count the total number of reserved dishes
$reservedCount = $result->num_rows;

// Initialize variables for rowspan logic
$transactionRows = [];
$lastTransactionID = null;
$rowCount = 0;

// Group rows by transaction ID
if ($reservedCount > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactionRows[] = $row;
        if ($row['transactionid'] === $lastTransactionID) {
            $rowCount++;
        } else {
            if ($lastTransactionID !== null) {
                $transactionRows[count($transactionRows) - $rowCount - 1]['rowspan'] = $rowCount + 1;
            }
            $lastTransactionID = $row['transactionid'];
            $rowCount = 0;
        }
    }
    // Set rowspan for the last group
    $transactionRows[count($transactionRows) - $rowCount - 1]['rowspan'] = $rowCount + 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Dishes</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/argon.css?v=1.0.0">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .card {
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-image: url('assets/img/theme/restro00.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 20px 0;
        }
    </style>
</head>
<body>
<!-- Sidenav -->
<?php require_once('partials/_sidebar.php'); ?>

<!-- Main content -->
<div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div class="header pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
        <!-- All Reservations -->
        <div class="row mt-5">
            <div class="col">
                <div class="card shadow">
                    <h2>Reserved Dishes</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Food Name</th>
                                <th>Quantity</th>
                                <th>Special Requests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($transactionRows as $index => $row) {
                                echo "<tr>";
                                echo "<td>{$row['food_name']}</td>";
                                echo "<td>{$row['quantity']}</td>";
                                
                                // Display Special Requests only if rowspan is set
                                if (isset($row['rowspan'])) {
                                    echo "<td style='test:center;'rowspan='{$row['rowspan']}'>{$row['special_requests']}</td>";
                                }

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('partials/_scripts.php'); ?>
</body>
</html>
