<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

if (isset($_POST['useItem'])) {
    $prod_name = $_POST['itemName'];
    $prod_qty = $_POST['useQty'];

    // Update inventory table
    $postQuery1 = "UPDATE inventory
                   SET inhand_qty = inhand_qty - ?
                   WHERE item_name = ?";
    $postStmt1 = $mysqli->prepare($postQuery1);
    
    if ($postStmt1) {
        // Bind parameters for the update query
        $postStmt1->bind_param("is", $prod_qty, $prod_name);
        
        // Execute the update query
        if ($postStmt1->execute()) {
            $success1 = "Item Updated in inventory";
            
            // Retrieve the updated item_id from inventory table
            $ret = "SELECT item_id FROM inventory WHERE item_name = ?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param("s", $prod_name);
            $stmt->execute();
            $stmt->bind_result($item_id);
            $stmt->fetch();
            $stmt->close();
            
            if ($item_id) {
                // Insert into inventory_use table
                $buy_qty = 0; // Assuming use_qty is always 0 for purchase
                $time = date("Y-m-d"); // Corrected date format
                
                $postQuery2 = "INSERT INTO inventory_use (item_id, buy_qty, use_qty, qty_date) VALUES (?, ?, ?, ?)";
                $postStmt2 = $mysqli->prepare($postQuery2);
                
                if ($postStmt2) {
                    // Bind parameters for the insert query
                    $postStmt2->bind_param("iiis", $item_id, $buy_qty, $prod_qty, $time);
                    
                    // Execute the insert query
                    if ($postStmt2->execute()) {
                        $success2 = "Item Updated in inventory_use";
                    } else {
                        $error2 = "Error updating inventory_use: " . $postStmt2->error;
                    }
                    
                    // Close the insert statement
                    $postStmt2->close();
                } else {
                    $error2 = "Prepare statement failed for inventory_use: " . $mysqli->error;
                }
            } else {
                $error1 = "Failed to retrieve item_id after update.";
            }
            
        } else {
            $error1 = "Error updating inventory: " . $postStmt1->error;
        }
        
        // Close the update statement
        $postStmt1->close();
        
    } else {
        $error1 = "Prepare statement failed for inventory: " . $mysqli->error;
    }
    
    // Redirect after processing
    header("refresh:1; url=manage_item.php");
    exit; // Exit to prevent further execution after redirect
}

// Fetch data for display
require_once('partials/_head.php');
?>
<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>
    <?php
    $use = $_GET['use'] ?? null;
    $ret = "SELECT * FROM inventory
            INNER JOIN inventory_use ON inventory.item_id = inventory_use.item_id
            WHERE inventory.item_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param("i", $use);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($prod = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body"></div>
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
                      <input type="text" value="<?php echo $prod->item_name ?>" name="itemName" class="form-control" readonly>
                    </div>
                  </div>
                  <br>

                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Used Quantity</label>
                      <input type="text" name="useQty" value="<?php echo $prod->use_qty ?>" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="useItem" value="Use Item" class="btn btn-success">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
