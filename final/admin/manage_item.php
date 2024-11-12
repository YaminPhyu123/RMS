<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM inventory WHERE item_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_item.php");
    exit();
}

// Handle AJAX search
if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];
    $search_query = "%{$search_query}%";
    
    // Pagination parameters
    $limit = 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT inventory.item_id, inventory.item_name, inventory.unit_price, inventory.inhand_qty, latest_inventory_use.max_date
            FROM inventory 
            LEFT JOIN (
                SELECT item_id, MAX(qty_date) AS max_date
                FROM inventory_use
                GROUP BY item_id
            ) AS latest_inventory_use 
            ON inventory.item_id = latest_inventory_use.item_id
            WHERE inventory.item_name LIKE ?
            LIMIT ?, ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sii', $search_query, $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "";
    while ($prod = $result->fetch_object()) {
        $output .= "<tr>
                        <td>{$prod->item_name}</td>
                        <td>{$prod->unit_price} Ks</td>
                        <td>{$prod->inhand_qty}</td>
                        <td>" . (isset($prod->max_date) ? $prod->max_date : '') . "</td>
                        <td>
                            <a href='manage_item.php?delete={$prod->item_id}'><button class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Delete</button></a>
                            <a href='update_item.php?update={$prod->item_id}'><button class='btn btn-sm btn-success'> <i class='fas fa-cart-plus'></i> Update</button></a>
                            <a href='edit_item.php?edit={$prod->item_id}'><button class='btn btn-sm btn-primary'><i class='fas fa-edit'></i> Edit</button></a>
                        </td>
                    </tr>";
    }

    echo $output;
    exit();
}

// Handle initial data load with pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT inventory.item_id, inventory.item_name, inventory.unit_price, inventory.inhand_qty, latest_inventory_use.max_date
        FROM inventory 
        LEFT JOIN (
            SELECT item_id, MAX(qty_date) AS max_date
            FROM inventory_use
            GROUP BY item_id
        ) AS latest_inventory_use 
        ON inventory.item_id = latest_inventory_use.item_id
        LIMIT ?, ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$initial_data = "";
while ($prod = $result->fetch_object()) {
    $initial_data .= "<tr>
                        <td>{$prod->item_name}</td>
                        <td>{$prod->unit_price} Ks</td>
                        <td>{$prod->inhand_qty}</td>
                        <td>" . (isset($prod->max_date) ? $prod->max_date : '') . "</td>
                        <td>
                            <a href='manage_item.php?delete={$prod->item_id}'><button class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Delete</button></a>
                            <a href='update_item.php?update={$prod->item_id}'><button class='btn btn-sm btn-success'> <i class='fas fa-cart-plus'></i> Update</button></a>
                            <a href='edit_item.php?edit={$prod->item_id}'><button class='btn btn-sm btn-primary'><i class='fas fa-edit'></i> Edit</button></a>
                        </td>
                    </tr>";
}

require_once('partials/_head.php');
?>
<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="add_item.php" class="btn btn-outline-success">
                <i class="fas fa-file-invoice-dollar text-primary"></i>
                Add New Item
              </a>
              <br><br>
              <!-- Search Form -->
              <form method="GET" class="form-inline ml-3">
                <div class="form-row">
                  <div class="col">
                    <input type="text" id="search_query" name="search_query" class="form-control" placeholder="Search For Item Name" value="<?php echo isset($_GET['search_query']) ? $_GET['search_query'] : ''; ?>">
                  </div>
                </div>
              </form>
            </div>
            
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Item Name</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Inhand Qty</th>
                    <th scope="col">Date</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody id="search_results">
                  <?php echo $initial_data; ?>
                </tbody>
              </table>
              <!-- Pagination Controls -->
              <nav aria-label="Page navigation">
                <ul class="pagination">
                  <?php
                  // Pagination controls
                  $total_rows_sql = "SELECT COUNT(*) as total FROM inventory";
                  $total_rows_stmt = $mysqli->prepare($total_rows_sql);
                  $total_rows_stmt->execute();
                  $total_rows_result = $total_rows_stmt->get_result();
                  $total_rows = $total_rows_result->fetch_object()->total;
                  $total_pages = ceil($total_rows / $limit);

                  for ($i = 1; $i <= $total_pages; $i++) {
                      echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                              <a class='page-link' href='manage_item.php?page=$i'>" . $i . "</a>
                            </li>";
                  }
                  ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function(){
      // Debounced AJAX search
      let debounceTimer;
      $('#search_query').on('input', function(){
        clearTimeout(debounceTimer);
        const query = $(this).val();
        debounceTimer = setTimeout(function(){
          $.ajax({
            url: 'manage_item.php',
            method: 'GET',
            data: { search_query: query },
            success: function(data) {
              $('#search_results').html(data);
            }
          });
        }, 300);  // Debounce time in milliseconds
      });
    });
  </script>
</body>
</html>
