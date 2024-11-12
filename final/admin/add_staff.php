<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

// Add Staff
if (isset($_POST['addStaff'])) {
    // Prevent Posting Blank Values
    if (empty($_POST["staff_name"]) || empty($_POST['contact_number']) || empty($_POST['address']) || empty($_POST['position']) || empty($_POST['date_hired']) || empty($_POST['salary'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $staff_name = $_POST['staff_name'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];
        $position = $_POST['position'];
        $date_hired = $_POST['date_hired'];
        $active = isset($_POST['active']) ? 1 : 0;
        $salary = $_POST['salary'];



        // Insert Captured information to a database table
        $postQuery = "INSERT INTO staff (staff_name, contact_number, address, position, date_hired, active, salary) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $postStmt = $mysqli->prepare($postQuery);

        if ($postStmt) {
            $postStmt->bind_param('sssssii',$staff_name, $contact_number, $address, $position, $date_hired, $active, $salary); // 'i' for integer

            if ($postStmt->execute()) {
                $success = "Staff Added";
                header("refresh:1; url=staffs.php");
            } else {
                $err = "Please Try Again Or Try Later";
            }

            $postStmt->close();
        } else {
            $err = "Prepare statement failed: " . $mysqli->error;
        }
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
        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
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
                        <div class="card-header border-0">
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Staff Name</label>
                                        <input type="text" name="staff_name" class="form-control" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Contact Number</label>
                                        <input type="phone"pattern="^(09|\+?959)(2|4|5[6-9]|7[5-9]|8|9|6[7-9])\d{7,8}$|^01\d{7}$|^063\d{6,7}$" name="contact_number" class="form-control" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Position</label>
                                        <input type="text" name="position" class="form-control" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Date Hired</label>
                                        <input type="date" name="date_hired" class="form-control" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                    <label style=" margin-right: 25px; ">Active</label>
                                        <input type="checkbox" name="active" class="form-check-input" checked>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Salary</label>
                                        <input type="number" name="salary" class="form-control" step="0.01" value="">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="addStaff" value="Add Staff" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
