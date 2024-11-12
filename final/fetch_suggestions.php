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

if (isset($_POST['query'])) {
    $search = $conn->real_escape_string($_POST['query']);

    $sql = "SELECT food_name,food_id FROM foods  WHERE foods.status = 1 and food_name LIKE '%$search%' OR f_id IN 
            (SELECT f_id FROM food_category WHERE category_name LIKE '%$search%')";
    $result = $conn->query($sql);
    
    $suggestions = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row['food_name'];

        }
    }
    echo json_encode($suggestions);
}

$conn->close();
?>
