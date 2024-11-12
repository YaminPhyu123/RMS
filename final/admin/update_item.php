<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['updateItem'])) {
    $prod_name = $_POST['itemName'];
    $prod_price = floatval($_POST['unitPrice']);
    $prod_qty = floatval($_POST['buyQty']);
    $use_qty = floatval($_POST['usedQty']);

    // Update inventory table
    $postQuery1 = "UPDATE inventory
                   SET inhand_qty = inhand_qty + ? - ?, 
                       unit_price = ?
                   WHERE item_name = ?";
    $postStmt1 = $mysqli->prepare($postQuery1);

    if ($postStmt1) {
        $postStmt1->bind_param("ddds", $prod_qty, $use_qty, $prod_price, $prod_name);

        if ($postStmt1->execute()) {
            $success1 = "Item Updated in inventory";

            // Retrieve item_id after update
            $ret = "SELECT item_id, unit_price FROM inventory WHERE item_name = ?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param("s", $prod_name);
            $stmt->execute();
            $stmt->bind_result($item_id, $unit_price);
            $stmt->fetch();
            $stmt->close();

            if ($item_id) {
                // Check if record exists for today's date
                $time = date("Y-m-d");
                $ret = "SELECT * FROM inventory_use WHERE item_id = ? AND qty_date = ?";
                $stmt = $mysqli->prepare($ret);
                $stmt->bind_param("is", $item_id, $time);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // Update existing record
                    $postQuery2 = "UPDATE inventory_use 
                                   SET buy_qty =  ?, 
                                       use_qty =  ? 
                                   WHERE item_id = ? AND qty_date = ?";
                    $postStmt2 = $mysqli->prepare($postQuery2);
                    if ($postStmt2) {
                        $postStmt2->bind_param("ddis", $prod_qty, $use_qty, $item_id, $time);
                        if ($postStmt2->execute()) {
                            $success2 = "Item Updated in inventory_use";
                        } else {
                            $error2 = "Error updating inventory_use: " . $postStmt2->error;
                        }
                        $postStmt2->close();
                    } else {
                        $error2 = "Prepare statement failed for inventory_use: " . $mysqli->error;
                    }
                } else {
                    // Insert new record
                    $postQuery3 = "INSERT INTO inventory_use (item_id, buy_qty, use_qty, qty_date) VALUES (?, ?, ?, ?)";
                    $postStmt3 = $mysqli->prepare($postQuery3);

                    if ($postStmt3) {
                        $postStmt3->bind_param("idds", $item_id, $prod_qty, $use_qty, $time);
                        if ($postStmt3->execute()) {
                            $success3 = "Item Updated in inventory_use";
                        } else {
                            $error3 = "Error inserting into inventory_use: " . $postStmt3->error;
                        }
                        $postStmt3->close();
                    } else {
                        $error3 = "Prepare statement failed for inventory_use: " . $mysqli->error;
                    }
                }

                // Check if record exists for today's date in daily_record
                $checkQuery = "SELECT * FROM daily_record WHERE date = ?";
                $checkStmt = $mysqli->prepare($checkQuery);
                $checkStmt->bind_param("s", $time);
                $checkStmt->execute();
                $checkStmt->store_result();

                $expense = $use_qty * $unit_price;
                $income = 0;
                $profit = 0;

                if ($checkStmt->num_rows > 0) {
                    // Update existing record in daily_record
                    $updateQuery = "UPDATE daily_record SET expense = expense + ? WHERE date = ?";
                    $updateStmt = $mysqli->prepare($updateQuery);
                    $updateStmt->bind_param("is", $expense, $time);

                    if ($updateStmt->execute()) {
                        $success4 = "Daily record updated";
                    } else {
                        $error4 = "Error updating daily record: " . $updateStmt->error;
                    }
                    $updateStmt->close();
                } else {
                    // Insert new record into daily_record
                    $insertQuery = "INSERT INTO daily_record (income, expense, profit, date) VALUES (?, ?, ?, ?)";
                    $insertStmt = $mysqli->prepare($insertQuery);

                    if ($insertStmt) {
                        $insertStmt->bind_param("iiis", $income, $expense, $profit, $time);
                        if ($insertStmt->execute()) {
                            $success5 = "Daily record inserted";
                        } else {
                            $error5 = "Error inserting daily record: " . $insertStmt->error;
                        }
                        $insertStmt->close();
                    } else {
                        $error5 = "Prepare statement failed for daily record: " . $mysqli->error;
                    }
                }

            } else {
                $error1 = "Failed to retrieve item_id after update.";
            }
        } else {
            $error1 = "Error updating inventory: " . $postStmt1->error;
        }
        $postStmt1->close();
    } else {
        $error1 = "Prepare statement failed for inventory: " . $mysqli->error;
    }
    header("refresh:1; url=manage_item.php");
    exit;
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
            <?php
            $update = $_GET['update'] ?? null;
            $ret = "SELECT * FROM inventory INNER JOIN inventory_use ON inventory.item_id = inventory_use.item_id WHERE inventory.item_id = ?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $update);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($prod = $res->fetch_object()) {
            ?>
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
                            <br><br>
                            <div class="card shadow">
                                <div class="card-header border-0">
                                    <h3>Update Item</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Form -->
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Item Name</label>
                                                <input type="text" value="<?php echo htmlspecialchars($prod->item_name); ?>" name="itemName" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Unit Price</label>
                                                <input type="text" value="<?php echo htmlspecialchars($prod->unit_price); ?>" name="unitPrice" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Buy Quantity</label>
                                                <input type="text" value="" name="buyQty" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <label>Used Quantity</label>
                                                <input type="text" value="" name="usedQty" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <input type="submit" name="updateItem" value="Update Item" class="btn btn-success">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                echo "No item found with specified ID.";
            }
            ?>
        <!-- </div> -->
    </div>
            <!-- Argon Scripts -->
            <?php require_once('partials/_scripts.php'); ?>
        </body>
        </html>