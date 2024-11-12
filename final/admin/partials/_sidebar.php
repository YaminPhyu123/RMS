<?php
$admin_id = $_SESSION['admin_id'];
//$login_id = $_SESSION['login_id'];
$ret = "SELECT * FROM  system_admin  WHERE admin_id = '$admin_id'";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($admin = $res->fetch_object()) {

?>
  <nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
      <!-- Toggler -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Brand -->
      <!-- <a class="navbar-brand pt-0" href="dashboard.php">
        <img src="assets/img/brand/repos.png" class="navbar-brand-img" alt="...">
      </a> -->
      <!-- User -->
      <ul class="nav align-items-center d-md-none">
        <li class="nav-item dropdown">
          <a class="nav-link nav-link-icon" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="ni ni-bell-55"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right" aria-labelledby="navbar-default_dropdown_1">
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle">
                <img alt="Image placeholder" src="assets/img/theme/team-1-800x800.jpg">
              </span>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
            <div class=" dropdown-header noti-title">
              <h6 class="text-overflow m-0">Welcome!</h6>
            </div>
            <a href="change_profile.php" class="dropdown-item">
              <i class="ni ni-single-02"></i>
              <span>My profile</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="logout.php" class="dropdown-item">
              <i class="ni ni-user-run"></i>
              <span>Logout</span>
            </a>
          </div>
        </li>
      </ul>
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
       
        <form class="mt-4 mb-3 d-md-none">
          <div class="input-group input-group-rounded input-group-merge">
            <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="Search" aria-label="Search">
            <div class="input-group-prepend">
              <div class="input-group-text">
                <span class="fa fa-search"></span>
              </div>
            </div>
          </div>
        </form>
        <!-- Navigation -->
        <ul class="navbar-nav">
          
          <li class="nav-item">
            <a class="nav-link" href="staffs.php">
              <i class="fas fa-user-tie text-primary"></i> Manage Staff
            </a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="worktime.php">
          
            <i class="fas fa-clock text-primary"></i>Employee Work Records
            </a>
          </li> -->

          <li class="nav-item">
            <a class="nav-link" href="menu.php">
              <i class="ni ni-bullet-list-67 text-primary"></i>Menu
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="category.php">
            <i class="fas fa-th-large text-primary"></i>Category
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="orders.php">
              <i class="ni ni-cart text-primary"></i> Order Details
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="reservation.php">
            <i class="fas fa-calendar-check text-primary"></i>Reservations
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="ActiveTables.php">
            <i class="fas fa-table text-success"></i>Active Table Management
            </a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="predishes.php">
            <i class="fas fa-utensils text-primary"></i>pre-order dishes
            </a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link" href="wavechart.php">
            <i class="far fa-calendar-alt text-primary"></i>Monthly/Daily Lists
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="bestSelling.php">
            <i class="fas fa-star text-warning"></i>Best Selling Menu
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage_item.php">
              <i class="fas fa-file-invoice-dollar text-primary"></i> Manage Items
            </a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="salary.php">
            <i class="fas fa-dollar-sign text-primary"></i>Monthly Salary
            </a>
          </li> -->
        </ul>
        <!-- Divider -->
       
        <!-- Heading -->
       
        <ul class="navbar-nav mb-md-3">
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="fas fa-sign-out-alt text-danger"></i> Log Out
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<?php } ?>