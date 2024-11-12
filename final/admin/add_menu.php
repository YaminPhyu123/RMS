<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['addProduct'])) {
    //Prevent Posting Blank Values
    if (empty($_POST["prod_name"]) || empty($_POST["cat_name"]) || empty($_FILES["prod_img"]["name"]) || empty($_POST['prod_price'])) {
        $err = "Blank Values Not Accepted";
    } else {
        $prod_name = $_POST['prod_name'];
        $cat_id = $_POST['cat_name'];
        $status = 1;
        $delete_flag = 0;
        $prod_img = $_FILES['prod_img']['name'];
        
        // Move uploaded file to destination directory
        move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/menu/" . $_FILES["prod_img"]["name"]);
        
        $prod_price = $_POST['prod_price'];

        // Insert Captured information to a database table
        $postQuery = "INSERT INTO foods (f_id, food_name, image, food_price, status, delete_flag) VALUES (?, ?, ?, ?, ?, ?)";
        $postStmt = $mysqli->prepare($postQuery);
        
        // Check if prepare succeeded
        if ($postStmt) {
            // Bind parameters with types
            $postStmt->bind_param("isssii", $cat_id, $prod_name, $prod_img, $prod_price, $status, $delete_flag);

            // Execute the statement
            $postStmt->execute();

            // Check if execution succeeded
            if ($postStmt->affected_rows > 0) {
                $success = "Product Added";
                header("refresh:1; url=menu.php");
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
              <h3>Please Fill All Fields</h3>
            </div>
            
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" class="form-control">
                    <!-- <input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>" class="form-control"> -->
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <label for="category_id" class="control-label">Category</label>
                    <select name="cat_name" id="">
                      <?php
                      $category = mysqli_query($mysqli, "SELECT * FROM food_category");
                      while ($cat = mysqli_fetch_array($category)) {
                        echo '<option value="' . $cat['f_id'] . '">' . $cat['category_name'] . '</option>';
                      }
                      ?>
                    </select>
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
                    <label>Product Price</label>
                    <input type="text" name="prod_price" class="form-control">
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addProduct" value="Add Product" class="btn btn-success">
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
