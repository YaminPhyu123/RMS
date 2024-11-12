<?php require('userconfig/constants.php'); 
$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
    
if (!isset($_SESSION['pagebackcount2'])) {
    $_SESSION['pagebackcount2'] = 1; // Initialize the session variable
} 
$pagebackcount = isset($_SESSION['pagebackcount2']) ? $_SESSION['pagebackcount2'] : 1; 
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['pagebackcount2'])) {
        $_SESSION['pagebackcount2'] = 1;
    } else {
        $_SESSION['pagebackcount2']++;
    }

    if (isset($_POST['Remove_Item'])) {
      
        foreach ($_SESSION['PreCart'] as $key => $value) {
            if ($value['Item_Name'] == $_POST['Item_Name']) {
                unset($_SESSION['PreCart'][$key]);
                $_SESSION['PreCart'] = array_values($_SESSION['PreCart']);
                // echo "<script>window.location.href='preorderlist.php';</script>";
              
            }
        }
        echo "<script>
        window.location.href =  window.history.back();
         </script>";
    }

    if (isset($_POST['Mod_Quantity'])) {
        foreach ($_SESSION['PreCart'] as $key => $value) {
            if ($value['Item_Name'] == $_POST['Item_Name']) {
                $_SESSION['PreCart'][$key]['Quantity'] = sanitizeInput($_POST['Mod_Quantity']);
           
            }
        }
        echo "<script>
        window.location.href =  window.history.back();
         </script>";
    }


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
          btn-custom {
            background-color:green; 
            color: #fff; 
            border-color: green;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; 
        }
        
        .btn-custom:hover {
            background-color:black;
            border-color:black; 
            color: #fff; 
        }

        </style>
    <meta charset="utf-8">
    <title>My Restaurant</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">


    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" /> -->


    <link href="css/bootstrap.min.css" rel="stylesheet">


    <link href="css/style.css" rel="stylesheet">
</head>

