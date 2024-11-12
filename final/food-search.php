
<?php include('userpartials-front/menu.php');
include('userpartials-front/topnav.php');
$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder"; 
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
$isLocationAllowed=false;
function isInsideShop() {
    $shopLatitude=21.9480064;
    $shopLongitude=96.0987136;
//     $shopLatitude = 20.1241008;
//    $shopLongitude = 94.9970469; 
   $allowedRadius = 0.03; // 1km

   if (!isset($_SESSION['user_latitude']) || !isset($_SESSION['user_longitude'])) {
       return false;
   }
   $userLatitude = $_SESSION['user_latitude'];
   $userLongitude = $_SESSION['user_longitude'];

   // Calculate the distance
   $distance = sqrt(pow($userLatitude - $shopLatitude, 2) + pow($userLongitude - $shopLongitude, 2));
   return $distance <= $allowedRadius;
}
if (isset($_SESSION['user_latitude']) && isset($_SESSION['user_longitude'])) {
   $isLocationAllowed = isInsideShop();
   echo "isInsideShop() result: " . ($isLocationAllowed ? "Inside shop" : "Outside shop or location not set");
   
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
    } }?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Restaurant Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

 

    <!-- Google Web Fonts 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />-->

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/corestyle.css" rel="stylesheet">
</head>

<body>
    
<!-- fOOD sEARCH Section Starts Here -->
<section class="food-search text-center">
    <div class="container">
        <?php 

           
            $search = mysqli_real_escape_string($conn, $_POST['search']);
        
        ?>


        <h2>Foods on Your Search <a href="#" class="text-white">"<?php echo $search; ?>"</a></h2>

    </div>
</section>
<!-- fOOD sEARCH Section Ends Here -->



<!-- fOOD MEnu Section Starts Here -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <?php 

            //SQL Query to Get foods based on search keyword
            //$search = burger '; DROP database name;
            // "SELECT * FROM tbl_food WHERE title LIKE '%burger'%' OR description LIKE '%burger%'";
            $sql = "SELECT * FROM foods WHERE food_name LIKE '%$search%' ";

            //Execute the Query
            $res = mysqli_query($conn, $sql);

            //Count Rows
            $count = mysqli_num_rows($res);

            //Check whether food available of not
            if($count>0)
            {
                //Food Available
                while($row=mysqli_fetch_assoc($res))
                {
                    //Get the details
                    $id = $row['food_id'];
                    $itemid=$row['f_id'];
                    $title = $row['food_name'];
                    $price = $row['food_price'];
                    $image_name = $row['image'];
                    ?>

                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php 
                                // Check whether image name is available or not
                                if($image_name=="")
                                {
                                    //Image not Available
                                    echo "<div class='error'>Image not Available.</div>";
                                }
                                else
                                {
                                    //Image Available
                                    ?>
                                    <img src="<?php echo SITEURL; ?>admin/assets/img/menu/<?php echo $image_name; ?>" alt="Chicke Hawain Pizza" class="img-responsive img-curve">
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
<br>
                                    <?php if ($preorderMode): ?>
                                        <!-- Show the preorder form -->
                                        <form action="foods.php" method="POST">
                                        <?php 
                                        $reservationId=$_SESSION['reservation_id'];
                                        $isDisabled = ($itemid == 17 && !isReservationTimeAllowed($reservationId)) ? 'disabled' : '';
  ?>
  <button type="submit"  name="add_to_pre" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $id; ?>" <?php echo $isDisabled; ?>>
      Add To Preorder
  </button>                                            <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                            <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                            <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                            <input type="hidden" id="Quantity-hidden-<?php echo $id; ?>" name="Quantity" value="1">
                                        </form>
                                    <?php else: ?>
                                        <!-- Show the order form -->
                                        <form action="foods.php" method="POST">

                                        <?php $isDisabled = !$isLocationAllowed || ($itemid == 17 && checkTime()) ? 'disabled' : '';
  ?>
  <button type="submit" id="orderButton" name="add_to_order" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $id; ?>" <?php echo $isDisabled; ?>>
      Order Now
  </button>                                            <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                            <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                            <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="Item" value="<?php echo $itemid; ?>">
                                            <input type="hidden" id="Quantity-hidden-<?php echo $id; ?>" name="Quantity" value="1">
                                        </form>
                                    <?php endif; ?>

                                </div>
                            
                                    </div>

                    <?php
                }
            }
            else
            {
                //Food Not Available
                echo "<div class='error'>Food not found.</div>";
            }
        
        ?>

     
        </div>
</section>
<!-- fOOD Menu Section Ends Here -->
<script>
      
      function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(sendPositionToServer, handleGeoError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function sendPositionToServer(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
// Inside sendPositionToServer function
console.log("Latitude: " + latitude + ", Longitude: " + longitude);

            // Display coordinates on the HTML page
            document.getElementById('latitude').textContent = latitude;
            document.getElementById('longitude').textContent = longitude;

            // Send the coordinates to the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "save_location.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log("Location saved");
                }
            };
            xhr.send("latitude=" + latitude + "&longitude=" + longitude);
        }

        function handleGeoError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    var orderButtons = document.querySelectorAll('.add-to-cart-btn');
            orderButtons.forEach(function(button) {
                button.disabled = true;
            });
                    break;
                case error.POSITION_UNAVAILABLE:
                   
                    break;
                case error.TIMEOUT:
             
                    break;
                case error.UNKNOWN_ERROR:
              
                    break;
            }
        }

        // Call the function to get location when the script runs
        getLocation();


</script>
</body>
</html>
