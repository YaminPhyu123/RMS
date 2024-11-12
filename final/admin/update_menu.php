<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['updateMenu'])) {
  $update = $_GET['update'];
  $cat_id = $_POST["cat_name"];
  $prod_name = $_POST["fd_name"];
  $fd_price = $_POST['fd_price'];
  $status = $_POST['status'];
  $delete_flag = 0;

  // Check if a new image file is uploaded
  if ($_FILES['fd_image']['name']) {
    // If a new image is uploaded, handle file upload
    $fd_img = $_FILES['fd_image']['name'];
    move_uploaded_file($_FILES["fd_image"]["tmp_name"], "assets/img/menu/" . $_FILES["fd_image"]["name"]);

    // Update query with image
    $postQuery = "UPDATE foods SET f_id=?, image=?, food_name=?, food_price=?, status=?, delete_flag=? WHERE food_id=?";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isssiii', $cat_id, $fd_img, $prod_name, $fd_price, $status, $delete_flag, $update);
  } else {
    // If no new image uploaded, retain the existing image path
    $postQuery = "UPDATE foods SET f_id=?, food_name=?, food_price=?, status=?, delete_flag=? WHERE food_id=?";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('isssii', $cat_id, $prod_name, $fd_price, $status, $delete_flag, $update);
  }

  // Execute update query
  $postStmt->execute();

  // Check if update was successful
  if ($postStmt) {
    $success = "Product Updated";
    header("refresh:1; url=menu.php");
  } else {
    $err = "Please Try Again Or Try Later";
  }
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    $update = $_GET['update'] ?? null;
    $ret = "SELECT * FROM foods WHERE food_id= ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $update);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($prod = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
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
                      <input type="text" value="<?php echo $prod->food_name ?>" name="fd_name" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label for="category_id" class="control-label">Category</label>
                      <select name="cat_name" id="cat_name" class="form-control">
                        <?php
                        $category = mysqli_query($mysqli, "SELECT * FROM food_category");
                        while ($cat = mysqli_fetch_array($category)) {
                          $selected = ($cat['f_id'] == $prod->f_id) ? 'selected' : '';
                          echo '<option value="' . $cat['f_id'] . '" ' . $selected . '>' . $cat['category_name'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <br>

                  <div class="form-row">
                    <div class="col-md-6">
                      <label for="product_image">Product Image</label>
                      <input type="file" id="product_image" name="fd_image" class="form-control-file">
                      <?php if (!empty($prod->image)) : ?>
                        <img src="assets/img/menu/<?php echo htmlspecialchars($prod->image); ?>" class="img-responsive img-curve" style="max-width: 100px; max-height: 100px;">
                      <?php else : ?>
                        <p>No image selected</p>
                      <?php endif; ?>
                    </div>
                  </div>

                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Product Price</label>
                      <input type="text" value="<?php echo $prod->food_price ?>" name="fd_price" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label for="status" class="control-label">Status</label>
                      <select name="status" id="status" class="form-control form-control-sm rounded-0" required>
                        <option value="1" <?php echo ($prod->status == 1) ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($prod->status == 0) ? 'selected' : ''; ?>>Inactive</option>
                      </select>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateMenu" value="Update Menu" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
      <?php
      }
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->

</body>

</html>
