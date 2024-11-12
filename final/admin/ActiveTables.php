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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$conn->begin_transaction();

try {
    // 1. Fetch pending reservations for today that have not been assigned a table
    $reservationQuery = "
        SELECT id, num_guests, reservation_time 
        FROM reservations 
        WHERE reservation_date = CURDATE()
        AND reserve_status = 'pending'
        AND reservation_time BETWEEN DATE_SUB(CURTIME(), INTERVAL 30 MINUTE) AND DATE_ADD(CURTIME(), INTERVAL 30 MINUTE)
        AND assigned = 0
        ORDER BY id";
    
    $reservationsResult = $conn->query($reservationQuery);

    if ($reservationsResult->num_rows > 0) {
        // 2. Loop through reservations
        while ($reservation = $reservationsResult->fetch_assoc()) {
            $reservationId = $reservation['id'];
            $numGuests = $reservation['num_guests'];

            // Fetch available tables with sufficient seating capacity
            $tableQuery = "
                SELECT TableID 
                FROM tables 
                WHERE IsAvailable = 1 
                AND SeatingCapacity >= $numGuests 
                AND table_status <> 'reserved' 
                ORDER BY SeatingCapacity
                LIMIT 1";
            
            $tablesResult = $conn->query($tableQuery);

            if ($tablesResult->num_rows > 0) {
                $table = $tablesResult->fetch_assoc();
                $tableId = $table['TableID'];

                // Update table status to 'reserved'
                $updateTableQuery = "
                    UPDATE tables 
                    SET table_status = 'reserved' 
                    WHERE TableID = $tableId";
                
                if (!$conn->query($updateTableQuery)) {
                    throw new Exception("Error updating table status: " . $conn->error);
                }

                // Update reservation to mark the table as assigned
                $updateReservationQuery = "
                    UPDATE reservations 
                    SET assigned = 1 
                    WHERE id = $reservationId";
                
                if (!$conn->query($updateReservationQuery)) {
                    throw new Exception("Error updating reservation: " . $conn->error);
                }
            } else {
                // Fetch the next best table
                $tableQuery1 = "
                    SELECT TableID 
                    FROM tables 
                    WHERE IsAvailable = 1 
                    AND table_status <> 'reserved' 
                    ORDER BY SeatingCapacity DESC
                    LIMIT 1";
            
                $tablesResult1 = $conn->query($tableQuery1);

                if ($tablesResult1->num_rows > 0) {
                    $table1 = $tablesResult1->fetch_assoc();
                    $tableId1 = $table1['TableID'];

                    // Update table status to 'reserved'
                    $updateTableQuery1 = "
                        UPDATE tables 
                        SET table_status = 'reserved' 
                        WHERE TableID = $tableId1";
                    
                    if (!$conn->query($updateTableQuery1)) {
                        throw new Exception("Error updating table status: " . $conn->error);
                    }

                    // Update reservation to mark the table as assigned
                    $updateReservationQuery1 = "
                        UPDATE reservations 
                        SET assigned = 1 
                        WHERE id = $reservationId";
                    
                    if (!$conn->query($updateReservationQuery1)) {
                        throw new Exception("Error updating reservation: " . $conn->error);
                    }
                }
            }
        }
    }

    // 3. Check if reservations that made tables reserved still match the criteria
    $checkTablesQuery = "
        SELECT TableID, SeatingCapacity
        FROM tables 
        WHERE table_status = 'reserved'";
    
    $reservedTablesResult = $conn->query($checkTablesQuery);

    if ($reservedTablesResult->num_rows > 0) {
        while ($table = $reservedTablesResult->fetch_assoc()) {
            $tableId = $table['TableID'];
            $seatingCapacity = $table['SeatingCapacity'];

            // Check if the reservation still matches the criteria
            $checkReservationQuery = "
                SELECT id 
                FROM reservations 
                WHERE reservation_date = CURDATE()
                AND reserve_status = 'pending'
                AND reservation_time BETWEEN DATE_SUB(CURTIME(), INTERVAL 30 MINUTE) AND DATE_ADD(CURTIME(), INTERVAL 30 MINUTE)
                AND assigned = 1 
                AND num_guests <= $seatingCapacity
                LIMIT 1";
            
            $reservationResult = $conn->query($checkReservationQuery);

            // If no matching reservation is found, update the table status back to 'not_reserved'
            if ($reservationResult->num_rows == 0) {
                $updateTableQuery = "
                    UPDATE tables 
                    SET table_status = 'not_reserved' 
                    WHERE TableID = $tableId";
                
                if (!$conn->query($updateTableQuery)) {
                    throw new Exception("Error reverting table status: " . $conn->error);
                }

                // Also reset the `assigned` status of the reservation
                $resetReservationQuery = "
                    UPDATE reservations 
                    SET assigned = 0 
                    WHERE id = (
                        SELECT id
                        FROM reservations 
                        WHERE assigned = 1 
                        AND num_guests <= $seatingCapacity
                        LIMIT 1
                    )";
                
                if (!$conn->query($resetReservationQuery)) {
                    throw new Exception("Error resetting reservation assigned status: " . $conn->error);
                }
            }
        }
    }

    // Commit the transaction
    $conn->commit();

} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    echo "Failed: " . $e->getMessage();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update1'])) {
    $tId = $_POST['tableId'];
    $s = $_POST['status'];

    $sql = "UPDATE transaction SET payment_status = ? WHERE tableId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $s, $tId);

    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update2'])) {
    $tId = $_POST['tableId'];
    $s = $_POST['status'];

    $sql = "UPDATE transaction SET payment_status = ? WHERE tableId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $s, $tId);

    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finish'])){
    $tid = $_POST['transactionId'];
    $tableid = $_POST['tableId'];

    $sqlfinish = "UPDATE reservations r
    JOIN selection_order so ON so.r_id = r.id
    JOIN transaction t ON t.tid = so.t_id
    SET r.reserve_status = 'completed'
    WHERE t.tid=?";
    $stmt = $conn->prepare($sqlfinish);
    $stmt->bind_param('s', $tid);
    $stmt->execute();

    $sqlactive = "UPDATE tables set isAvailable = 1 where TableID=?";
    $stmt = $conn->prepare($sqlactive);
    $stmt->bind_param('i', $tableid);
    $stmt->execute();

    $sqlfinish = "UPDATE transaction set Finish = 1 where TableID=?";
    $stmt = $conn->prepare($sqlfinish);
    $stmt->bind_param('i', $tableid);
    $stmt->execute();

}



// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
//     $orderId = $_POST['orderId'];
//     $tableId = $_POST['tableId']; 

//     $sqlDelete = "DELETE FROM selection_order WHERE s_id = ?";
//     if ($stmt = $conn->prepare($sqlDelete)) {
//         $stmt->bind_param('i', $orderId);
//         if ($stmt->execute()) {
        
//             $sqlCount = "SELECT COUNT(so.s_id) as order_count 
//                          FROM selection_order so
//                          JOIN transaction t ON so.t_id = t.tid
//                          WHERE t.Finish = 0 AND t.tableId = ?";
//             if ($countStmt = $conn->prepare($sqlCount)) {
//                 $countStmt->bind_param('i', $tableId);
//                 $countStmt->execute();
//                 $countStmt->bind_result($orderCount);
//                 $countStmt->fetch();
//                 $countStmt->close();
                
//                 if ($orderCount < 1) {
//                     $sqlFinish = "DELETE FROM transaction WHERE TableID = ? AND Finish = 0";
//                     if ($updateStmt = $conn->prepare($sqlFinish)) {
//                         $updateStmt->bind_param('i', $tableId);
//                         $updateStmt->execute();
//                         $updateStmt->close();
                        
//                         $sqlA = "UPDATE tables SET isAvailable = 1 WHERE TableID = ?";
//                         if ($stmtTable = $conn->prepare($sqlA)) {
//                             $stmtTable->bind_param('i', $tableId);
//                             $stmtTable->execute();
//                             $stmtTable->close();
//                         }
            
//                     }

//                     echo json_encode(['status1' => 'no_orders']);
//                 } 
//             } 
//         } 
//         $stmt->close();
//     } else {
//         echo json_encode(['status' => 'prepare_error']);
//     }
// }
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
    <title>Table Management</title>
    <style>
    
    /* .update:hover {
    background-color: black;
    color: white;
} */

th {
    background-color: black;
    color: white;
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;

    white-space: nowrap;
}

.table-container {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    max-width: 1200px;
    gap: 50px;
    justify-content: center;
}

