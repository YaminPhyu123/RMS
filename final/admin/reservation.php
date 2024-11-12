<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Handle updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $num_guests = $_POST['num_guests'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $special_requests = $_POST['special_requests'];
    $pre_choose = $_POST['pre_choose'];
    $reserve_status = $_POST['reserve_status'];



    if ($reserve_status == 'confirmed' && $pre_choose == 'yes') {
        // Find an available table
        $sqlreserved = "SELECT TableID FROM tables WHERE  table_status='reserved'";
        $resultreserved = $conn->query($sqlreserved);
               if ($resultreserved->num_rows > 0) {
        $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and SeatingCapacity>=$num_guests and table_status='reserved'  ORDER BY
    SeatingCapacity LIMIT 1";}else{
          $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and SeatingCapacity>=$num_guests   ORDER BY
    SeatingCapacity LIMIT 1";
    }
        $result = $conn->query($sqlAvailableTable);
        if ($result->num_rows > 0) {

          
            $availableTable = $result->fetch_assoc();
            $tableId = $availableTable['TableID'];
            
            $sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
            $stmt = $conn->prepare($sqlupdate);
            $stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
            $stmt->execute();
            $stmt->close();

            // Assign the available table to the reservation
            $sqlAssignTable = "UPDATE transaction t
                               JOIN selection_order so ON so.t_id = t.tid
                               SET t.tableId = ?
                               WHERE so.r_id = ?";
            $stmt = $conn->prepare($sqlAssignTable);
            $stmt->bind_param('ii', $tableId, $id);
            $stmt->execute();
            $stmt->close();

            // Mark the table as unavailable
            $sqlUpdateTable = "UPDATE tables SET IsAvailable = 0 WHERE TableID = ?";
            $stmt = $conn->prepare($sqlUpdateTable);
            $stmt->bind_param('i', $tableId);
            $stmt->execute();
            $stmt->close();

              //table reserved remove
              $sqlUpdateTable = "UPDATE tables SET table_status= 'not_reserved' WHERE TableID = ?";
              $stmt = $conn->prepare($sqlUpdateTable);
              $stmt->bind_param('i', $tableId);
              $stmt->execute();
              $stmt->close();
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'assigned_table_id' => $tableId]);
    } else {

    //sa
    $sqlreserved = "SELECT TableID FROM tables WHERE  table_status='reserved'";
    $resultreserved = $conn->query($sqlreserved);
           if ($resultreserved->num_rows > 0) {
    $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and table_status='reserved'  ORDER BY
    SeatingCapacity DESC LIMIT 1";}
    else{
            $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1   ORDER BY   SeatingCapacity DESC LIMIT 1";}

    
        $result = $conn->query($sqlAvailableTable);
        if ($result->num_rows > 0) {
            $availableTable = $result->fetch_assoc();
            $tableId = $availableTable['TableID'];
            
            $sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
            $stmt = $conn->prepare($sqlupdate);
            $stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
            $stmt->execute();
            $stmt->close();

            // Assign the available table to the reservation
            $sqlAssignTable = "UPDATE transaction t
                               JOIN selection_order so ON so.t_id = t.tid
                               SET t.tableId = ?
                               WHERE so.r_id = ?";
            $stmt = $conn->prepare($sqlAssignTable);
            $stmt->bind_param('ii', $tableId, $id);
            $stmt->execute();
            $stmt->close();

            // Mark the table as unavailable
            $sqlUpdateTable = "UPDATE tables SET IsAvailable = 0 WHERE TableID = ?";
            $stmt = $conn->prepare($sqlUpdateTable);
            $stmt->bind_param('i', $tableId);
            $stmt->execute();
            $stmt->close();

              //table reserved remove
              $sqlUpdateTable = "UPDATE tables SET table_status= 'not_reserved' WHERE TableID = ?";
              $stmt = $conn->prepare($sqlUpdateTable);
              $stmt->bind_param('i', $tableId);
              $stmt->execute();
              $stmt->close();
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'assigned_table_id' => $tableId]);
    }
       //asone
    }
    exit;
}else{
      
    $sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
    $stmt = $conn->prepare($sqlupdate);
    $stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
    $stmt->execute();
    $stmt->close();
}




