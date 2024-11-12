<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

$con = mysqli_connect("localhost", "root", "", "project");
if (!$con) {
    echo "Problem in database connection! Contact administrator!" . mysqli_error();
}

// Check if period and value are set
if (isset($_GET['period'])) {
    $period = $_GET['period'];
    $specificValue = $_GET['value'] ?? null;

    // Determine the date condition based on period
    switch ($period) {
        case 'daily':
            $dateCondition = "DATE(so.date) = '" . $specificValue . "'";
            break;
        case 'monthly':
            $dateCondition = "DATE_FORMAT(so.date, '%Y-%m') = '" . $specificValue . "'";
            break;
        case 'yearly':
            $dateCondition = "YEAR(so.date) = '" . $specificValue . "'";
            break;
        default:
            $dateCondition = "1"; // Default condition if period is not recognized
            break;
    }

    // Query to fetch top 5 best-selling foods based on the selected period and date condition
    $food_query = "SELECT f.food_name, f.image, SUM(so.quantity) AS total_quantity
                    FROM selection_order so
                    INNER JOIN foods f ON so.food_id = f.food_id
                    INNER JOIN transaction t ON so.t_id = t.tid
                    WHERE t.payment_status = 'Paid' AND {$dateCondition}
                    GROUP BY so.food_id
                    ORDER BY total_quantity DESC
                    LIMIT 5";

    $food_result = mysqli_query($con, $food_query);
    $serial_number = 1; // Initialize serial number counter

    // Build HTML for top 5 best-selling foods
    $output = "";
    while ($food_row = mysqli_fetch_array($food_result)) {
        $food_name = $food_row['food_name'];
        $image_name = $food_row['image'];
        $total_quantity = $food_row['total_quantity'];

        // Construct the HTML row with dynamic PHP variables
        $output .= "<tr>
                        <td>{$serial_number}</td>
                        <td>{$food_name}</td>
                        <td><img src='assets/img/menu/{$image_name}' alt='{$food_name}'
                                class='img-responsive img-curve' width='100px' height='100px'>
                        </td>
                        <td>{$total_quantity}</td>
                    </tr>";
        $serial_number++; // Increment serial number for the next row
    }

    echo $output; // Output the HTML
} else {
    echo "Invalid request"; // Handle if period parameter is missing
}
?>
