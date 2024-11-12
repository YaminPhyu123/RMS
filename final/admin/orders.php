<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

require_once('partials/_head.php');

$search = $_GET['search'] ?? ''; // Changed to GET to match the form method
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Records</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Pinsetter', sans-serif;
            font-size: 32px;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
            transition: background-color 0.3s;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            th, td {
                padding: 8px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
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
            <div class="container">
                <form action="" method="GET">
                   <input type="search" style="width:90%" autocomplete=off placeholder="Search by transaction id or bar code" name="search" value="<?php echo htmlspecialchars($search); ?>">
                   <button class="btn btn-primary" type="submit">Search</button>
                </form>
                <h2>Order Details Records</h2>
                <table id="transactionTable" class="display table table-striped">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Food Name</th>
                            <th>Food Price</th>
                            <th>Total Quantity</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "project";

                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Prepare SQL query
                        $stmt = $conn->prepare("
                            SELECT 
                                t.tid AS transaction_id,
                                f.food_name,
                                f.food_price,
                                s.quantity,
                                s.date AS order_date
                            FROM 
                                selection_order s
                            JOIN 
                                transaction t ON s.t_id = t.tid
                            JOIN 
                                foods f ON s.food_id = f.food_id
                            WHERE 
                                
                                t.tid LIKE ?
                            ORDER BY 
                                s.date DESC
                        ");
                        $searchParam = "%$search%";
                        $stmt->bind_param('s', $searchParam);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["transaction_id"]) . "</td>
                                        <td>" . htmlspecialchars($row["food_name"]) . "</td>
                                        <td>" . htmlspecialchars($row["food_price"]) . "</td>
                                        <td>" . htmlspecialchars($row["quantity"]) . "</td>
                                        <td>" . htmlspecialchars($row["order_date"]) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center'>No results found</td></tr>";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#transactionTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            lengthChange: true,
            lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
                lengthMenu: "_MENU_ records per page",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
  </script>
      <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
