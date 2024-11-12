<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Delete Staff
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $adn = "DELETE FROM staff WHERE staff_id = ?";
    $stmt = $mysqli->prepare($adn);
    if ($stmt) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: staffs.php?msg=Deleted"); 
            exit(); 
               } else {
            $stmt->close();
            $err = "Try Again Later";
        }
    } else {
        $err = "Database Error";
    }
}

require_once('partials/_head.php');

$servername = "localhost";
$username = "root";
$password = "";
$database = "project";


$mysqli = new mysqli($servername, $username, $password, $database);


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payroll']) && isset($_POST['token']) && $_POST['token'] === $_SESSION['form_token']) {

    function getTotalSalary($mysqli, $currentMonth, $currentYear) {
        $totalSalary = 0;
        $stmt = $mysqli->prepare("SELECT salary, date_hired FROM staff WHERE active = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $currentDate = new DateTime();
        $startOfMonth = new DateTime($currentYear . '-' . $currentMonth . '-01');
        $endOfMonth = new DateTime($currentYear . '-' . $currentMonth . '-01');
        $endOfMonth->modify('last day of this month');
        
        while ($staff = $result->fetch_assoc()) {
            $salary = $staff['salary'];
            $dateHired = new DateTime($staff['date_hired']);
            
            // Calculate days between current date and hiring date
            $daysSinceHired = $currentDate->diff($dateHired)->days;

            // Check if hired in the current month
            if ($dateHired >= $startOfMonth && $dateHired <= $endOfMonth) {
               
                if ($daysSinceHired >= 15) {
                    $totalSalary += $salary;
                } else {
                    $totalSalary += $salary / 2;
                   
                }
            } else {
                $totalSalary += $salary; 
            }
        }
        
        $stmt->close();
        return $totalSalary;
    }

    //a payroll record for the current month already exists or not
    function payrollExists($mysqli, $currentMonth, $currentYear) {
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM payroll WHERE MONTH(pay_date) = ? AND YEAR(pay_date) = ?");
        $stmt->bind_param("ii", $currentMonth, $currentYear);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    // add a payroll record with the total salary
    function addPayrollRecord($mysqli, $totalSalary) {
        $stmt = $mysqli->prepare("INSERT INTO payroll (salary, pay_date) VALUES (?, ?)");
        $payDate = date('Y-m-d');
        $stmt->bind_param("ds", $totalSalary, $payDate);
        return $stmt->execute();
    }

    // update the expense in daily_record
    function updateDailyRecordExpense($mysqli, $totalSalary) {
        $payDate = date('Y-m-d');
        // Check if record for today exists
        $stmt = $mysqli->prepare("SELECT d_id, expense FROM daily_record WHERE date = ?");
        $stmt->bind_param("s", $payDate);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Record exist add the expense
            $stmt->bind_result($d_id, $currentExpense);
            $stmt->fetch();
            $newExpense = $currentExpense + $totalSalary;
            $stmt->close();

            $stmt = $mysqli->prepare("UPDATE daily_record SET expense = ? WHERE date = ?");
            $stmt->bind_param("ds", $newExpense, $payDate);
        } else {
            // Record does not exist, insert a new record
            $stmt->close();
            $stmt = $mysqli->prepare("INSERT INTO daily_record (income, expense, profit, date) VALUES (0, ?, 0, ?)");
            $stmt->bind_param("ds", $totalSalary, $payDate);
        }
        
        return $stmt->execute();
    }

    $currentMonth = date('m');
    $currentYear = date('Y');

    if (!payrollExists($mysqli, $currentMonth, $currentYear)) {
        $totalSalary = getTotalSalary($mysqli, $currentMonth, $currentYear);
        if (addPayrollRecord($mysqli, $totalSalary)) {
            if (updateDailyRecordExpense($mysqli, $totalSalary)) {
                $messages="<p>Payroll  successful.</p>";
            
            }
        } 
    } else {
        
        $messagee= "<p>Payroll already exists.</p>";
    }

    
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}


function getPayrollRecords($mysqli) {
    $sql = "SELECT * FROM payroll ORDER BY pay_date DESC";
    return $mysqli->query($sql);
}

$payrollList = getPayrollRecords($mysqli);
?>
<style>
    .testcolormessage{
        color:green;
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
                    
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($err)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($err); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
 
<div class="card-header border-0">
<?php if (isset($messages)): ?>
            <div class=" w-100 mb-2 text-right testcolormessage">
                <?php echo $messages; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($messagee)): ?>
            <div class=" w-100 mb-2 text-right">
                <?php echo $messagee; ?>
            </div>
        <?php endif; ?>
 <div class="d-flex justify-content-between align-items-center">
    
        <a href="add_staff.php" class="btn btn-outline-success">
            <i class="fas fa-user-plus"></i> Add New Staff
        </a>

    

        <form method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">
            <button type="submit" name="payroll" class="btn btn-outline-success">
                <i class="fas fa-dollar-sign"></i> Payroll for This Month
            </button>
        </form>
    </div>
</div>


                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <!-- <th scope="col">Id</th> -->
                                        <th scope="col">Name</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Position</th>
                                        <th scope="col">Date Hired</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Salary</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM staff";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($staff = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                           <!-- <td><?php echo htmlspecialchars($staff->staff_id); ?></td> -->
                                            <td><?php echo htmlspecialchars($staff->staff_name); ?></td>
                                            <td><?php echo htmlspecialchars($staff->contact_number); ?></td>
                                            <td><?php echo htmlspecialchars($staff->address); ?></td>
                                            <td><?php echo htmlspecialchars($staff->position); ?></td>
                                            <td><?php echo htmlspecialchars($staff->date_hired); ?></td>
                                            <td><?php echo $staff->active ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo htmlspecialchars($staff->salary); ?></td>
                                            <td>
                                                <a href="staffs.php?delete=<?php echo $staff->staff_id; ?>" onclick="return confirm('Are you sure you want to delete this staff?');">
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </a>
                                                <a href="update_staff.php?update=<?php echo $staff->staff_id; ?>">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-user-edit"></i>
                                                        Update
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
    <!-- <script>
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
    </script> -->
</body>

</html>
