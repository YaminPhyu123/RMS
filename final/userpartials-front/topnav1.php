<?php
$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
$tno = isset($_SESSION['tno']) ? $_SESSION['tno'] : null;?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Restaurant Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

 

    <!-- Google Web Font
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">


    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

-->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/corestyle.css" rel="stylesheet">
</head>
<style>
    .active{
        background: color #45637d;
        color:white;
    }
    </style>
<body>
    
    <div class="container-xxl bg-white p-0">
        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-static p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
              <!--  <a href="<?php echo SITEURL; ?>" class="navbar-brand p-0">
                    <img src="../images/logo.png" alt="Logo">
                </a>-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
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
            <a href="foods.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-lin active">Foods</a>
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
                    <?php if ($preorderMode): ?><!-- nav order/preorddder a sa-->
                        
 <a href="preorderlist.php" class="btn btn-primary py-2 px-4"><span>Pre Order list <i class="fas fa-list-alt" style="font-size: 100%;"></i> <?php echo $cp; ?></span></a>

                    <?php else: ?> 
<a href="orderlist.php" class="btn btn-primary py-2 px-4"><span> My Order <i class="fas fa-list-alt" style="font-size: 100%;"></i>  <?php echo $co; ?></span></a>
                    <?php endif; ?>
                </div>
            </nav>

         
        </div>
        <!-- Navbar & Hero End -->