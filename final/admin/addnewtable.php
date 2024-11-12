<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();


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

// Handle table saving
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save') {
    // Include database connection
     // Make sure to include your DB connection file

    // Get POST data and sanitize it
    $tableNumber = $conn->real_escape_string($_POST['TableNumber']);
    $seatingCapacity = $conn->real_escape_string($_POST['SeatingCapacity']);
    $description = $conn->real_escape_string($_POST['Description']);
    $imageUpload = $conn->real_escape_string($_POST['ImageUpload']);

    // Check if the table already exists
    $checkTableSql = "SELECT * FROM tables WHERE TableID = '$tableNumber'";
    $result = $conn->query($checkTableSql);

    if ($result->num_rows > 0) {
        // If the table exists, return an error status
        echo json_encode(['status' => 'error', 'message' => 'Table already exists in tables.']);
        exit;
    }

    // Insert new table record
    $insertSql = "INSERT INTO tables (TableID, tableQR, SeatingCapacity, Description, isAvailable) 
                  VALUES ('$tableNumber', '$imageUpload', '$seatingCapacity', '$description', 1)";

    if ($conn->query($insertSql) === TRUE) {
        // If successful, return a success status
        echo json_encode(['status' => 'success']);
    } else {
        // If an error occurred, return an error status with a message
        echo json_encode(['status' => 'error', 'message' => 'Error saving data: ' . $conn->error]);
    }

    // Close the connection
    $conn->close();
    exit;
}


// Update table if form is submitted
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_POST['TableID']) && isset($_POST['SeatingCapacity']) && isset($_POST['Description']) && isset($_POST['IsAvailable'])) {
    $tableID = $_POST['TableID'];
    $seatingCapacity = $_POST['SeatingCapacity'];
    $description = $_POST['Description'];
    $isAvailable = $_POST['IsAvailable'];

    // Sanitize inputs
    $tableID = $conn->real_escape_string($tableID);
    $seatingCapacity = $conn->real_escape_string($seatingCapacity);
    $description = $conn->real_escape_string($description);
    $isAvailable = $conn->real_escape_string($isAvailable);
 // Update query
 $updateSql = "UPDATE tables SET SeatingCapacity='$seatingCapacity', Description='$description', IsAvailable='$isAvailable' WHERE TableID='$tableID'";

 if ($conn->query($updateSql) === TRUE) {
    //  echo '<script>alert("Table updated successfully");</script>';
 } else {
     echo "Error: " . $updateSql . "<br>" . $conn->error;
 }
}
// Delete table if action is delete
// if (isset($_GET['TableID'])) {
//     $tableID = $_GET['TableID'];
//     $deleteSql = "DELETE FROM tables WHERE TableID= ?";
//     $stmt = $mysqli->prepare( $deleteSql);
//     $stmt->bind_param('s', $tableID);
//     $stmt->execute();
//     $stmt->close();
//     if ($stmt) {
//       $deleted = "" && header("refresh:1; url=addnewtable.php");
//     } else {
//       $err = "Try Again Later";
//     }
//   }

if (isset($_GET['TableID'])) {
    $tableID = $_GET['TableID'];
    $sql = "DELETE FROM tables WHERE TableID= ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $tableID);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
             $deleted = "" && header("refresh:1; url=addnewtable.php");
            } else {
               $err = "Try Again Later";
             }
}
  require_once('partials/_head.php');
  ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Management</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        #qr-code {
    display: inline-block;
    max-width: 100%; /* Ensures the image is responsive */
    height: auto; /* Keeps the aspect ratio */
}

.form-group.text-center {
    text-align: center; /* Centers the image horizontally */
}

         body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 20px;
        }
        .containeradd {
            max-width: 50%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
         
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            padding-bottom: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        

.btn .save-btn {
    padding: 3px 3px;

}

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        input{
            width:100px;
            padding: 5px;
            text-align: left;
       
          
        }
        th {
           
            background-color: #2c3e50;
            color: white;
        }
        .btn:hover {
            background-color:black;
        }
        /* form {
            margin-bottom: 20px;
        } */
        form label {
            display: black;
            margin-bottom: 5px;
        }
        form input, form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>

    <!-- Main content -->
    <div class="main-content">
        <br><br>
    
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- All Reservations -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">
                        <div class="table-container">
                            <div class="containeradd">
                                <h2>Manage Tables</h2>

                                <!-- Form for generating QR code and saving table -->
                                <form method="post" id="generate-qr-form">
                                    <label for="TableNumber">Table Number:</label>
                                    <input type="text" id="TableNumber" name="TableNumber" required>
                                    <label for="SeatingCapacity">Seating Capacity:</label>
                                    <input type="number" id="SeatingCapacity" name="SeatingCapacity" required>
                                    <label for="Description">Description:</label>
                                    <input type="text" id="Description" name="Description">
                                    <div class="form-group">
                                        <input type="button" value="add new table" class="btn btn-success generateBtn">
        
                                        <input type="button" value="Save QR" class="btn btn-success saveBtn" style="display:none;">
                                    </div>
                                    <div class="form-group text-center">
                                   <img src="" alt="QR Code" class="fade" id="qr-code">
                                    </div>
                                    <span class="alert alert-danger fade"></span>
                                </form>
                            </div>

                            <!-- Table listing -->
                            <div class="container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Table Number</th>
                                            <th>Seating Capacity</th>
                                            <th>Description</th>
                                            <th>Is Available</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    // Select operation
                                    $sql = "SELECT * FROM tables";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        // Output data of each row
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr data-tableid="' . $row["TableID"] . '">';
                                            echo '<td class="editable" data-field="TableID">' . $row["TableID"] . '</td>';
                                            echo '<td class="editable" data-field="SeatingCapacity">' . $row["SeatingCapacity"] . '</td>';
                                            echo '<td class="editable" data-field="Description">' . $row["Description"] . '</td>';
                                            echo '<td class="editable" data-field="IsAvailable">' . ($row["IsAvailable"] ? 'Yes' : 'No') . '</td>';
                                            echo '<td>';
                                            echo '<button class="btn btn-sm btn-primary edit-btn">';
                                            echo"<i class='fas fa-edit'></i> Edit</button>";
                                            echo '<button class="btn btn-sm btn-success save-btn" style="display:none;">';
                                            echo "<i class='fas fa-check-circle'></i>Save</button>";
                                            echo '<a href="addnewtable.php?delete&TableID=' . $row["TableID"] . '" class="btn btn-sm btn-danger">';
                                            echo "<i class='fas fa-trash'></i> Delete</a>";
                                            echo '</td>';
                                      

                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5">0 results</td></tr>';
                                    }

                                    // Close the connection
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
    </div>
    <script>
