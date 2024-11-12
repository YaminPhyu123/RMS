<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['calculate'])) {
    $manager_salary = $_POST['managerSalary'];
    $staff = $_POST['staff']; 

    $staff_salary = $_POST['staffSalary']; 
    $time = date("Y-m-d");
     
    //calculate total salary
    $salary = $staff * $staff_salary;
    $total = $manager_salary + $salary;
    
    // Update daily_record table
    $postQuery = "UPDATE daily_record SET expense = expense + ? WHERE date = ?";
    $postStmt = $mysqli->prepare($postQuery);

    if ($postStmt) {
        $postStmt->bind_param("is", $total, $time); // 's' for string, 'i' for integer

        if ($postStmt->execute()) {
            $success = "Salary Updated";
            header("refresh:1; url=salary.php");
            exit(); // Ensure no further code is executed
        } else {
            $err = "Please Try Again Or Try Later";
        }

        $postStmt->close();
    } else {
        $err = "Prepare statement failed: " . $mysqli->error;
    }
}

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>
        <!-- Page content -->
        <!-- <div class="container-fluid mt--8"> -->
            
                <!-- Header -->
                <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
                    <span class="mask bg-gradient-dark opacity-8"></span>
                    <div class="container-fluid">
                        <div class="header-body">
                        </div>
                    </div>
                </div>
                <!-- Page content -->
                <div class="container-fluid mt--8">
                    <!-- Table -->
                    <div class="row">
                        <div class="col">
                            <div class="card shadow">
                                <!-- <div class="card-header border-0">
                                    <h3>Update Item</h3>
                                </div> -->
                                <div class="card-body">
                                    <!-- Form -->
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Salary For Manager</label>
                                                <input type="text" value="" name="managerSalary" class="form-control" >
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Number of Staff</label>
                                                <input type="text" value="" name="staff" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Salary For One Staff</label>
                                                <input type="text" value="" name="staffSalary" class="form-control">
                                            </div>
                                        </div>
                                       
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <input type="submit" name="calculate" value="Calculate Salary" class="btn btn-success">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <!-- </div> -->
            </div>
            <!-- Argon Scripts -->
            <?php require_once('partials/_scripts.php'); ?>
        </body>
        </html>