<body >
    <div class="cart-button" style="position: relative; height: 80px">
        <!-- <button class="btn btn-custom btn-primary btn-outline-primary" style="position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px;color: #fff; border: none; border-radius: 4px;">
            <a href="foods.php" style="color: inherit; text-decoration: none;">Back</a>
        </button> -->
      
        <button id="back"  name="back" class="btn btn-custom btn-primary btn-outline-primary" style="position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px;color: #fff; border: none; border-radius: 4px;">Back</button>
      
            <form method="post">
        <button name="back_to_order_page" class="btn btn-custom btn-primary btn-outline-primary" style="color: inherit; text-decoration: none;
        position: absolute;top: 20px;left:100px; padding: 10px 20px; font-size: 16px;color: #fff; border: none;">
         Cancel Prechoose Foods
        </button>
    </form>

    <form method="post">
        <button name="cancel_reservation" class="btn btn-custom btn-primary btn-outline-primary" style="color: inherit; text-decoration: none;
        position: absolute;top: 20px;left:325px; padding: 10px 20px; font-size: 16px;color: #fff; border: none;">
         Cancel Reservation
        </button>
    </form>

    </div>
    <div class="container-xxl bg-white p-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- choose htar thaw foods-->
                </div>

                <div class="col-lg-9 table-responsive">
                    <table class="table" id="cart_table">
                        <thead class="text-center">
                            <tr>
                                <th scope="col">N0.</th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Sub Total</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $item_price = 0;
                            $total_amount = 0;

                            if (isset($_SESSION['PreCart'])) {
                                foreach ($_SESSION['PreCart'] as $key => $value) {
                                    $item_price = $value['Price'] * $value['Quantity'];
                                    $total_amount += $item_price; // Accumulate total amount
                                
                                    $sn = $key + 1;
                                
                                    echo "
                                        <tr>
                                            <td>$sn</td>
                                            <td>$value[Item_Name]</td>
                                            <td>$value[Price]</td>
                                            <td>
                                                <form action='preorderlist.php' method='POST'>
                                                    <input class='text-center iquantity' name='Mod_Quantity' onchange='this.form.submit();' type='number' value='$value[Quantity]' min='1' max='20'>
                                                    <input type='hidden' name='Item_Name' value='$value[Item_Name]'>
                                                </form>
                                            </td>
                                            <td class='itotal'>$item_price</td> <!-- Display subtotal here -->
                                            <td>
                                                <form action='preorderlist.php' method='POST'>
                                                    <button name='Remove_Item' class='btn btn-danger btn-sm'>REMOVE</button>
                                                    <input type='hidden' name='Item_Name' value='$value[Item_Name]'>
                                                </form>
                                            </td>
                                        </tr>";
                                }
                                
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-3">
                    <!-- Checkout Summary -->
                    <div class="border bg-light rounded p-4">
                        <h4 class="text-center">Total</h4>
                        <h2 class="text-center" id="gtotal"><?php echo number_format($total_amount, 2); ?></h2>
                        <br>
 <form action="preorderlist.php" id="payForm" method="POST">
                            
<div class="form-group">
          <!-- <div class="form-check">
         <input class="form-check-input" type="radio" name="pay_mode" value="cash" id="cash" required>
         <label class="form-check-label" for="cash">Pay With Cash</label><br>
         </div> -->
         <div class="form-check">
        <input class="form-check-input" type="radio" name="pay_mode" value="kpay" id="kpay" required>
        <label class="form-check-label" for="kpay">Pay With Kbz Pay</label><br>
        </div>
        </div>
     <div class="d-grid gap-2 col-12 mx-auto">
      <button class="btn btn-custom btn-primary btn-outline-primary" style="color:white;"name="purchase" type="submit" id="purchaseBtn">Order</button>
      </div>
      <span style="font-size: smaller; color: red;">reservations may be canceled if guests are more than 30 minutes late and no refund</span>
  </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script> -->


<?php

// transaction ID shar fho
function generateTransactionID($length=5) {
 $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';//par mae har
 
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['back_to_order_page'])){

        if (isset($_SESSION['preorder'])) {
            unset($_SESSION['preorder']);}
            $reserve_idupdate = isset($_SESSION['reservation_id']) ? $_SESSION['reservation_id'] : NULL;
            $queryupdate = "UPDATE `reservations`SET`pre_choose`='no' WHERE `id`='$reserve_idupdate';";
                 mysqli_query($conn, $queryupdate); 
             unset($_SESSION['reservation_id']);
             unset($_SESSION['PreCart']);
             echo "<script>window.location.href='foods.php';</script>";
            exit;
        }

        if (isset($_POST['cancel_reservation'])){

            if (isset($_SESSION['preorder'])) {
                unset($_SESSION['preorder']);}
                $reserve_iddelete = isset($_SESSION['reservation_id']) ? $_SESSION['reservation_id'] : NULL;
                $querydelete = "DELETE FROM `reservations` WHERE `id`='$reserve_iddelete';";
                     mysqli_query($conn, $querydelete); 
                 unset($_SESSION['reservation_id']);
                 unset($_SESSION['PreCart']);
                 echo "<script>window.location.href='foods.php';</script>";
                exit;
            }

    if (isset($_POST['purchase'])){
    $selectedOption = $_POST["pay_mode"];
    if ($total_amount>0) 
    {$reserve_id = isset($_SESSION['reservation_id']) ? $_SESSION['reservation_id'] : NULL;
       //unset($_SESSION['reservation_id']);
       
        
        $itemArrays = [];
        $transaction_id = generateTransactionID(5);
        $_SESSION['transactionId']=$transaction_id;
//transationtable htal htae
// if($selectedOption == "cash"){
//     $pay='notpaid';
// }else
 if($selectedOption == "kpay"){
    $pay='paid';
}

        $query = "INSERT INTO `transaction`(`tid`, `t_total`, `payment_status`)
         VALUES ('$transaction_id','$total_amount','$pay')"; 
         
        if (isset($_SESSION['PreCart']) && !empty($_SESSION['PreCart'])) {
            foreach ($_SESSION['PreCart'] as $item) {
                $item_name = $item['Item_Name'];
                $price = $item['Price'];
                $id = $item['Id'];

                $quantity = $item['Quantity'];
        
                // array fan tee
                $itemArray = [
                    'Item_Name' => $item_name,
                    'Price' => $price,
                    'Id' => $id,
                    'Quantity' => $quantity
                ];
                $itemArrays[] = $itemArray;
                                                 }
                                                          }
                                               
            $loop= count($itemArrays);
        
// Loop 

if(mysqli_query($conn, $query)){
for ($i = 0; $i < $loop; $i++) {
    $item = $itemArrays[$i];
    $foodid = $item['Id'];
    $q= $item['Quantity'];




    $query1 = "INSERT INTO selection_order (t_id, r_id, food_id,quantity) 
        VALUES ('$transaction_id', '$reserve_id', '$foodid','$q')";
         
             mysqli_query($conn, $query1); 

    $foodid='';
    $q='';
   
}}
  //  unset($_SESSION['PreCart']);
  unset($_SESSION['preorder']);
  echo "<script>window.location.href='receipt.php';</script>";
 } 

}
 
}
?>

<script>
  document.getElementById('back').addEventListener('click', handleBack);
function handleBack() {
    var count = <?php echo $pagebackcount; ?>;
    // for (var i = 0; i <= count; i++) {

        setTimeout(function() {
            window.history.back();
        }, 1); 
    // }

    fetch('clear_session2.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            action: 'clearPageBackCount2'
        })
    });
}
</script>

</body>

</html>
