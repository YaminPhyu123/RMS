<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['editItem'])) {
    $prod_name = $_POST['itemName'];
    $prod_price = floatval($_POST['unitPrice']);
    $use_qty = floatval($_POST['usedQty']);

    // Retrieve item_id and current use_qty
    $ret = "SELECT item_id, unit_price FROM inventory WHERE item_name = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param("s", $prod_name);
    $stmt->execute();
    $stmt->bind_result($item_id, $unit_price);
    $stmt->fetch();
    $stmt->close();

    if ($item_id) {
        // Fetch current use_qty from inventory_use table
        $time = date("Y-m-d");
        $ret_use_qty = "SELECT COALESCE(SUM(use_qty), 0) FROM inventory_use WHERE item_id = ? AND qty_date = ?";
        $stmt = $mysqli->prepare($ret_use_qty);
        $stmt->bind_param("is", $item_id, $time);
        $stmt->execute();
        $stmt->bind_result($current_use_qty);
        $stmt->fetch();
        $stmt->close();

        // Fetch current inhand_qty
        $ret_inhand_qty = "SELECT inhand_qty FROM inventory WHERE item_name = ?";
        $stmt = $mysqli->prepare($ret_inhand_qty);
        $stmt->bind_param("s", $prod_name);
        $stmt->execute();
        $stmt->bind_result($inhand_qty);
        $stmt->fetch();
        $stmt->close();

        // Calculate new inhand_qty
        $new_inhand_qty = $inhand_qty + $current_use_qty - $use_qty;

        // Update inventory table
        $postQuery1 = "UPDATE inventory SET inhand_qty = ?, unit_price = ? WHERE item_name = ?";
        $postStmt1 = $mysqli->prepare($postQuery1);
        if ($postStmt1) {
            $postStmt1->bind_param("dds", $new_inhand_qty, $prod_price, $prod_name);
            if ($postStmt1->execute()) {
                $success1 = "Item Updated in inventory";

                // Check if record exists for today's date in inventory_use
                $ret_use_check = "SELECT * FROM inventory_use WHERE item_id = ? AND qty_date = ?";
                $stmt = $mysqli->prepare($ret_use_check);
                $stmt->bind_param("is", $item_id, $time);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // Update existing record
                    $postQuery2 = "UPDATE inventory_use SET buy_qty = ?, use_qty = ? WHERE item_id = ? AND qty_date = ?";
                    $postStmt2 = $mysqli->prepare($postQuery2);
                    if ($postStmt2) {
                        $postStmt2->bind_param("ddis", $use_qty, $use_qty, $item_id, $time);
                        if ($postStmt2->execute()) {
                            $success2 = "Item Updated in inventory_use";
                        } else {
                            $error2 = "Error updating inventory_use: " . $postStmt2->error;
                        }
                        $postStmt2->close();
                    } else {
                        $error2 = "Prepare statement failed for inventory_use: " . $mysqli->error;
                    }
                }
                // Check if record exists for today's date in daily_record
                $checkQuery = "SELECT expense FROM daily_record WHERE date = ?";
                $checkStmt = $mysqli->prepare($checkQuery);
                $checkStmt->bind_param("s", $time);
                $checkStmt->execute();
                $checkStmt->bind_result($current_expense);
                $checkStmt->fetch();
                $checkStmt->close();

                if ($current_expense !== null) {
                    // Update existing record in daily_record
                    $expense = ($current_use_qty - $use_qty) * $unit_price;
                    $new_expense = $current_expense - $expense;

                    $updateQuery = "UPDATE daily_record SET expense = ? WHERE date = ?";
                    $updateStmt = $mysqli->prepare($updateQuery);
                    $updateStmt->bind_param("ds", $new_expense, $time);

                    if ($updateStmt->execute()) {
                        $success4 = "Daily record updated";
                    } else {
                        $error4 = "Error updating daily record: " . $updateStmt->error;
                    }
                    $updateStmt->close();
                }
            } else {
                $error1 = "Error updating inventory: " . $postStmt1->error;
            }
            $postStmt1->close();
        } else {
            $error1 = "Prepare statement failed for inventory: " . $mysqli->error;
        }
    } else {
        $error1 = "Failed to retrieve item_id after update.";
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
        <?php
$edit = $_GET['edit'] ?? null;

// Query to get the latest use_qty for the specified item
$ret = "SELECT inventory.item_id, inventory.item_name, inventory.unit_price, COALESCE(latest_inventory_use.use_qty, 0) AS use_qty
        FROM inventory 
        LEFT JOIN (
            SELECT i.item_id, i.use_qty
            FROM inventory_use i
            INNER JOIN (
                SELECT item_id, MAX(qty_date) AS max_date
                FROM inventory_use
                GROUP BY item_id
            ) latest
            ON i.item_id = latest.item_id AND i.qty_date = latest.max_date
        ) AS latest_inventory_use 
        ON inventory.item_id = latest_inventory_use.item_id
        WHERE inventory.item_id = ?";

$stmt = $mysqli->prepare($ret);
$stmt->bind_param('i', $edit);
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
                        <h3>Edit Item</h3>
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
                                    <label>Used Quantity</label>
                                    <input type="text" value="<?php echo htmlspecialchars($prod->use_qty); ?>" name="usedQty" class="form-control">
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <input type="submit" name="editItem" value="Edit Item" class="btn btn-success">
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