if ($reserve_status == 'confirmed' && $pre_choose == 'no') {
    // Find an available table
    $sqlreserved = "SELECT TableID FROM tables WHERE  table_status='reserved'";
    $resultreserved = $conn->query($sqlreserved);
           if ($resultreserved->num_rows > 0) {
    $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and SeatingCapacity>=$num_guests and table_status='reserved'  ORDER BY
SeatingCapacity LIMIT 1";}else{
      $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and SeatingCapacity>=$num_guests   ORDER BY
SeatingCapacity LIMIT 1";
}
    $result = $conn->query($sqlAvailableTable);
    if ($result->num_rows > 0) {

      
        $availableTable = $result->fetch_assoc();
        $tableId = $availableTable['TableID'];
        
        $sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
        $stmt->execute();
        $stmt->close();

     

        // Mark the table as unavailable
        $sqlUpdateTable = "UPDATE tables SET IsAvailable = 0 WHERE TableID = ?";
        $stmt = $conn->prepare($sqlUpdateTable);
        $stmt->bind_param('i', $tableId);
        $stmt->execute();
        $stmt->close();

          //table reserved remove
          $sqlUpdateTable = "UPDATE tables SET table_status= 'not_reserved' WHERE TableID = ?";
          $stmt = $conn->prepare($sqlUpdateTable);
          $stmt->bind_param('i', $tableId);
          $stmt->execute();
          $stmt->close();
    
    $conn->commit();
    echo json_encode(['status' => 'success', 'assigned_table_id' => $tableId]);
} else {

//sa
$sqlreserved = "SELECT TableID FROM tables WHERE  table_status='reserved'";
$resultreserved = $conn->query($sqlreserved);
       if ($resultreserved->num_rows > 0) {
$sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1 and table_status='reserved'  ORDER BY
SeatingCapacity DESC LIMIT 1";}else{
   $sqlAvailableTable = "SELECT TableID FROM tables WHERE IsAvailable = 1   ORDER BY
SeatingCapacity DESC LIMIT 1"; 
}
    $result = $conn->query($sqlAvailableTable);
    if ($result->num_rows > 0) {
        $availableTable = $result->fetch_assoc();
        $tableId = $availableTable['TableID'];
        
        $sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
        $stmt = $conn->prepare($sqlupdate);
        $stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
        $stmt->execute();
        $stmt->close();

        // Mark the table as unavailable
        $sqlUpdateTable = "UPDATE tables SET IsAvailable = 0 WHERE TableID = ?";
        $stmt = $conn->prepare($sqlUpdateTable);
        $stmt->bind_param('i', $tableId);
        $stmt->execute();
        $stmt->close();

          //table reserved remove
          $sqlUpdateTable = "UPDATE tables SET table_status= 'not_reserved' WHERE TableID = ?";
          $stmt = $conn->prepare($sqlUpdateTable);
          $stmt->bind_param('i', $tableId);
          $stmt->execute();
          $stmt->close();
    
    $conn->commit();
    echo json_encode(['status' => 'success', 'assigned_table_id' => $tableId]);
}
   //asone
}
exit;
}else{
  
$sqlupdate = "UPDATE reservations SET name=?, phone=?, num_guests=?, reservation_date=?, reservation_time=?, special_requests=?, pre_choose=?, reserve_status=? WHERE id=?";
$stmt = $conn->prepare($sqlupdate);
$stmt->bind_param('ssisssssi', $name, $phone, $num_guests, $reservation_date, $reservation_time, $special_requests, $pre_choose, $reserve_status, $id);
$stmt->execute();
$stmt->close();
}
// Commit transaction
$conn->commit();
echo json_encode(['status' => 'success']);
exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}

$sql = "SELECT * FROM reservations";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql .= " WHERE name LIKE '%$search%' OR phone LIKE '%$search%' OR id IN (SELECT DISTINCT r.id FROM `reservations` r 
JOIN selection_order so ON so.r_id=r.id join transaction tr on so.t_id=tr.tid WHERE tr.tid ='$search') ";
}
$sql .= " ORDER BY 
            CASE 
                WHEN reservation_date = CURDATE() THEN 1 
                WHEN reservation_date > CURDATE() THEN 2 
                ELSE 3 
            END, 
            CASE 
                WHEN reserve_status = 'pending' THEN 1
                WHEN reserve_status = 'comfirmed' THEN 2 
                ELSE 3
            END, 
            reservation_date ASC";
$result = $conn->query($sql);

