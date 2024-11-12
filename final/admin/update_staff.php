<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

// Update Staff
if (isset($_POST['UpdateStaff'])) {
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
        $update = $_GET['update']; // Assuming 'update' is passed via GET

        // Update Captured information in the database table
        $postQuery = "UPDATE staff SET staff_name=?, contact_number=?, address=?, position=?, date_hired=?, active=?, salary=? WHERE staff_id=?";
        $postStmt = $mysqli->prepare($postQuery);

        if ($postStmt) {
            $postStmt->bind_param('ssssssii', $staff_name, $contact_number, $address, $position, $date_hired, $active, $salary, $update); // 's' for string, 'i' for integer

            if ($postStmt->execute()) {
                $success = "Staff Updated";
                header("refresh:1; url=staffs.php");
                exit(); // Ensure no further code is executed
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

        <!-- Fetch staff details for update -->
        <?php
        $update = $_GET['update']; // Assuming 'update' is passed via GET
        $ret = "SELECT * FROM staff WHERE staff_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $update);
        $stmt->execute();
        $res = $stmt->get_result();
        $staff = $res->fetch_object();
        ?>

        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
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
            <!-- Form -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Update Staff Details</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Staff Name</label>
                                        <input type="text" name="staff_name" class="form-control" value="<?php echo htmlspecialchars($staff->staff_name); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control" value="<?php echo htmlspecialchars($staff->contact_number); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($staff->address); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Position</label>
                                        <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($staff->position); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Date Hired</label>
                                        <input type="date" name="date_hired" class="form-control" value="<?php echo htmlspecialchars($staff->date_hired); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label style=" margin-right: 25px; ">Active</label>
                                        <input type="checkbox" name="active" class="form-check-input" <?php echo $staff->active ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Salary</label>
                                        <input type="number" name="salary" class="form-control" step="0.01" value="<?php echo htmlspecialchars($staff->salary); ?>">
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="UpdateStaff" value="Update Staff" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
     
        </div>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
