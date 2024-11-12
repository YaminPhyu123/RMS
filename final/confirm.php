<?php include('userconfig/constants.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Hello</h1> 
    <form action="" method="POST">
    <?php
       $item_price = 0;
    $total_amount = 0;
    if(isset($_POST)){
    if(isset($_SESSION['cart']))
    {

        foreach($_SESSION['cart'] as $key => $value)
            {
                $item_price = $value['Price']*$value['Quantity'];
                $total_amount = $total_amount + $item_price;
                
                

                $sn=$key+1;
          
              }  }
    }
    ?>
    <p class="card-text"><?php echo $total_amount; ?>Ks</p>
    </form>
   

      

</body>
</html>