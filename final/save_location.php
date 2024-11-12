<?php
//save_location.php

// Check if latitude and longitude are received via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process received data
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    session_start();
    $_SESSION['user_latitude'] = $latitude;
    $_SESSION['user_longitude'] = $longitude;
}

?>

