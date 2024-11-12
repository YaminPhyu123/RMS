<?php  include('userconfig/constants.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Access and sanitize the 'tno' parameter
if(isset($tno)){}else{
$tno = isset($_GET['tno']) ? filter_var($_GET['tno'], FILTER_SANITIZE_NUMBER_INT) : null;
    $_SESSION['tno']=$tno;}
// $tno = isset($tno) ? $tno : '';
// Validate if the parameter is a valid integer



$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$items_per_page = 15;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get the total number of items
$sql_total = "SELECT COUNT(*) as total FROM foods WHERE status = 1 AND food_name LIKE '%$search%'";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_items = $row_total['total']; // Total number of items in the database

// Modify SQL query to apply LIMIT and OFFSET
$sql1 = "SELECT * FROM foods WHERE status = 1 AND food_name LIKE '%$search%' LIMIT $items_per_page OFFSET $offset";
$res1 = mysqli_query($conn, $sql1);
$count = mysqli_num_rows($res1);

// Pagination controls
$total_pages = ceil($total_items / $items_per_page);

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
                // Example PHP variables
                $itemName = $item['Item_Name'];
                $quantity = $item['Quantity'];
                ?>
                
                <script>
                    alert('Added one more: <?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?> (Quantity: <?php echo htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?>)');
                </script>
                
                echo "<script>
                window.location.href =  window.history.back();
                 </script>";
    <?php
                 
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
//     // $shopLatitude=21.9480064;
//     // $shopLongitude=96.0987136;

//    $shopLatitude =20.1410775;
//    $shopLongitude =94.9974563;

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
    require 'admin/partials/_head.php';
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
    <link rel="stylesheet" href="css/style.css">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
 .text-green {
    background-color: green;
    color:white;
}

.text-default {
    background-color: #2d3b5f; 
    color:white;
}


 .suggestions {
    border: 1px solid #ddd;
    width: 41%;
    max-height: 150px;
    overflow-y: auto;
    position: absolute;
    left: 26.5%;
    background-color: rgba(255, 255, 255, 0.95);
    z-index: 1000;
    border-radius: 5px;
    margin-top: 5px;
    display: none;
}

.suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    font-size: 1rem;
    color: #333;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #eee;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover {
    background-color: rgba(240, 240, 240, 0.9);
}


    .active{
        background: color #45637d;
        color:white;
    }
    .pagination {
    display: flex;
    justify-content: center;
    padding: 8px 0;
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
                      
                    <!-- //null check -->
                    <?php if ($tno === null): ?>
            <a href="index.php" class="nav-item nav-link">Home</a>
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
            <a href="foods.php" class="nav-item nav-link active">Foods</a>
        <?php else: ?>
            <a href="index.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link">Home</a>
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
            <a href="foods.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link active">Foods</a>
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


<!--serach bar-->
            <section class="food-search text-center">
       
<div class="container">
            <form action="" method="GET">

                <input type="search"  id="searchBox" autocomplete="off" placeholder="Search for Food...." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit" name="submit"id="searchButton">Search</button>
                <div id="suggestionsBox" class="suggestions"></div>

            </form>
        </div>
  </section>
  <!--search bar end-->
        </div>
        <!-- Navbar & Hero End -->

        <!-- Menu Start -->
 <div class="container">
    <div class="row">
        <?php


        $count = mysqli_num_rows($res1);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($res1)) {
                $id = $row['food_id'];
                $itemid = $row['f_id'];
                $title = $row['food_name'];
                $price = $row['food_price'];
                $image_name = $row['image'];
                $tno=$_SESSION['tno'];
                ?>
    <div class="col-lg-3 col-md-3 col-sm-4 col-6 mb-4 custom-col">
                    <div class="card h-100"data-aos="fade" data-aos-delay="200">
                        <img src="<?php echo SITEURL; ?>admin/assets/img/menu/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive" width="100%" height="200">
                        <div class="card-body text-center" >

                            <h6 class="card-title"><?php echo $title; ?></h5>
                            <p class="card-text"><?php echo $price; ?>Ks</p>
                            <div class="Quantity-selector">
                                <div class="btn btn-custom minus-btn" onclick="decrementValue(<?php echo $id; ?>)">-</div>
                                <input type="integer" id="Quantity-<?php echo $id; ?>" name="Quantity" class="form-control text-center" value="1" min="1" disabled>
                                <div class="btn btn-custom plus-btn" onclick="incrementValue(<?php echo $id; ?>)">+</div>
                            </div>
                            <br>
                        </div>
                        <div class="card-footer text-center">
                            <?php if ($preorderMode): ?>
                                <form  method="POST">
                                    <?php 
                                    $reservationId=$_SESSION['reservation_id'];
                                    $isDisabled = ($itemid == 17 && !isReservationTimeAllowed($reservationId)) ? 'disabled' : '';
                                    ?>
                                    <button type="submit" name="add_to_pre" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $id; ?>" <?php echo $isDisabled; ?>>
                                        Add To Preorder
                                    </button>
                                    <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                    <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                    <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                    <input type="hidden" id="Quantity-hidden-<?php echo $id; ?>" name="Quantity" value="1">
                                </form>
                            <?php else: ?>
                                <form method="POST" id="orderForm"> 
                                    <?php $isDisabled = !$isLocationAllowed || ($itemid == 17 && checkTime()) ? 'disabled' : ''; ?>
                                    <button type="submit" id="orderButton" name="add_to_order" class="btn btn-primary btn-sm mt-2 add-to-cart-btn" data-item-id="<?php echo $id; ?>" <?php echo $isDisabled; ?>>
                                        Order Now
                                    </button>
                                    <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                    <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                    <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="Item" value="<?php echo $itemid; ?>">
                                    <input type="hidden" id="Quantity-hidden-<?php echo $id; ?>" name="Quantity" value="1">
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>food not found</p>";
        }
      
    
        $range = 30; // Adjust this number as needed

        // Calculate the starting and ending page numbers for the pagination range
        $start_page = max(1, $current_page - floor($range / 2));
        $end_page = min($total_pages, $current_page + floor($range / 2));
        
        // Adjust the start and end page if the range goes beyond the total number of pages
        if ($end_page - $start_page + 1 < $range) {
            $start_page = max(1, $end_page - $range + 1);
            $end_page = min($total_pages, $start_page + $range - 1);
        }
        
        // Generate the base URL for pagination links, including the search parameter if it exists
        $base_url = "foods.php?tno=".$tno."&page=";
    

        if (!empty($search)) {
            $base_url .= "&search=" . urlencode($search);
        }
        
        echo '<div class="pagination">'; // Start a container for pagination
        
        // Create "Previous" link
        if ($current_page > 1) {
            echo '<a href="' . $base_url . '&page=' . ($current_page - 1) . '">&laquo; Prev</a> ';
        } else {
            echo '<span class="disabled">&laquo; Prev</span> ';
        }
        
        // Create page number links
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="current-page">' . $i . '</span> ';
            } else {
                echo '<a href="' . $base_url . '&page=' . $i . '">' . $i . '</a> ';
            }
        }
        
        // Create "Next" link
        if ($current_page < $total_pages) {
            echo '<a href="' . $base_url . '&page=' . ($current_page + 1) . '">Next &raquo;</a> ';
        } else {
            echo '<span class="disabled">Next &raquo;</span> ';
        }
        
        echo '</div>'; // End container for pagination
        

        
