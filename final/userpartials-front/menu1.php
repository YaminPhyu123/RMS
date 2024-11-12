<?php include('userconfig/constants.php');
        $tno = isset($_SESSION['tno']) ? $_SESSION['tno'] : null; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Important to make website responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    
    <!-- Link our CSS file -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar Section Starts Here -->
    <section class="navbar" style="display:none">
        <div class="container">
            

            <div class="menu text-right">
            <?php if ($tno === null): ?>
                <ul>
                    <li >
                        <a  href="<?php echo SITEURL; ?>index.php">Home</a>
                    </li>
                    
                    <li >
                    <div class="dropdown">
                        <a  href="<?php echo SITEURL; ?>categories.php">Categories</a>
                            <div class="dropdown-content">
                                <?php
                                    $sql = "SELECT f_id, category_name FROM food_category";
                                            $res = mysqli_query($conn, $sql);

                                            // Initialize an empty array to store categories
                                            $categories = array();

                                            // Check if there are results
                                            if (mysqli_num_rows($res) > 0) {
                                                // Loop through each row and store data in $categories array
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    $id = $row['f_id'];
                                                    $title = $row['category_name'];
                                                    $categories[$id] = $title;
                                                }
                                            } 
                                            foreach ($categories as $id => $category_name) {
                                                echo "<a href='" . SITEURL . "category-foods.php?f_id=$id&tno=$tno'>$category_name</a>";
                                            }
                                ?>
                            </div>
                 </div>
                    </li>
                    
                    <li >
                        <a   href="<?php echo SITEURL; ?>foods.php" >Foods</a>
                    </li>
               

                    <?php else: ?>
                    <ul>
                    <li >
                        <a  href="<?php echo SITEURL; ?>index.php?tno=<?php echo $tno; ?>">Home</a>
                    </li>
                    
                    <li >
                    <div class="dropdown">
                        <a  href="<?php echo SITEURL; ?>categories.php?tno=<?php echo $tno; ?>">Categories</a>
                            <div class="dropdown-content">
                                <?php
                                    $sql = "SELECT f_id, category_name FROM food_category";
                                            $res = mysqli_query($conn, $sql);

                                            // Initialize an empty array to store categories
                                            $categories = array();

                                            // Check if there are results
                                            if (mysqli_num_rows($res) > 0) {
                                                // Loop through each row and store data in $categories array
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    $id = $row['f_id'];
                                                    $title = $row['category_name'];
                                                    $categories[$id] = $title;
                                                }
                                            } 
                                            foreach ($categories as $id => $category_name) {
                                                echo "<a href='" . SITEURL . "category-foods.php?f_id=$id&tno=$tno'>$category_name</a>";
                                            }
                                ?>
                            </div>
                 </div>
                    </li>
                    
                    <li >
                        <a   href="<?php echo SITEURL; ?>foods.php?tno=<?php echo $tno; ?>"class="active" >Foods</a>
                    </li>
                <ul>
                    <li >
                        <a  href="<?php echo SITEURL; ?>index.php?tno=<?php echo $tno; ?>" >Home</a>
                    </li>
                    
                    <li >
                    <div class="dropdown">
                        <a  href="<?php echo SITEURL; ?>categories.php?tno=<?php echo $tno; ?>">Categories</a>
                            <div class="dropdown-content">
                                <?php
                                    $sql = "SELECT f_id, category_name FROM food_category";
                                            $res = mysqli_query($conn, $sql);

                                            // Initialize an empty array to store categories
                                            $categories = array();

                                            // Check if there are results
                                            if (mysqli_num_rows($res) > 0) {
                                                // Loop through each row and store data in $categories array
                                                while ($row = mysqli_fetch_assoc($res)) {
                                                    $id = $row['f_id'];
                                                    $title = $row['category_name'];
                                                    $categories[$id] = $title;
                                                }
                                            } 
                                            foreach ($categories as $id => $category_name) {
                                                echo "<a href='" . SITEURL . "category-foods.php?f_id=$id&tno=$tno'>$category_name</a>";
                                            }
                                ?>
                            </div>
                 </div>
                    </li>
                    
                    <li >
                        <a   href="<?php echo SITEURL; ?>foods.php?tno=<?php echo $tno; ?>" class="active">Foods</a>
                    </li>

                    <?php endif; ?>

                    <li >
                        <a  href="<?php echo SITEURL; ?>reservationform.php" >Reservation</a>
                    </li>
                    <li>
                      
                     <?php
                        $count=0;
                        if(isset($_SESSION['cart']))
                        {
                            $count=count($_SESSION['cart']);
                        }
                    
                    ?>
                    <a href="myorders.php" class="btn btn-primary py-2 px-4"><i class="fas fa-shopping-cart"></i><span> My Order <?php echo $count; ?></span></a>
                    </li>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>
  
    <!-- Navbar Section Ends Here -->
</body>
</html>