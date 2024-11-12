<?php
include('userpartials-front/menu1.php');
include('userpartials-front/topnav.php');

$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";

if(isset($tno)){}else{
    $tno = isset($_GET['tno']) ? filter_var($_GET['tno'], FILTER_SANITIZE_NUMBER_INT) : null;
        $_SESSION['tno']=$tno;}

// Check if category id is passed
if(isset($_GET['f_id'])) {
    $category_id = $_GET['f_id'];
    
    // Get category title based on category id
    $sql = "SELECT category_name FROM food_category WHERE f_id=$category_id";
    $res = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $category_title = $row['category_name'];
    } else {
        // Redirect to home page if category not found
        header('location:'.SITEURL);
        exit;
    }
} else {
    // Redirect to home page if category id not set
    header('location:'.SITEURL);
    exit;
}


$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    if(isset($_POST['add_to_order']))
    {
        if(isset($_SESSION['OrderCart']))
        {
            
           
            $myitems = array_column($_SESSION['OrderCart'],'Item_Name');
            if(in_array($_POST['Item_Name'], $myitems))
            {
                foreach ($_SESSION['OrderCart'] as &$item) {
                    if ($item['Item_Name'] == $_POST['Item_Name']) {
                        $item['Quantity'] += $_POST['Quantity'];
                        break;
                    }
                }
                echo "<script>
                window.location.href =  window.history.back();
                 </script>";
                // echo "<script>
                // alert('Food Already In Order List'); 
               
                // </script>"; 
                 
            }
            else
            {

            
            $count = count($_SESSION['OrderCart']); 
            $_SESSION['OrderCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
            window.location.href =  window.history.back();
             </script>";
            }
            

        }
        else
        {
            $_SESSION['OrderCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
            window.location.href =  window.history.back();
             </script>";
                
        }

    }else 
    if(isset($_POST['add_to_pre']))
    {
        if(isset($_SESSION['PreCart']))
        {
            
           
            $myitems = array_column($_SESSION['PreCart'],'Item_Name');
            if(in_array($_POST['Item_Name'], $myitems))
            {
                
                // echo "<script>
                // alert('Food Already In Pre-order List'); 
               
                // </script>"; 
                foreach ($_SESSION['PreCart'] as &$item) {
                    if ($item['Item_Name'] == $_POST['Item_Name']) {
                        $item['Quantity'] += $_POST['Quantity'];
                        break;
                    }
                }
                echo "<script>
                window.location.href =  window.history.back();
                 </script>";
            }
            else
            {

            
            $count = count($_SESSION['PreCart']); 
            $_SESSION['PreCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
                window.location.href =  window.history.back();
                 </script>";
                
            }
            

        }
        else
        {
            $_SESSION['PreCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
            window.location.href =  window.history.back();
             </script>";
                
        }

    }
}

date_default_timezone_set('Asia/Yangon'); // Set the timezone to Myanmar/Yangon
// Output the current date and time in the specified format
function checkTime() {
    date_default_timezone_set('Asia/Yangon');
    $currentTime = date('H:i'); // Get current time in 24-hour format
    
    // Define the start and end times in 24-hour format
    $startTime = '06:00';
    $endTime = '10:00';
    
    // Check if current time is within the specified range
    if ($currentTime >= $startTime && $currentTime <= $endTime) {
        return false;
     
       // Time is within range, so return true (enabled)
    } else {
        return true;
      
        // Time is outside range, so return false (disabled)
    }
 
}
function isReservationTimeAllowed($reservationId) {
    $servername = "localhost";
$username = "root";
$password = "";
$dbname = "project";
$conn = new mysqli($servername, $username, $password, $dbname);

   $sql = "SELECT reservation_time FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservationId);
    $stmt->execute();
    $stmt->bind_result($reservationTime);
    
    if ($stmt->fetch()) {
        $allowedStartTime = strtotime('06:00'); // Example allowed start time
        $allowedEndTime = strtotime('10:00'); // Example allowed end time
        
        $reservationTimeUnix = strtotime($reservationTime);
        
        if ($reservationTimeUnix >= $allowedStartTime && $reservationTimeUnix <= $allowedEndTime) {
            return true; // Reservation time is allowed
        } else {
            return false; // Reservation time is not allowed
        }
    } }
$isLocationAllowed = true;
// function isInsideShop() {
//     $shopLatitude=21.9480064;
//     $shopLongitude=96.0987136;

//     // $shopLatitude = 20.1241008;
//     // $shopLongitude = 94.9970469; 

//    $allowedRadius = 0.03; // 1km

//    if (!isset($_SESSION['user_latitude']) || !isset($_SESSION['user_longitude'])) {
//        return false;
//    }
//    $userLatitude = $_SESSION['user_latitude'];
//    $userLongitude = $_SESSION['user_longitude'];

//    // Calculate the distance
//    $distance = sqrt(pow($userLatitude - $shopLatitude, 2) + pow($userLongitude - $shopLongitude, 2));
//    return $distance <= $allowedRadius;
// }
// if (isset($_SESSION['user_latitude']) && isset($_SESSION['user_longitude'])) {
//    $isLocationAllowed = isInsideShop();
  
   
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Restaurant Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/style.css">
      <!-- Vendor CSS Files -->
  <link href="vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="vendor/aos/aos.css" rel="stylesheet">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <script src="vendor/aos/aos.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/glightbox/js/glightbox.min.js"></script>
  <script src="vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="vendor/php-email-form/validate.js"></script>
  <script src="vendor/swiper/swiper-bundle.min.js"></script>
    <!-- Google Web Fonts
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

     Icon Font Stylesheet 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

     Libraries Stylesheet 
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" /> 

    Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/corestyle.css" rel="stylesheet">

<style>
    .active{
        background: color #45637d;
        color:white;
    }


.pagination {
    display: flex;
    justify-content: center;
    padding: 8px 0;
    margin-top: 20px; 
}

.pagination a, .pagination .current-page {
    display: block;
    padding: 4px 4px;
    margin: 0 2px;
    color: black;
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pagination a:hover {
    background-color: #f0f0f0;
}

.pagination .current-page {
    background-color: #007bff;
    color: #fff;
    border: 1px solid #007bff;
}

.pagination .disabled {
    color: #ccc;
    border: 1px solid #ddd;
    cursor: not-allowed;
}


    </style>
    </head>
<body>
<!-- Food Search Section Starts Here -->
<section class="food-search text-center">
    <div class="container">
        <!-- food search content here -->
    </div>
</section>
<!-- Food Search Section Ends Here -->

<!-- Food Menu Section Starts Here -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Foods on <a href="#" class="text-white">"<?php echo $category_title; ?>"</a></h2>

        <?php

$items_per_page = 6;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$sql_total = "SELECT COUNT(*) as total FROM foods WHERE f_id=$category_id";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_items = $row_total['total']; // Total number of items in the database
// var_dump($total_items, $current_page, $offset);

        // Query to fetch foods based on category id
        $sql2 = "SELECT * FROM foods WHERE f_id=$category_id LIMIT $items_per_page OFFSET $offset";
        $res2 = mysqli_query($conn, $sql2);
        $count = mysqli_num_rows($res2);

        // Pagination controls
        $total_pages = ceil($total_items / $items_per_page);

        if(mysqli_num_rows($res2) > 0) {
            while($row2 = mysqli_fetch_assoc($res2)) {
                $itemid = $row2['f_id'];
                $foodid= $row2['food_id'];
                $title = $row2['food_name'];
                $price = $row2['food_price'];
                $image_name = $row2['image'];
 
                ?>
                <div class="food-menu-box">
                    <div class="food-menu-img">
                        <?php 
                        if($image_name=="") {
                            // Image not Available
                            echo "<div class='error'>Image not Available.</div>";
                        } else {
                            // Image Available
                            ?>
                            <img src="<?php echo SITEURL; ?>admin/assets/img/menu/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve" width="300" height="100"loading="lazy">
                            <?php
                        }
                        ?>
             </div>
                                               
                    <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo $title; ?></h5>
                                    <p class="card-text"><?php echo $price; ?>Ks</p>
<!--plus/minus-->
                               <!--     <div class="Quantity-selector">
                                        <div class="btn btn-custom minus-btn" onclick="decrementValue(<?php echo $id; ?>)">-</div>
                                        <input type="integer" id="Quantity-<?php echo $id; ?>" name="Quantity" class="form-control text-center" value="1" min="1" disabled>
                                        <div class="btn btn-custom plus-btn" onclick="incrementValue(<?php echo $id; ?>)">+</div>
                                    </div>-->

                                    <?php if ($preorderMode): ?>
                                        <!-- Show the preorder form -->
                                        <form  method="POST">

                                        <!-- <form action="foods.php" method="POST"> -->
                                        <?php 
                                        $reservationId=$_SESSION['reservation_id'];
                                        $isDisabled = ($itemid == 17 && !isReservationTimeAllowed($reservationId)) ? 'disabled' : '';
  ?>
  <button type="submit"  name="add_to_pre" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $foodid; ?>" <?php echo $isDisabled; ?>>
      Add To Preorder
  </button>
                                             <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                            <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                            <input type="hidden" name="Id" value="<?php echo $foodid; ?>">
                                            <input type="hidden" id="Quantity-hidden-<?php echo $foodid; ?>" name="Quantity" value="1">
                                        </form>
                                    <?php else: ?>
                                        <!-- Show the order form -->
                                        <!-- <form action="foods.php" method="POST"> -->
                                        <form  method="POST">

                                        <?php $isDisabled = !$isLocationAllowed || ($itemid == 17 && checkTime()) ? 'disabled' : '';
  ?>
  <button type="submit" id="orderButton" name="add_to_order" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $foodid; ?>" <?php echo $isDisabled; ?>>
      Order Now
  </button>                                   <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                            <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                            <input type="hidden" name="Id" value="<?php echo $foodid; ?>">
                                            <input type="hidden" name="Item" value="<?php echo $itemid; ?>">
                                            <input type="hidden" id="Quantity-hidden-<?php echo $foodid; ?>" name="Quantity" value="1">
                                        </form>
                                    <?php endif; ?>

                                </div>
                       
                </div>
                <?php
            }
        } else {
            echo "<div class='error'>Food not Available.</div>";
        }

        ?>

    </div>
    </section>
    <div class="clearfix"></div>
    </div>
<!-- Food Menu Section Ends Here -->
   <?php
       
         $range = 30; 

         // Calculate the starting and ending page numbers for the pagination range
         $start_page = max(1, $current_page - floor($range / 2));
         $end_page = min($total_pages, $current_page + floor($range / 2));
         
         // Adjust the start and end page if the range goes beyond the total number of pages
         if ($end_page - $start_page + 1 < $range) {
             $start_page = max(1, $end_page - $range + 1);
             $end_page = min($total_pages, $start_page + $range - 1);
         }

         $base_url = "category-foods.php?tno=" . htmlspecialchars($tno) .  "&f_id=$category_id&page="; // Include f_id in the base URL
         
         echo '<div class="pagination">'; // Start a container for pagination
         
         // Create "Previous" link
         if ($current_page > 1) {
             echo '<a href="' . $base_url . ($current_page - 1) . '">&laquo; Prev</a> ';
         } else {
             echo '<span class="disabled">&laquo; Prev</span> ';
         }
         
         // Create page number links
         for ($i = $start_page; $i <= $end_page; $i++) {
             if ($i == $current_page) {
                 echo '<span class="current-page">' . $i . '</span> ';
             } else {
                 echo '<a href="' . $base_url . $i . '">' . $i . '</a> ';
             }
         }
         
         // Create "Next" link
         if ($current_page < $total_pages) {
             echo '<a href="' . $base_url . ($current_page + 1) . '">Next &raquo;</a> ';
         } else {
             echo '<span class="disabled">Next &raquo;</span> ';
         }
         
         echo '</div>'; // End container for pagination
         ?>


<script>
     //+-sign
   /*  function incrementValue(id) {
            var value = parseInt(document.getElementById('Quantity-' + id).value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById('Quantity-' + id).value = value;
            document.getElementById('Quantity-hidden-' + id).value = value;
        }

        function decrementValue(id) {
            var value = parseInt(document.getElementById('Quantity-' + id).value, 10);
            value = isNaN(value) ? 0 : value;
            if (value > 1) {
                value--;
                document.getElementById('Quantity-' + id).value = value;
                document.getElementById('Quantity-hidden-' + id).value = value;
            }
        }*/

//        function getLocation() {
//             if (navigator.geolocation) {
//                 navigator.geolocation.getCurrentPosition(sendPositionToServer, handleGeoError);
//             } else {
//                 alert("Geolocation is not supported by this browser.");
//             }
//         }

//         function sendPositionToServer(position) {
//             var latitude = position.coords.latitude;
//             var longitude = position.coords.longitude;
// // Inside sendPositionToServer function
// console.log("Latitude: " + latitude + ", Longitude: " + longitude);

//             // Display coordinates on the HTML page
//             document.getElementById('latitude').textContent = latitude;
//             document.getElementById('longitude').textContent = longitude;

//             // Send the coordinates to the server using AJAX
//             var xhr = new XMLHttpRequest();
//             xhr.open("POST", "save_location.php", true);
//             xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
//             xhr.onreadystatechange = function() {
//                 if (xhr.readyState == 4 && xhr.status == 200) {
//                     console.log("Location saved");
//                 }
//             };
//             xhr.send("latitude=" + latitude + "&longitude=" + longitude);
//         }

//         function handleGeoError(error) {
//             switch(error.code) {
//                 case error.PERMISSION_DENIED:
//                     alert("User denied the request for Geolocation.");
//             // Disable the order button
//             var orderButtons = document.querySelectorAll('.add-to-cart-btn');
//             orderButtons.forEach(function(button) {
//                 button.disabled = true;
//             });
//                 case error.POSITION_UNAVAILABLE:
                   
//                     break;
//                 case error.TIMEOUT:
                  
//                     break;
//                 case error.UNKNOWN_ERROR:
                   
//                     break;
//             }
//         }

        // Call the function to get location when the script runs
        // getLocation();
       

</script>
<script>
    AOS.init();
</script>
</body>
</html>
