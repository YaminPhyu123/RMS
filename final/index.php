<?php include('userpartials-front/menu1.php'); 
// if(isset($_SESSION['table_number'])){

// }
if(isset($tno)){}else{
    $tno = isset($_GET['tno']) ? filter_var($_GET['tno'], FILTER_SANITIZE_NUMBER_INT) : null;
        $_SESSION['tno']=$tno;}
// else{
    // $tno = isset($_SESSION['tno']) ? $_SESSION['tno'] : null;
// }
date_default_timezone_set("Asia/Rangoon");

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
                // $food=$item['Quantity'];
                //$foodname=$item['Item_Name']
                // echo "<script>
                // alert($foodname$food); 
               
                // </script>"; 
                // echo "<script>
                // alert('Food Already In Order List'); 
               
                // </script>"; 
                 
               }
               else
              {

            
            $count = count($_SESSION['OrderCart']); 
            $_SESSION['OrderCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            // echo "<script>
            //      window.location.href='foods.php';
            //     </script>";
            echo "<script>
            window.location.href =  window.history.back();

             </script>";
                
               }
            

        }
        else
        {
            $_SESSION['OrderCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
            window.location.href = window.history.back();

             </script>";
        }

    }elseif(isset($_POST['add_to_pre']))
    {
        if(isset($_SESSION['PreCart']))
        {
            
           
            $myitems = array_column($_SESSION['PreCart'],'Item_Name');
            if(in_array($_POST['Item_Name'], $myitems))
            {
                
                foreach ($_SESSION['PreCart'] as &$item) {
                    if ($item['Item_Name'] == $_POST['Item_Name']) {
                        $item['Quantity'] += $_POST['Quantity'];
                        break;
                    }
                }
                // $food=$item['Quantity'];
                // echo "<script>
                // alert($food); 
               
                // </script>"; 
                 
            }
            else
            {

            
            $count = count($_SESSION['PreCart']); 
            $_SESSION['PreCart'][$count] = array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
           window.location.href = window.history.back();

         
         </script>";
                
            }
            

        }
        else
        {
            $_SESSION['PreCart'][0]=array('Item_Name'=>$_POST['Item_Name'],'Price'=>$_POST['Price'],'Id'=>$_POST['Id'],'Quantity'=>$_POST['Quantity']);
            echo "<script>
    window.location.href = window.history.back();

         
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

$isLocationAllowed=true;
// function isInsideShop() {
//     $shopLatitude=21.9480064;
//     $shopLongitude=96.0987136;

// //    $shopLatitude =20.1410775;
// //    $shopLongitude =94.9974563;

//    $allowedRadius = 0.03; 

//    if (!isset($_SESSION['user_latitude']) || !isset($_SESSION['user_longitude'])) {
//     $isLocationAllowed = false;
//    }
//    $userLatitude = $_SESSION['user_latitude'];
//    $userLongitude = $_SESSION['user_longitude'];

//    // Calculate the distance
//    $distance = sqrt(pow($userLatitude - $shopLatitude, 2) + pow($userLongitude - $shopLongitude, 2));
//    return $distance <= $allowedRadius;
// }
// if (isset($_SESSION['user_latitude']) && isset($_SESSION['user_longitude'])) {
//    $isLocationAllowed = isInsideShop();
// //    echo "isInsideShop() result: " . ($isLocationAllowed ? "Inside shop" : "Outside shop or location not set");
   
// }
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
        $allowedStartTime = strtotime('06:00'); 
        $allowedEndTime = strtotime('10:00');
        
        $reservationTimeUnix = strtotime($reservationTime);
        
        if ($reservationTimeUnix >= $allowedStartTime && $reservationTimeUnix <= $allowedEndTime) {
            return true; 
        } else {
            return false; 
        }
    } }

?>
    
    
<head>
    <meta charset="utf-8">
    <title>Restaurant Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
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
 

  
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" /> -->

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/corestyle.css" rel="stylesheet">
</head>
<style>
    
        /* Container styling */
.food-menu-section {
    background-color: white; /* Dark background similar to your image */
    padding: 40px 0;
    text-align: center;
}

.food-menu-section h2 {
    color:  #ffa600; /* Gold color for the title */
    font-size: 36px;
    text-transform: uppercase;
    margin-bottom: 30px;
}

/* Food item container */
.food-menu-container {
    display: flex;
    margin-left: 20px;
    flex-wrap: wrap;
}

/* Individual food item styling */
.food-menu-item {
    background-color: #333;
    padding: 20px;
    border-radius: 15px;
    width: 180px;
    margin: 10px;
    transition: transform 0.3s;
}

.food-menu-item:hover {
    transform: scale(1.05);
}

.food-menu-img1 {
    width: 100px;
    height: 100px;
    margin-bottom: 15px;
    border-radius: 50%;
    border: 4px solid #444;
}

