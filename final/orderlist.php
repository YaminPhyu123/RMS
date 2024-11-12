<?php require('userconfig/constants.php'); 
$preorderMode = isset($_SESSION['preorder']) && $_SESSION['preorder'] === "preorder";
   
if (isset($_SESSION['tno'])) {
    $tno = $_SESSION['tno'];
}
$tno = isset($_SESSION['tno']) ? $_SESSION['tno'] : NULL;
$STN = isset($_POST["TableID"]) ? $_POST["TableID"] : NULL;
$tableNumber1 = !empty($STN) ? $STN : $tno;
if (!isset($_SESSION['pagebackcount'])) {
    $_SESSION['pagebackcount'] = 1;
} 
$pagebackcount = isset($_SESSION['pagebackcount']) ? $_SESSION['pagebackcount'] : 1;
?>

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

     Icon Font Stylesheet 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

     Libraries Stylesheet 
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" /> -->

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/corestyle.css" rel="stylesheet">
    <style>

   

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

     
        .footer {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
        .cent{
            float:center;
        }
    </style>

</head>
<body>  
<script>
   // Get references to the select element and the purchase button
const itemSelect = document.getElementById('TableID');
const purchaseButton = document.getElementById('purchaseBtn');

// Add an event listener to the select element
itemSelect.addEventListener('change', function() {
  // Check if an option other than the placeholder is selected
  if (itemSelect.value !== '') {
    purchaseButton.removeAttribute('disabled'); // Enable the purchase button
  } else {
    purchaseButton.setAttribute('disabled'); // Disable the purchase button
  }
});

</script>

    <div class="container-xxl bg-white p-0">
    
         <br><br id="br">
         <?php




// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize pagebackcount if not set
    if (!isset($_SESSION['pagebackcount'])) {
        $_SESSION['pagebackcount'] = 1;
    } else {
        $_SESSION['pagebackcount']++;
    }

    // Handle Remove_Item
    if (isset($_POST['Remove_Item'])) {
        if (isset($_SESSION['OrderCart']) && is_array($_SESSION['OrderCart'])) {
            foreach ($_SESSION['OrderCart'] as $key => $value) {
                if ($value['Item_Name'] == $_POST['Item_Name']) {
                    unset($_SESSION['OrderCart'][$key]);
                    $_SESSION['OrderCart'] = array_values($_SESSION['OrderCart']); // Reindex the array
                    break;
                }
            }
        }
        echo "<script>
        window.location.href =  window.history.back();
         </script>";
    }

    // Handle Mod_Quantity
    if (isset($_POST['Mod_Quantity'])) {
        if (isset($_SESSION['OrderCart']) && is_array($_SESSION['OrderCart'])) {
            foreach ($_SESSION['OrderCart'] as $key => $value) {
                if ($value['Item_Name'] == $_POST['Item_Name']) {
                    $_SESSION['OrderCart'][$key]['Quantity'] = sanitizeInput($_POST['Mod_Quantity']);
                    break; // Exit loop after modifying quantity
                }
            }
        }
        echo "<script>
        window.location.href =  window.history.back();
         </script>";
    }
   
}
?>

<button class="btn btn-custom btn-primary btn-outline-primary" 
        style="position: absolute; top: 20px; left: 20px; padding: 10px 20px; font-size: 16px; color: #fff; border: none; border-radius: 4px;"
        id="back"  name="back" >
    Back