$(function(){
    $('.generateBtn').click(function(){
        const tableNumber = $('#TableNumber').val();
        const seatingCapacity = $('#SeatingCapacity').val();
        const description = $('#Description').val();

        if (tableNumber) {
            $.ajax({
                url: "generate.php",
                type: "post",
                data: { qr_text: tableNumber },
                success: function(response){
                    if(response.status === 'success'){
                        $('#qr-code').attr('src', "qrcodes/" + response.fileName);
                        $('#qr-code').removeClass('fade');
                        $('.saveBtn').show();
                        $('.generateBtn').hide();                    
                       
                    } else {
                        $('.alert').text('Error generating QR code: ' + response.message);
                        $('.alert').show();
                    }
                },
                error: function(error){
                    $('.alert').text('Error generating QR code: ' + error.responseText);
                    $('.alert').show();
                }
            });
        } else {
            $('.alert').text('Please enter a Table Number.');
            $('.alert').show();
        }
    });

    $('.saveBtn').click(function() {
    const tableNumber = $('#TableNumber').val();
    const seatingCapacity = $('#SeatingCapacity').val();
    const description = $('#Description').val();
    const imageFileName = $('#qr-code').attr('src').split('/').pop(); // Extract the file name

    

    $.ajax({
        url: '',  // Provide the URL to your PHP handler if it's not the same page
        type: 'post',
        data: {
            action: 'save',
            TableNumber: tableNumber,
            SeatingCapacity: seatingCapacity,
            Description: description,
            ImageUpload: imageFileName // Send only the file name
        },
        success: function(response) {
            const jsonResponse = JSON.parse(response);

            if (jsonResponse.status === 'success') {
                alert('Table successfully added');
                window.location = "addnewtable.php";
            } else if (jsonResponse.status === 'error' && jsonResponse.message === 'Table already exists in tables.') {
                if (confirm('Error: Table already exists ')) {
                    window.location = "addnewtable.php";
                }
            } else {
                alert('Error saving data: ' + jsonResponse.message);
            }
        },
        error: function(error) {
            $('.alert').text('Error saving data: ' + error.responseText).show();
        }
    });
});

});


document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const saveButtons = document.querySelectorAll('.save-btn');
            const editableFields = document.querySelectorAll('.editable');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelectorAll('.editable').forEach(td => {
                        const value = td.textContent.trim();
                        if (td.dataset.field === 'IsAvailable') {
                            td.innerHTML = `<select><option value="1"${value === 'Yes' ? ' selected' : ''}>Yes</option><option value="0"${value === 'No' ? ' selected' : ''}>No</option></select>`;
                        } else {
                            td.innerHTML = `<input type="text" value="${value}">`;
                        }
                    });
                    this.style.display = 'none';
                    row.querySelector('.save-btn').style.display = 'inline-block';
                });
            });

            saveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const TableID = row.querySelector('[data-field="TableID"] input').value;
                    const seatingCapacity = row.querySelector('[data-field="SeatingCapacity"] input').value;
                    const description = row.querySelector('[data-field="Description"] input').value;
                    const isAvailable = row.querySelector('[data-field="IsAvailable"] select').value;

                    // Update table via AJAX
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '?action=update', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            alert('Table updated successfully');
                            row.querySelector('[data-field="TableID"]').innerHTML = TableID;
                            row.querySelector('[data-field="SeatingCapacity"]').innerHTML = seatingCapacity;
                            row.querySelector('[data-field="Description"]').innerHTML = description;
                            row.querySelector('[data-field="IsAvailable"]').innerHTML = isAvailable === '1' ? 'Yes' : 'No';
                            button.style.display = 'none';
                            row.querySelector('.edit-btn').style.display = 'inline-block';
                        } else {
                            alert('Error updating table');
                        }
                    };
                    xhr.send(`TableID=${TableID}&SeatingCapacity=${seatingCapacity}&Description=${description}&IsAvailable=${isAvailable}`);
                    location.reload(); }); });
            });
      
    
</script>
<?php require_once('partials/_scripts.php'); ?>
</body>
</html>