.food-menu-item h3 {
    color: #ffa600;
    font-size: 18px;
    margin-bottom: 10px;
}

.food-menu-item p {
    color: #ddd;
    font-size: 14px;
}

/* Media Queries for responsiveness */
@media screen and (max-width: 768px) {
    .food-menu-container {
        flex-direction: column;
        align-items: center;
    }

    .food-menu-item {
        width: 80%;
    }
}

     .text-green {
    background-color: green;
    color:white;
}

.text-default {
    background-color: #2d3b5f; 
    color:white;
}

    .active{
        background: color #45637d;
        color:white;
    }
    </style>
<body>
    <body>
    <!-- Navbar Section Starts Here -->
    <div class="container-xxl bg-white p-0">
        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-static p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0 sticky-top">
              <!--  <a href="<?php echo SITEURL; ?>" class="navbar-brand p-0">
                    <img src="../images/logo.png" alt="Logo">
                </a>-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">

                    <?php if ($tno === null): ?>
            <a href="index.php" class="nav-item nav-link active">Home</a>
            <div class="dropdown">
                <a href="<?php echo SITEURL; ?>categories.php" class="nav-item nav-link">Categories</a>
                <div class="dropdown-content">
                    <?php
                    $sql = "SELECT f_id, category_name FROM food_category";
                    $res = mysqli_query($conn, $sql);
                    $categories = array();
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $id = $row['f_id'];
                            $title = $row['category_name'];
                            $categories[$id] = $title;
                        }
                    }
                    foreach ($categories as $id => $category_name) {
                        echo "<a href='" . SITEURL . "category-foods.php?f_id=$id'>$category_name</a>";
                    }
                    ?>
                </div>
            </div>
            <a href="foods.php" class="nav-item nav-link">Foods</a>
        <?php else: ?>
            <a href="index.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link active">Home</a>
            <div class="dropdown">
                <a href="<?php echo SITEURL; ?>categories.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link">Categories</a>
                <div class="dropdown-content">
                    <?php
                    $sql = "SELECT f_id, category_name FROM food_category";
                    $res = mysqli_query($conn, $sql);
                    $categories = array();
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $id = $row['f_id'];
                            $title = $row['category_name'];
                            $categories[$id] = $title;
                        }
                    }
                    foreach ($categories as $id => $category_name) {
                        echo "<a href='" . SITEURL . "category-foods.php?f_id=$id&tno=" . htmlspecialchars($tno) . "'>$category_name</a>";
                    }
                    ?>
                </div>
            </div>
            <a href="foods.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link">Foods</a>
        <?php endif; ?>
                              <!--reservation tin p yin htet tin ma ya aung-->
               <?php
               
               if (isset($_SESSION['preorder']) && $_SESSION['preorder'] == 'preorder') {
                   ?>
                       <?php if ($tno === null): ?>
                        <a href="<?php echo SITEURL; ?>reservationform.php" class="nav-item nav-link "style="opacity: 0.5;">Reservation</a>
            <?php else: ?>
            <a href="<?php echo SITEURL; ?>reservationform.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link disabled"style="opacity: 0.5;">Reservation</a>             
                <?php endif;?>
                <?php
               } else {?>
                <?php if ($tno === null): ?>
                    <a href="<?php echo SITEURL; ?>reservationform.php" class="nav-item nav-link ">Reservation</a>
                    <?php else: ?>
            <a href="<?php echo SITEURL; ?>reservationform.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link disabled">Reservation</a>             
                <?php endif;?>
        
                       <?php
               }
               ?>
                    </div>
                    
                          <?php
                          $co = 0;
                          if (isset($_SESSION['OrderCart'])) {
                              $co = count($_SESSION['OrderCart']);}
                              $cp = 0;
                              if (isset($_SESSION['PreCart'])) {
                                  $cp= count($_SESSION['PreCart']);
                              }
                          
                          
                          ?>
                                            
                    <?php if ($preorderMode): ?>

<?php 
// Determine the class based on the value of $cp
$preorderClass = $cp > 0 ? 'text-green' : 'text-default'; 
?>
<a href="preorderlist.php" class="btn btn-primary py-2 px-4 <?php echo $preorderClass; ?>">
    <span>Pre Order list <i class="fas fa-list-alt" style="font-size: 100%;"></i> <?php echo $cp; ?></span>
</a>

<?php else: ?> 
<?php 
// Determine the class based on the value of $co
$orderClass = $co > 0 ? 'text-green' : 'text-default'; 
?>
<a href="orderlist.php" class="py-2 px-4 <?php echo $orderClass; ?>">
    <span>My Order <i class="fas fa-list-alt" style="font-size: 100%;"></i> <?php echo $co; ?></span>