.table {
    width: 100px;
    height: 100px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 1.2em;
    cursor: pointer;
    background-color: #3498db;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

/* .table:hover {
    background-color: #2980b9;
} */
.colors {
    display: flex;
    justify-content: space-around; 
    align-items: center; 
    gap: 20px; 
}

.color-item {
    display: flex;
    align-items: center; 
}

.color-block {
    width: 20px; 
    height: 20px; 
    border-radius: 18px; 
    margin-right: 10px; 
   
}

.greenblock {
    background-color: #2ecc71;
}

.redblock {
    background-color: #e74c3c;
}

.yellowblock {
    background-color: #ffef09;
    color: black;
}

.color-text {
    margin-top: 10px;
  
}


.available {
    background-color: #2ecc71;
}

.available:hover {
    background-color: #23b45f;
}
.occupied {
    background-color: #e74c3c;
}

.occupied:hover {
    background-color: #ca3f30;
}

.reserved {
    background-color: #ffbb00;
}

.reserved:hover {
    background-color: #db8d0f;
}
.order-details {
    margin-top: 20px;
}

.order-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.order-form input {
    padding: 5px;
    font-size: 1em;
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

button {
    width: 100px;
    height: 100px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: black;
    font-size: 1.2em;
    cursor: pointer;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #2ecc71;
    border-color: #2ecc71;
    color: black;
}

.edit-form {
    display: none;
}
.edit-btn, .delete-btn{
    margin-right: 5px;
    padding: 7px 7px;
    font-size: 13px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    color: inherit; /* Change color to inherit */
    background-color: #007bff; /* or the color you want for the background */
    border: 1px solid #007bff;
    transition: background-color 0.3s ease;
}

/* .edit-btn:hover {
    background-color: #0056b3;
    border-color: #0056b3;
} */

.delete-btn {
    background-color: #dc3545;
    border: 1px solid #dc3545;
    color: inherit; /* Change color to inherit */
    transition: background-color 0.3s ease;
}

/* .delete-btn:hover {
    background-color: #c82333;
    border-color: #c82333;
}

.delete-btn:hover {
    background-color: #c82333;
    border-color: #c82333;
    color: white;
} */

@media (max-width: 768px) {
    .container {
        padding: 10px;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}

    </style>
<script src="assets/vendor/jquery/dist/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/argon.js?v=1.0.0"></script>
<script src="assets/vendor/chart.js/dist/Chart.min.js"></script>
<script src="assets/vendor/chart.js/dist/Chart.extension.js"></script>
</head>
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
            <form method="get" action="reservation.php">
                        <div class="form-row">
                            <div class="col">
                               
                            </div>
                            <div class="col">
                          
                            </div>
                   <div style="float: right;">
    <a class="btn11" style="color: yellow; text-align: center; display: inline-block; padding: 10px 20px;border-radius: 5px; text-decoration: none;">Total Pre-ordered Dishes: <i class="fas fa-utensils text-warning"></i></a>
</div>

                            <div style="float:right;">  
                                <a href="predishes.php"class="btn" style="color:black;background-color:yellow;border-radius: 100px; "><?php echo $reservedCount; ?></a>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
        <!-- All Reservations -->
        <div class="row mt-5">
            <div class="col">           
                <div class="card shadow">

                <div class="colors">
    <div class="color-item">
        <div class="color-block greenblock"></div>
        <p class="color-text">Available</p>
    </div>
    <div class="color-item">
        <div class="color-block redblock"></div>
        <p class="color-text">Occupied</p>
    </div>
    <div class="color-item">
        <div class="color-block yellowblock"></div>
        <p class="color-text">Reserved</p>
    </div>
</div>


                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "project";

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch tables data with availability status
                    $tables = [];
                    $sql = "SELECT t.TableID, t.table_status,
                    t.IsAvailable, 
                    COUNT(CASE 
                            WHEN tr.payment_status = 'notpaid' THEN so.s_id 
                            WHEN so.r_id IS NOT NULL AND r.reserve_status = 'confirmed' THEN so.s_id 
                          END) AS orderCount
             FROM tables t
             LEFT JOIN transaction tr ON t.TableID = tr.tableId
             LEFT JOIN selection_order so ON tr.tid = so.t_id
             LEFT JOIN reservations r ON r.id = so.r_id
             GROUP BY t.TableID";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $tables[] = $row;
                        }
                    }
                    ?>
                    <br><br>
                    <div class="table-container">
                        <?php foreach ($tables as $table) : ?>
                            <?php
$tableStatusClass = '';
if ($table['table_status'] === 'reserved') {
    $tableStatusClass = 'reserved';
} else {
    $tableStatusClass = $table['IsAvailable'] ? 'available' : 'occupied';
}
?>

                            <div class="table <?php echo $tableStatusClass ?>" data-id="<?php echo $table['TableID']; ?>">
                                <?php echo $table['TableID']; ?>
                                <br>
                                orders: <?php echo $table['orderCount']; ?>
                            </div>
                        <?php endforeach; ?>
                        <button><a href="addnewtable.php">add new table</a></button>
                    </div>
                    <div class="order-details" id="order-details"></div>
                    <script>
                        const tableContainer = document.querySelector('.table-container');
                        const orderDetailsDiv = document.getElementById('order-details');

                        // Event listener for table click
                        document.querySelectorAll('.table').forEach(table => {
                            table.addEventListener('click', function () {
                                const tableId = this.dataset.id;

                                // Fetch orders data for the selected table
                                fetchOrders(tableId);
                            });
                        });

                        // Function to fetch orders data via AJAX
                        function fetchOrders(tableId) {
                            fetch(`fetch_orders.php?tableId=${tableId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.length === 0) {
                                        window.location.reload();
                return; 
            }
                                    showOrderDetails(data);
                                })
                                .catch(error => console.error('Error:', error));
                        }

                        // Function to display order details
                        function showOrderDetails(orders) {
                            let orderFormHTML = `
                                <form class="order-form" method="post" action="ActiveTables.php">
                                    <input type="hidden" name="tableId" value="${orders[0].tableId}">
                                    <input type="hidden" name="transactionId" value="${orders[0].tid}">
                                    <input type="hidden" name="orderId" value="${orders[0].s_id}">

                                    <strong>TRANSACTION ID: ${orders[0].tid}</strong>
                                    <strong>Table Number: ${orders[0].tableId}</strong>
                                    <strong>Payment Status: ${orders[0].payment_status}</strong>
                                    ${orders[0].special_requests ? `<strong>Special Request: ${orders[0].special_requests}</strong>` : ''}
                                    <div style="overflow: hidden;">`;

                            if (orders[0].payment_status === 'notpaid') {
                                orderFormHTML += `
                                    <label name="slabel" style="float: left; margin-right: 10px; line-height: 30px;">Status:
                                        <select name="status" required style="height: 30px;">
                                            <option value="notpaid">Not paid</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </label>
                                `;
                            }

                            if (!orders[0].reservation_id) {
                                if (orders[0].payment_status === 'notpaid') {
                                    orderFormHTML += `
                                        <button type="submit" style="width: 10%; height: 30px; margin-top: 0px;" name="update2" class="update2">Update</button>
                                     
                                    `;
                                } else {
                                    orderFormHTML += `
                                        <button type="submit" style="width: 10%; height: 30px; margin-top: 0px;" name="finish" class="finish">Finish</button>
                                    `;
                                }
                            } else if (orders[0].payment_status === 'notpaid') {
                                orderFormHTML += `
                                    <button type="submit" style="width: 10%; height: 30px; margin-top: 0px;" name="update2" class="update2">Update</button>
                                    <button type="submit" style="width: 10%; height: 30px; margin-top: 0px;" name="finish" class="finish">Finish</button>
                                `;
                            } else {
                                orderFormHTML += `
                                    <button type="submit" style="width: 10%; height: 30px; margin-top: 0px;" name="finish" class="finish">Finish</button>
                                `;
                            }

                            orderFormHTML += `
                                    </div>
                                </form>
                                <hr>
                                <div>
                                    <h3>Order Details</h3>
                                    <table style="width:50%;">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Food Name</th>
                                                <th>Unit Price</th>
                                                <th>Quantity</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            let totalAmount = 0;
                            orders.forEach(order => {
                                totalAmount += order.food_price * order.quantity; // Calculate total amount

                                orderFormHTML += `
                                    <tr>
                                        <td><img src='assets/img/menu/${order.image}' class='img-responsive img-curve' width='100px' height='100px'></td>
                                        <td>${order.food_name}</td>
                                        <td>${order.food_price}</td>
                                        <td>${order.quantity}</td>
                                   
                                         `;
    
                              orderFormHTML += `


                                    </tr>
                                `;
                            });

                            orderFormHTML += `
                             <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <div style="text-align:left; margin-top: 20px;">
                                        <strong>Total: ${totalAmount}</strong>
                                    </div>
                                </td>
                             
                                </tr>
                                   </tbody>
                                </table>
                            `;

                            orderDetailsDiv.innerHTML = orderFormHTML;
                        }
                        function deleteOrder(orderId, tableId) {
        if (confirm('Are you sure you want to delete this dish?')) {
            fetch('ActiveTables.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'delete_order': '1',
                    'orderId': orderId,
                    'tableId': tableId,
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // For debugging: ensure response is received
                fetchOrders(tableId);

                if (data.status === 'no_orders') {
                // If no orders left, refresh the page
                window.location.reload();
            } 
            })
            .catch(error => console.error('Error:', error));
        }
    }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require_once('partials/_scripts.php'); ?>
</body>
</html>
