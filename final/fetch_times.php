<?php
$db = new mysqli('localhost', 'root', '', 'project');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['date'])) {
    $date = $_GET['date'];
    $stmt = $db->prepare("SELECT reservation_time FROM reservations WHERE reservation_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reservedTimes = [];
    while ($row = $result->fetch_assoc()) {
        $reservedTimes[] = $row['reservation_time'];
    }
    
    echo json_encode($reservedTimes);
    exit;
}
?>
