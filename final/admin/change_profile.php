<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Ensure admin_id is set
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$adminQuery = "SELECT admin_name, admin_email FROM system_admin WHERE admin_id = ?";
$adminStmt = $mysqli->prepare($adminQuery);
$adminStmt->bind_param('i', $admin_id);
$adminStmt->execute();
$adminStmt->bind_result($admin_name, $admin_email);
$adminStmt->fetch();
$adminStmt->close();

// Update Profile
if (isset($_POST['ChangeProfile'])) {
    $admin_name = trim($_POST['admin_name']);
    $admin_email = trim($_POST['admin_email']);

    $Qry = "UPDATE system_admin SET admin_name = ?, admin_email = ? WHERE admin_id = ?";
    $postStmt = $mysqli->prepare($Qry);
    $postStmt->bind_param('ssi', $admin_name, $admin_email, $admin_id);
    if ($postStmt->execute()) {
        $_SESSION['success'] = "Account Updated";
        header("Location: change_profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Please Try Again Or Try Later";
    }
    $postStmt->close();
}

// Change Password
if (isset($_POST['changePassword'])) {
    $error = 0;
    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    if (empty($old_password)) {
        $error = 1;
        $_SESSION['error'] = "Old Password Cannot Be Empty";
    }
    if (empty($new_password)) {
        $error = 1;
        $_SESSION['error'] = "New Password Cannot Be Empty";
    }
    if ($new_password !== $confirm_password) {
        $error = 1;
        $_SESSION['error'] = "Confirmation Password Does Not Match";
    }

    if (!$error) {
        $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

        // Verify the old password
        $sql = "SELECT admin_password FROM system_admin WHERE admin_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!password_verify($old_password, $row['admin_password'])) {
            $_SESSION['error'] = "Please Enter Correct Old Password";
        } else {
            $query = "UPDATE system_admin SET admin_password = ? WHERE admin_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('si', $new_password_hashed, $admin_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Password Changed";
                header("Location:change_profile.php");
                exit();
            } else {
                $_SESSION['error'] = "Please Try Again Or Try Later";
            }
        }
        $stmt->close();
    }
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <div class="header pb-1 pt-5 pt-lg-8 d-flex align-items-center" style="min-height:0px; background-image: url(assets/img/theme/restro00.jpg); background-size: cover; background-position:top;">
      <!-- Mask -->
      <span class="mask bg-gradient-default opacity-8"></span>
      <!-- Header container -->
      <div class="container-fluid d-flex align-items-center">
        <div class="row">
          <div class="col-lg-7 col-md-10">
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <h1 class="display-2 text-white">Hello <?php echo htmlspecialchars($admin_name); ?></h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
          <div class="card card-profile shadow">
            <div class="row justify-content-center">
              <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                  <a href="#">
                    <img src="assets/img/theme/user-a-min.png" class="rounded-circle" alt="Profile Image">
                  </a>
                </div>
              </div>
            </div>
            <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
              <div class="d-flex justify-content-between">
              </div>
            </div>
            <div class="card-body pt-0 pt-md-4">
              <div class="text-center">
                <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                <div class="h5 font-weight-300">
                  <i class="ni location_pin mr-2"></i><?php echo htmlspecialchars($admin_email); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-8 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h3 class="mb-0">My Account</h3>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form method="post">
                <h6 class="heading-small text-muted mb-4">User Information</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-username">User Name</label>
                        <input type="text" name="admin_name" value="<?php echo htmlspecialchars($admin_name); ?>" id="input-username" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="input-email">Email Address</label>
                        <input type="email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" id="input-email" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <input type="submit" name="ChangeProfile" class="btn btn-success form-control-alternative" value="Submit">
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <hr>
              <form method="post">
                <h6 class="heading-small text-muted mb-4">Change Password</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label class="form-control-label" for="input-old-password">Old Password</label>
                        <input type="password" name="old_password" id="input-old-password" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label class="form-control-label" for="input-new-password">New Password</label>
                        <input type="password" name="new_password" id="input-new-password" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <label class="form-control-label" for="input-confirm-password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="input-confirm-password" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="form-group">
                        <input type="submit" name="changePassword" class="btn btn-success form-control-alternative" value="Change Password">
                      </div>
                    </div>
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