?>
    </div>
</div>

        <!-- Menu Ends -->

    </div>

    <!-- JavaScript Libraries
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script> -->




           <script>
  document.getElementById('searchBox').addEventListener('input', function() {
    let query = this.value.trim();
    if (query.length > 0) {
        fetchSuggestions(query);
    } else {
        hideSuggestionsBox();
    }
});

function fetchSuggestions(query) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_suggestions.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            let suggestions = JSON.parse(this.responseText);
            let output = '';
            for (let i = 0; i < suggestions.length; i++) {
                output += `<div class="suggestion-item">${suggestions[i]}</div>`;
            }
            document.getElementById('suggestionsBox').innerHTML = output;
                document.getElementById('suggestionsBox').style.display = 'block';
            } else {
                hideSuggestionsBox();
            }
    
    };
    xhr.send('query=' + query);
}
function hideSuggestionsBox() {
    document.getElementById('suggestionsBox').innerHTML = '';
    document.getElementById('suggestionsBox').style.display = 'none';
}


document.getElementById('searchButton').addEventListener('click', function() {
    hideSuggestionsBox();
});

document.addEventListener('click', function(e) {
    if (!document.getElementById('searchBox').contains(e.target) &&
        !document.getElementById('suggestionsBox').contains(e.target)) {
        hideSuggestionsBox();
    }
});
document.getElementById('suggestionsBox').addEventListener('click', function(e) {
    if (e.target.classList.contains('suggestion-item')) {
        document.getElementById('searchBox').value = e.target.textContent;
        document.getElementById('suggestionsBox').innerHTML = '';
    }
});

document.addEventListener('DOMContentLoaded', function() {
        // Safely pass PHP variable to JavaScript
        const tno = <?php echo json_encode($tno); ?>;

        document.getElementById('food-search').addEventListener('input', function() {
            let query = this.value;

            if (query.length > 0) {
                fetch('search_foods.php?tno=' + encodeURIComponent(tno) + '&query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        let suggestions = document.getElementById('suggestions');
                        suggestions.innerHTML = '';

                        data.forEach(food => {
                            let div = document.createElement('div');
                            div.textContent = food;
                            div.addEventListener('click', function() {
                                document.getElementById('food-search').value = food;
                                suggestions.innerHTML = '';
                            });
                            suggestions.appendChild(div);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    });
            } else {
                document.getElementById('suggestions').innerHTML = '';
            }
        });
    });


       function incrementValue(id) {
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
        }


//         function getLocation() {
//             if (navigator.geolocation) {
//                 navigator.geolocation.getCurrentPosition(sendPositionToServer, handleGeoError);
//             } else {
//                 alert("Geolocation is not supported by this browser.");
//             }
//         }

//         function sendPositionToServer(position) {
//             var latitude = position.coords.latitude;
//             var longitude = position.coords.longitude;

// console.log("Latitude: " + latitude + ", Longitude: " + longitude);


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
             
//                     break;
//                 case error.TIMEOUT:
                   
//                     break;
//                 case error.UNKNOWN_ERROR:
                
//                     break;
//             }
//         }

  
//         getLocation();
   

</script>
<script>
    AOS.init();
</script>
</body>
</html>