</button>


    <div class="container-xxl bg-white p-0 " id="oc">
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

                            

                            if (isset($_SESSION['OrderCart'])) {
                                foreach ($_SESSION['OrderCart'] as $key => $value) {
                                    $item_price = $value['Price'] * $value['Quantity'];
                                    $total_amount += $item_price; // Accumulate total amount
                                
                                    $sn = $key + 1;
                                  
                                    echo "
                                        <tr>
                                            <td>$sn</td>
                                            <td>$value[Item_Name]</td>
                                            <td>$value[Price]</td>
                                            <td>
                                                <form  method='POST'>
                                                    <input class='text-center iquantity' name='Mod_Quantity' onchange='this.form.submit();' type='number' value='$value[Quantity]' min='1' max='20'>
                                                    <input type='hidden' name='Item_Name' value='$value[Item_Name]'>
                                                </form>
                                            </td>
                                            <td class='itotal'>$item_price</td> <!-- Display subtotal here -->
                                            <td>
                                                <form  method='POST'>
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
                
                <form action="orderlist.php" id="purchase" method="POST">        
        <?php
      

        // SQL query to retrieve all active table numbers
        $sql = "SELECT TableID FROM tables WHERE isAvailable = 1";
        $result = $conn->query($sql);

        if (isset($_SESSION['tno'])) {
            $tno = $_SESSION['tno'];
            echo 'table number:';
            echo $tno;
           
        }
        // else{
        // echo '<select name="TableID" id="TableID" required>';
        // echo '<option value=""><span>Select Table Number</span></option>';
        // if ($result->num_rows > 0) {
        //     while ($row = $result->fetch_assoc()) {
        //         echo '<option value="' . $row["TableID"] . '">' . $row["TableID"] . '</option>';
        //     }
        // // } else {
        // //     echo '<option value=""><span>No active tables found</span></option>';
        //  }
        // echo '</select>';
        // }
      
        ?>     
  <div class="col-md-2 " style="float:right"> <div class="d-grid gap-2 col-12 mx-auto"> 
  <?php if (isset($_SESSION['tno'])): ?>
    <button class="btn btn-custom btn-primary btn-outline-primary" style="color:white;" name="purchase" type="submit" id="purchaseBtn">Order</button>
<?php endif; ?>

</div>
    <!--  <span style="font-size: smaller; color: red;">reservations may be canceled if guests are more than 30 minutes late</span>-->
  </form>
  </div>        

                        </div>
                       
        </div>
        
    </div>
                    <!--receipt-->
                    <div style="max-width:65%;background-color: #2d3b5f;height: 600px;
background-color:white;font-size: 14px;font-family: tahoma;text-align: center;margin:auto;">
                     <div class=" position-relative" id="receipt"style="display:none">          
                      <div class="border bg-light rounded p-4">
            <!-- <p style="color:green;"> -->
            <p style="color:green;">table: <?php echo $tableNumber1 ?> order successful</p>
     <h1>Receipt</tr></h1>
    <form action="orderlist.php" id="receipt" method="POST">                    
    <table class="table" id="cart_table">
                      
    
                                <th scope="col">Item Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Sub Total</th>
                                <th scope="col"></th>
   <tbody class="text-center">
                            <?php
                            $item_price = 0;
                            $total_amount = 0;

                            if (isset($_SESSION['OrderCart'])) {
                                foreach ($_SESSION['OrderCart'] as $key => $value) {
                                    $item_price = $value['Price'] * $value['Quantity'];
                                    $total_amount += $item_price; // Accumulate total amount
                                
                                    $sn = $key + 1;
                                
                                    echo "
                                        <tr>
                                            <td>$value[Item_Name]</td>
                                            <td>$value[Price]</td>
                                            <td>$value[Quantity]
                                            </td>
                                            <td class='itotal'>$item_price</td> <!-- Display subtotal here -->
                                          
                                        </tr>";
                                }
                                
                            }
                            ?>
                                    </tbody>
                                    </table>
    <h3><class="text-align"style="text-align:center">Total:
    <class="text-center" id="gtotal"><?php echo number_format($total_amount, 2); ?><b></h3>
   
                  
        <div class="footer">
        <p>Thank you!</p>
       </div>
     <button class="btn btn-custom btn-primary btn-outline-primary" style="color:white; float:middle;"name="ok" id="ok"type="submit">Ok</button>
                        </form>
                        </div>
                        </div>
                    
                        </div>
                     
     <!--receipt end-->

    <!-- JavaScript Libraries -->
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
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
    
     if (isset($_POST['purchase'])){
 
    if ($total_amount>0) 
    {
       
      echo "<script>

document.getElementById('cart_table').style.display = 'none';  
document.getElementById('br').style.display = 'none'; 
document.getElementById('back').style.display = 'none';  
   document.getElementById('receipt').style.display = 'block'; 
      document.getElementById('purchase').style.display = 'none'; // Show the form
   </script>";
    
        $itemArrays = [];
        $transaction_id = generateTransactionID(5);
       // $reserve_id = $_SESSION['reservation_id'] ; 
      $pay='notpaid';



      if (empty($STN) && empty($tno)) {
        $query="INSERT INTO `transaction`(`tid`, `t_total`, `payment_status`)
        VALUES ('$transaction_id','$total_amount','$pay')";
    } else {
       
        // $query = "INSERT INTO `transaction`(`tid`,`tableId`,`t_total`, `payment_status`)
        //  VALUES (`','$STN',`$',`$pay`)"; 
        //mysqli_query($conn, $query);
       $query="INSERT INTO `transaction`(`tid`, `tableId`, `t_total`, `payment_status`)
        VALUES ('$transaction_id','$tableNumber1','$total_amount','$pay')";
    }

//table no select unactive loke
    // Validate selected table number (optional)

        // Update isActive to false for the selected table number   
 
        //SQL query to update isActive to false
$updateSqlTable = "UPDATE tables SET isAvailable = 0 WHERE TableID = ?";
$stmt = $conn->prepare($updateSqlTable);
   $stmt->bind_param("i", $tableNumber1);
   $stmt->execute();                                      

     //table reserved remove
    //  $sqlUpdateTable = "UPDATE tables SET table_status= 'not_reserved' WHERE TableID = ?";
    //  $stmt = $conn->prepare($sqlUpdateTable);
    //  $stmt->bind_param('i', $tableNumber1);
    //  $stmt->execute();
   
         
        if (isset($_SESSION['OrderCart']) && !empty($_SESSION['OrderCart'])) {
            foreach ($_SESSION['OrderCart'] as $item) {
              
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
                                                          unset($_SESSION['OrderCart']);
                                               
            $loop= count($itemArrays);
        
// Loop 

if(mysqli_query($conn, $query)){
for ($i = 0; $i <=$loop; $i++) {
    $item = $itemArrays[$i];
    $foodid = $item['Id'];
    $q= $item['Quantity'];


    $query1 = "INSERT INTO selection_order (t_id, r_id, food_id,quantity) 
          VALUES ('$transaction_id', null, '$foodid','$q')";
             mysqli_query($conn, $query1);
    $id='';
    $q='';
   
}}

}
     }

       //  unset($_SESSION['OrderCart']);
 //unset($_SESSION['reservation_id']);
//echo "<script>window.location.href='reservationform.php';</script>";

 



   if (isset($_POST['ok'])){
    
   unset($_SESSION['tno']);
   unset($_SESSION['tNo']);

    
       echo "<script>window.location.href='http://127.0.0.1/final/foods.php';</script>";
   
}
 
}

?>

  <script>
   
document.getElementById('back').addEventListener('click', handleBack);
function handleBack() {
    // var count = <?php echo $pagebackcount; ?>;
    // for (var i = 0; i <= count; i++) {

        setTimeout(function() {
            window.history.back();
        }, 1); 
    // }

    fetch('clear_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            action: 'clearPageBackCount'
        })
    });
}


    </script>

</body>
</html>