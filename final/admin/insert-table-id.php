<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";

// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// if (isset($_POST['tid'])) {
//     $tid = $_POST['tid'];
//     $t_image = $tid . ".png";

//     // Prepare the SQL statement
//     $stmt = $conn->prepare("UPDATE `tables` SET `tableQR` = ? WHERE `TableID` = ?");
//     if ($stmt) {
//         // Bind the parameters
//         $stmt->bind_param('si', $t_image, $tid);
        
//         // Execute the statement
//         if ($stmt->execute()) {
//             echo "Table ID updated successfully!";
//         } else {
//             echo "Error updating table ID: " . $stmt->error;
//         }

//         // Close the statement
//         $stmt->close();
//     } else {
//         echo "Error preparing statement: " . $conn->error;
//     }
// } else {
//     echo "No Table ID provided.";
// }

// Close the connection







        if(isset($_POST['tid'])){
            
            $seatingCapacity = $_POST['SeatingCapacity'];
            $description = $_POST['Description'];
            $tableNumber = $_POST['TableNumber'];
            $t_image=$tid.".png";
            $insert= "INSERT INTO tables (TableID, tableQR, SeatingCapacity, Description, IsAvailable) 
                      VALUES ('$tableNumber', '$t_image', '$seatingCapacity', '$description',  1)";
            if(mysqli_query($con,$insert)){
                echo "Table id inserted successfully!";
            }
            else{
                echo "Table insertion fails!";
            }
        }
        else{
            echo "No tid";
        }
$conn->close();
?>