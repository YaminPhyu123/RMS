<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['query'])) {
    $search = htmlspecialchars($_GET['query']);
    
    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT food_name FROM foods WHERE name LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $matches = [];
    while ($row = $result->fetch_assoc()) {
        $matches[] = $row;
    }
    
    // Return the results as JSON
    echo json_encode($matches);
    
    $stmt->close();
}

$conn->close();
?>
