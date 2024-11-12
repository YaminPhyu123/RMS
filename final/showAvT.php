<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Availability</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .btn-check-all {
            margin-top: 20px;
            text-align: center;
        }

        .btn-check-all button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-check-all button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Check and Update Table Availability</h2>
        <div class="btn-check-all">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <button type="submit" name="showAvailableTables">Show Available Tables</button>
            </form>
        </div>

        <?php
                  if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

            // Query to get all available tables
            $sql = "SELECT TableNumber, SeatingCapacity, Description, status 
FROM tables 
WHERE status = 'available' OR status = 'reserved';
";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Table Number</th><th>Seating Capacity</th><th>Description</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['TableNumber']}</td>";
                    echo "<td>{$row['SeatingCapacity']}</td>";
                    echo "<td>{$row['Description']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No tables are currently available.</p>";
            }

            // Close connection
            $conn->close();
        }
        ?>

    </div>
</body>
</html>
