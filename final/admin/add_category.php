<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['addCategory'])) {
    //Prevent Posting Blank Values
    if (empty($_POST["prod_name"]) || empty($_FILES["prod_img"]["name"]) ) {
        $err = "Blank Values Not Accepted";
    } else {
        $prod_name = $_POST['prod_name'];
      
        $status = 1;
       
        $prod_img = $_FILES['prod_img']['name'];
        
        // Move uploaded file to destination directory
        move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/menu/" . $_FILES["prod_img"]["name"]);
        
        // Insert Captured information to a database table
        $postQuery = "INSERT INTO food_category ( category_name, status, category_image ) VALUES (?, ?, ?)";
        $postStmt = $mysqli->prepare($postQuery);
        
        // Check if prepare succeeded
        if ($postStmt) {
            // Bind parameters with types
            $postStmt->bind_param("sis", $prod_name, $status, $prod_img);

            // Execute the statement
            $postStmt->execute();

            // Check if execution succeeded
            if ($postStmt->affected_rows > 0) {
                $success = "Category Added";
                header("refresh:1; url=category.php");
                exit; // Exit to prevent further execution after redirect
            } else {
                $err = "Error executing query: " . $postStmt->error;
            }
        } else {
            $err = "Prepare statement failed: " . $mysqli->error;
        }

        // Close statement
        $postStmt->close();
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
            <div class="card-header border-0">
              
            </div>
            
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Category Name</label>
                    <input type="text" name="prod_name" class="form-control">
                  </div>
                </div>
                <br>
               
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Image</label>
                    <input type="file" name="prod_img" class="btn btn-outline-success form-control">
                  </div>
                </div>
              
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addCategory" value="Add Category" class="btn btn-success">
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
