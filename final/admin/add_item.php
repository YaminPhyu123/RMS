<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

if (isset($_POST['addItem'])) {
    $prod_name = $_POST['item_name'];
    $prod_price = 0;
    $prod_qty = 0;
    
    // Check if the item already exists
    $checkQuery = "SELECT item_id FROM inventory WHERE item_name = ?";
    $checkStmt = $mysqli->prepare($checkQuery);

    if ($checkStmt) {
        $checkStmt->bind_param("s", $prod_name);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Item already exists
            $error = "Item already exists in inventory.";
        } else {
            // Item does not exist, proceed with insertion
            $time = date("Y-m-d"); // Adjusted date format for MySQL datetime

            // Insert Captured information into the inventory table
            $postQuery1 = "INSERT INTO inventory (item_name, unit_price, inhand_qty) VALUES (?, ?, ?)";
            $postStmt1 = $mysqli->prepare($postQuery1);

            if ($postStmt1) {
                $postStmt1->bind_param("sdi", $prod_name, $prod_price, $prod_qty);

                if ($postStmt1->execute()) {
                    $item_id = $mysqli->insert_id; // Get the auto-generated item_id
                    $success = "Item Inserted in inventory";

                    // Insert into inventory_use table using the retrieved item_id
                    $buy_item = 0; 
                    $use_qty = 0;
                    $postQuery2 = "INSERT INTO inventory_use (item_id, buy_qty, use_qty, qty_date) VALUES (?, ?, ?, ?)";
                    $postStmt2 = $mysqli->prepare($postQuery2);

                    if ($postStmt2) {
                        $postStmt2->bind_param("idds", $item_id, $buy_item, $use_qty, $time);

                        if ($postStmt2->execute()) {
                            $success = "Item Inserted in inventory_use ";
                        } else {
                            $error = "Error inserting into inventory_use: " . $postStmt2->error;
                        }

                        $postStmt2->close();
                    } else {
                        $error = "Prepare statement failed for inventory_use: " . $mysqli->error;
                    }

                } else {
                    $error = "Error inserting into inventory: " . $postStmt1->error;
                }

                $postStmt1->close();
            } else {
                $error = "Prepare statement failed for inventory: " . $mysqli->error;
            }
        }
        $checkStmt->close();
    } else {
        $error = "Prepare statement failed for checking item existence: " . $mysqli->error;
    }

    // Redirect after processing
    if(isset($error)) {
        echo "<script>alert('$error');</script>";
    }

    header("refresh:1; url=manage_item.php");
    exit; // Exit to prevent further execution after redirect
}

// Require your header and other HTML content
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
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>

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
                    <label>Item Name</label>
                    <input type="text" name="item_name" class="form-control">
                  </div>
                </div>
                <br>

                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="addItem" value="Add Item" class="btn btn-success">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
