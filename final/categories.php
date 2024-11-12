<?php include('userpartials-front/menu1.php'); 

$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
if(isset($tno)){}else{
    $tno = isset($_GET['tno']) ? filter_var($_GET['tno'], FILTER_SANITIZE_NUMBER_INT) : null;
        $_SESSION['tno']=$tno;} ?>
     
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
            <a href="index.php" class="nav-item nav-link ">Home</a>
            <div class="dropdown">
                <a href="<?php echo SITEURL; ?>categories.php" class="nav-item nav-link active">Categories</a>
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
            <a href="index.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link">Home</a>
            <div class="dropdown">
                <a href="<?php echo SITEURL; ?>categories.php?tno=<?php echo htmlspecialchars($tno); ?>" class="nav-item nav-link active">Categories</a>
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
    <section class="food-search text-center">
        <div class="container">
            
            <!-- <form action="<?php echo SITEURL; ?>food-search.php" method="POST">
                <input type="search" name="search" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form> -->

        </div>
    </section>
    <!-- fOOD sEARCH Section Ends Here -->

    <!-- CAtegories Section Starts Here -->
    <section class="categories">
        <div class="container">
         <!--   <h2 class="text-center">Explore Foods</h2>-->

            <?php 

                //Display all the cateories that are active
                //Sql Query
                $sql = "SELECT * FROM food_category WHERE status=1";

                //Execute the Query
                $res = mysqli_query($conn, $sql);

                //Count Rows
                $count = mysqli_num_rows($res);

                //CHeck whether categories available or not
                if($count>0)
                {
                    //CAtegories Available
                    while($row=mysqli_fetch_assoc($res))
                    {
                        //Get the Values
                        $id = $row['f_id'];
                        $title = $row['category_name'];
                        $image_name = $row['category_image'];
                        ?>
                        
                        <a href="<?php echo SITEURL; ?>category-foods.php?f_id=<?php echo $id; ?>">
                            <div class="box-3 float-container">
                                <?php 
                                    if($image_name=="")
                                    {
                                        //Image not Available
                                        echo "<div class='error'>Image not found.</div>";
                                    }
                                    else
                                    {
                                        //Image Available
                                        ?>
                                        <img src="<?php echo SITEURL; ?>admin/assets/img/menu/<?php echo $image_name; ?>" alt="Pizza" class="img-responsive img-curve" width="250" height="250" loading="lazy"data-aos="fade-up" data-aos-delay="100">
                                        <?php
                                    }
                                ?>
                                <br><br><br><br><br><br><br>

                                <h3 class="float-text text-black"><?php echo $title; ?></h3>
                            </div>
                        </a>

                        <?php
                    }
                }
                else
                {
                    //CAtegories Not Available
                    echo "<div class='error'>Category not found.</div>";
                }
            
            ?>
            

            <div class="clearfix"></div>
        </div>
    </section>
    <!-- Categories Section Ends Here -->

    <script>
    AOS.init();
</script>
            </body>
            </html>