</a>
<?php endif; ?>
                </div>
            </nav>
        </div>

    <!-- fOOD sEARCH Section Starts Here -->


  <!-- ======= Hero Section ======= -->
  <section id="hero" class="food-search2 d-flex align-items-center">
    <div class="container position-relative text-center text-lg-start">
      <div class="row">
        <div class="col-lg-8" data-aos="zoom-in" data-aos-delay="200">
          <h1>Welcome to <span>Restaurant</span></h1>


          <div class="btns">
          <?php if ($tno === null): ?>
            <a href="foods.php" class="btn-menu animated fadeInUp scrollto">Our Menu</a>
            <a href="reservationform.php" class="btn-book animated fadeInUp scrollto">Reservation</a>
            <?php else: ?>
            <a href="foods.php?tno=<?php echo htmlspecialchars($tno); ?>" class="btn-menu animated fadeInUp scrollto">Our Menu</a>
            <a href="reservationform.php?tno=<?php echo htmlspecialchars($tno); ?>" class="btn-book animated fadeInUp scrollto">Reservation</a>
            <?php endif;?>
          </div>
        </div>
        <div class="col-lg-4 d-flex align-items-center justify-content-center position-relative" data-aos="zoom-in" data-aos-delay="200">
        
      </div>
    </div>

    </section>
    <!-- fOOD sEARCH Section Ends Here -->

    <!-- fOOD MEnu Section Starts Here -->
    <section class="food-menu-section">
    <h2>Popular Menu</h2>

            <?php 
            
            //Getting Foods from Database that are active and featured
            //SQL Query
            // $sql2 = "SELECT * FROM foods WHERE status=1 LIMIT 6";
           $sql2 = " SELECT *, SUM(so.quantity) AS total_quantity
            FROM foods f
            INNER JOIN selection_order so ON so.food_id = f.food_id
            INNER JOIN transaction t ON so.t_id = t.tid
            GROUP BY so.food_id
            ORDER BY total_quantity DESC
            LIMIT 6";
            //Execute the Query
            $res2 = mysqli_query($conn, $sql2);

            //Count Rows
            $count2 = mysqli_num_rows($res2);

            //CHeck whether food available or not
            if($count2>0)
            {
                //Food Available
                while($row=mysqli_fetch_assoc($res2))
                {
                    //Get all the values
                    $id = $row['food_id'];
                    $itemid=$row['f_id'];
                    $title = $row['food_name'];
                    $price = $row['food_price'];
                    $image_name = $row['image'];
                    ?>

<div class="food-menu-container">
<div class="food-menu-item">
                            <?php 
                                //Check whether image available or not
                                if($image_name=="")
                                {
                                    //Image not Available
                                    echo "<div class='error'>Image not available.</div>";
                                }
                                else
                                {
                                    //Image Available
                                    ?>
                                    <img src="<?php echo SITEURL; ?>admin/assets/img/menu/<?php echo $image_name; ?>" alt="Chicke Hawain Pizza" class="img-responsive img-curve food-menu-img1"
                                    >
                                    <?php
                                }
                            ?>
                            
                 

                 
                            <h3><?php echo $title; ?></h3>
                            <p class="food-price"><?php echo $price; ?> Ks</p>
                            <br>

                            <?php if ($preorderMode): ?>
                                        <!-- Show the preorder form -->
                                        <form action="index.php" method="POST">
                                        <?php 
                                        $reservationId=$_SESSION['reservation_id'];
                                        $isDisabled = ($itemid == 17 && !isReservationTimeAllowed($reservationId)) ? 'disabled' : '';
  ?>
  <button type="submit"  name="add_to_pre" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $id; ?>" <?php echo $isDisabled; ?>>
      Add To Preorder
  </button>
                                                             <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                            <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                            <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                            <input type="hidden" id="Quantity-hidden-<?php echo $id; ?>" name="Quantity" value="1">
                                        </form>
                                    <?php else: ?>
                                        <!-- Show the order form -->
                                        <form action="index.php" method="POST">
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

                    <?php
                }
            }
            else
            {
                //Food Not Available 
                echo "<div class='error'>Food not available.</div>";
            }
            
            ?>

            

 

            <div class="clearfix"></div>

            

        </div>

       
</section>
<!-- fOOD Menu Section Ends Here -->
 
  <script>  

// function getLocation() {
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
//                     var orderButtons = document.querySelectorAll('.add-to-cart-btn');
//             orderButtons.forEach(function(button) {
//                 button.disabled = true;
//             });
//                     break;
//                 case error.POSITION_UNAVAILABLE:
//                     // alert("Location information is unavailable.");
//                     break;
//                 case error.TIMEOUT:
//                     // alert("The request to get user location timed out.");
//                     break;
//                 case error.UNKNOWN_ERROR:
//                     // alert("An unknown error occurred.");
//                     break;
//             }
//         }

//         // Call the function to get location when the script runs
//         getLocation();

</script>
<script>
    AOS.init();
</script>
</body>
</html>