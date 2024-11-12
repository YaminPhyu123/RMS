<?php
header('Content-Type: application/json');

// Database connection details
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

if (isset($_GET['tableId'])) {
    $tableId = $_GET['tableId'];

    // Prepare SQL statement
    $sql = "SELECT so.quantity, f.food_price, t.tid, f.image, f.food_name, t.t_total,f.food_price,so.s_id,
    t.tableId, r.id AS reservation_id,t.payment_status
            FROM selection_order so
            INNER JOIN transaction t ON so.t_id = t.tid
            INNER JOIN foods f ON so.food_id = f.food_id
            LEFT JOIN reservations r ON so.r_id = r.id AND r.reserve_status = 'confirmed' AND r.pre_choose = 'yes'
            WHERE (t.Finish=0 AND so.r_id IS NULL AND t.tableId = ?)
            OR (so.r_id IS NOT NULL AND r.reserve_status = 'confirmed' AND r.pre_choose = 'yes' AND t.tableId = ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $tableId, $tableId);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        echo json_encode($orders);
    } else {
        echo json_encode(["error" => "Failed to prepare SQL statement."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "No tableId provided."]);
}

$conn->close();
?>