require_once('partials/_head.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Management System</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            
            margin: 0;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
      
        table {
            width: 100%;
            border-collapse: collapse;
        
        }
       td,th{
        font-size: 14px;
       }
        td {
     
            padding: 7px;
          
            text-align: left;
        }
        th {
            background-color:#263748;
            color: #fff;
            text-align:center;
            
            
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
            transition:background-color 0.3s;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
      
        .btn:hover {
            background-color:black;
        }
       
        form {
            margin: 0;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="time"],
        .form-container select {
            width: 100%;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }

       
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }

    tr.expired {
        background-color: #ffcccc; 
    }
    tr.upcoming {
        background-color: #ccffcc; 
    }
    tr.completed {
        background-color: #2b3a3ab6;

    }
    tr.confirmed {
        background-color: #4899f59c;

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
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                    <!-- Search Form -->
                    <form method="get" action="reservation.php">
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" name="search" placeholder="Search by name, phone, or barcode">
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                            <?php
$sqlcount = "SELECT COUNT(*) AS reservation_count
FROM reservations
WHERE reservation_date = CURDATE() 
    AND reservation_time BETWEEN DATE_SUB(CURTIME(), INTERVAL 30 MINUTE) AND DATE_ADD(CURTIME(), INTERVAL 30 MINUTE)
AND reserve_status = 'pending'";
// Execute the query
$resultcount = $conn->query($sqlcount);
$rowcount = $resultcount->fetch_assoc();
 ?>
                        <div style="float: right;">
    <a class="btn11" style="color: white; text-align: center; display: inline-block; padding: 10px 20px;border-radius: 5px; text-decoration: none;">Upcoming Reservations:</a>
</div>

                            <div style="float:right;">    
                                <a class="btn btn-success" style="color:white"><?php echo $rowcount['reservation_count']?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

       
        <div class="container-fluid mt--8">
        
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">
                        <div class="table-responsive">
                            <table id="reservationsTable">
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Number of Guests</th>
                                    <th>Reservation Date</th>
                                    <th>Reservation Time</th>
                                    <th>Special Requests</th>
                                    <th>Pre Choose</th>
                                    <th>Reservation Status</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                                <?php
                                    while ($row = $result->fetch_assoc()) {
                                        date_default_timezone_set('Asia/Yangon');
                                        
                                        $today_date = date('Y-m-d');
                                        $current_datetime = time();

                                        $reservation_datetime_str = $row['reservation_date'] . ' ' . $row['reservation_time'];
                                        $reservation_datetime= strtotime($reservation_datetime_str);

                                        // $reservation_datetime = strtotime($row['reservation_date'] .$row['reservation_date'] . ' ' . $row['reservation_time']);
                                        $minutes_difference = round(($reservation_datetime - $current_datetime) / 60);
                                        $row_class = '';
                                        if ($row['reservation_date'] == $today_date){ 
                                            if($row['reserve_status'] =='pending') {
                                            if ($minutes_difference>=-30 & $minutes_difference<=30) {
                                            $row_class = 'upcoming';
                                        } else if ($minutes_difference <-30)  {
                                            $row_class = 'expired';
                                        }
                                    }else if ($row['reserve_status'] =='confirmed') {
                                            $row_class = 'confirmed';}
                                }if ($row['reserve_status'] =='completed'||$row['reservation_date'] < $today_date ||($row['reserve_status'] =='confirmed'&& $row['pre_choose'] =='no')) {
                                    $row_class = 'completed';}

                                    $time = new DateTime($row['reservation_time']);
                                    echo "<tr data-id='" . $row['id'] . "' class='" . $row_class . "'>";
                                    echo "<td><span class='view-mode name-view'>" . $row['name'] . "</span><input class='edit-mode' type='text' name='name' value='" . $row['name'] . "' style='display:none;'></td>";
                                    echo "<td><span class='view-mode phone-view'>" . $row['phone'] . "</span><input class='edit-mode' type='text' name='phone' value='" . $row['phone'] . "' style='display:none; width:100px;'></td>";
                                    echo "<td><span class='view-mode num_guests-view'>" . $row['num_guests'] . "</span><input class='edit-mode' type='text' name='num_guests' value='" . $row['num_guests'] . "' style='display:none; width:30px;'></td>";
                                    echo "<td><span class='view-mode reservation_date-view'>" . $row['reservation_date'] . "</span><input class='edit-mode' type='date' name='reservation_date' value='" . $row['reservation_date'] . "' style='display:none; width:100px;'></td>";
                                    echo "<td><span class='view-mode reservation_time-view'>" . $time->format('h:i A')  . "</span><input class='edit-mode' type='time' name='reservation_time' value='" . $row['reservation_time'] . "' style='display:none'></td>";
                                    echo "<td><span class='view-mode special_requests-view'>" . $row['special_requests'] . "</span><input class='edit-mode' type='text' name='special_requests' value='" . $row['special_requests'] . "' style='display:none'></td>";                        
                                    echo "<td><span class='view-mode pre_choose-view'>" . $row['pre_choose'] . "</span><input class='edit-mode' type='text' name='pre_choose' value='" . $row['pre_choose'] . "' style='display:none; width:30px;'></td>";
                                    echo "<td>
                                    <span class='view-mode reserve_status-view'>" . $row['reserve_status'] . "</span>
                                    <select class='edit-mode' name='reserve_status' data-id='" . $row['id'] . "' style='display:none'>
                                        <option value='pending'" . ($row['reserve_status'] == 'pending' ? 'selected' : '') . ">pending</option>
                                        <option value='confirmed'" . ($row['reserve_status'] == 'confirmed' ? 'selected' : '') . ">confirmed</option>
                                        <option value='completed'" . ($row['reserve_status'] == 'complete' ? 'selected' : '') . ">completed</option>
                                    </select>
                                  </td>";
                                    
                                    //payment status
                                    $tid = $row['id'];
                                    $payment_sql = "SELECT t.payment_status FROM transaction t JOIN selection_order so ON t.tid = so.t_id WHERE so.r_id = '$tid'";
                                    $payment_result = $conn->query($payment_sql);
                                    $payment_status = ($payment_result->num_rows > 0) ? $payment_result->fetch_assoc()['payment_status'] : '-';

                                    echo "<td><span class='view-mode'>" . $payment_status . "</span></td>";
                                    echo "<td class='actions'>";
                                    echo "<button type='button' class='edit btn btn-sm btn-primary'><i class='fas fa-edit'></i>Edit</button>";
                                    echo "<button type='button' class='save btn btn-sm btn-success' style='display:none'><i class='fas fa-check-circle'></i>Save</button>";
                                    echo "<form method='POST' style='display:inline;'>";
                                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' name='delete' class='btn btn-sm btn-danger delete-btn'>";
                                    echo "<i class='fas fa-trash'></i>delete</button>"; 
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit').forEach(button => {
            button.addEventListener('click', function() {
                var row = this.closest('tr');
                row.querySelectorAll('.view-mode').forEach(element => element.style.display = 'none');
                row.querySelectorAll('.edit-mode').forEach(element => element.style.display = 'block');
                this.style.display = 'none';

                row.querySelector('.save').style.display = 'inline-block';
            });
        });

        document.querySelectorAll('.save').forEach(button => {
            button.addEventListener('click', function() {
                var row = this.closest('tr');
                var id = row.getAttribute('data-id');
                var name = row.querySelector('input[name="name"]').value;
                var phone = row.querySelector('input[name="phone"]').value;
                var num_guests = row.querySelector('input[name="num_guests"]').value;
                var reservation_date = row.querySelector('input[name="reservation_date"]').value;
                var reservation_time = row.querySelector('input[name="reservation_time"]').value;
                var special_requests = row.querySelector('input[name="special_requests"]').value;
                var pre_choose = row.querySelector('input[name="pre_choose"]').value;
                var reserve_status = row.querySelector('select[name="reserve_status"]').value;
          

                fetch('reservation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update&id=${id}&name=${name}&phone=${phone}&num_guests=${num_guests}&reservation_date=${reservation_date}&reservation_time=${reservation_time}&special_requests=${special_requests}&pre_choose=${pre_choose}&reserve_status=${reserve_status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        row.querySelectorAll('.view-mode').forEach(element => element.style.display = 'block');
                        row.querySelectorAll('.edit-mode').forEach(element => element.style.display = 'none');
                        row.querySelector('.edit').style.display = 'inline-block';
                        this.style.display = 'none';

                        row.querySelector('.name-view').textContent = name;
                        row.querySelector('.phone-view').textContent = phone;
                        row.querySelector('.num_guests-view').textContent = num_guests;
                        row.querySelector('.reservation_date-view').textContent = reservation_date;
                        row.querySelector('.reservation_time-view').textContent = reservation_time;
                        row.querySelector('.special_requests-view').textContent = special_requests;
                        row.querySelector('.pre_choose-view').textContent = pre_choose;
                        row.querySelector('.reserve_status-view').textContent = reserve_status;


                         if (data.assigned_table_id) {
                    alert('Table ' + data.assigned_table_id + ' has been assigned to this reservation.');
                }
                    
                    }
                    else if (data.status === 'error') {
                alert(data.message);}
                    // alert('Reservation updated successfully');
                    location.reload();
                });
            });
        });

        document.querySelectorAll('.reserve_status').forEach(select => {
            select.addEventListener('change', function() {
                var id = this.getAttribute('data-id');
                var reserve_status = this.value;
                fetch('reservation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=update_status&id=' + id + '&reserve_status=' + reserve_status
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Status updated successfully');
                    }
                });
            });
        });
        
    </script>
        <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
