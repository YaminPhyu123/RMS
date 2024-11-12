<?php

require 'userconfig/constants.php'; 
require 'barcode.php'; // Include barcode generation library

// Check if a valid transaction ID exists in the session
if (isset($_SESSION['transactionId'])) {
    
    $transactionId = $_SESSION['transactionId'];
    unset($_SESSION['transaction_id']);

    // Generate barcode using the transaction id
    $barcode = new Barcode();
    $barcode->generate($transactionId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Restaurant Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Include your CSS and other meta tags here -->

    <!-- Bootstrap and other CSS libraries -->
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet"> -->

    <!-- Font Awesome and Bootstrap Icons -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet"> -->

    <!-- Custom styles -->
    <link href="css/corestyle.css" rel="stylesheet">

    <!-- Inline styles for specific elements -->
<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f2f2f2;
    padding: 20px;
}

.receipt {
    background-color: #fff;
    max-width: 600px;
    margin: 0 auto;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    
}

.receipt h2 {
    text-align: center;
    color: #007bff;
}

.receipt .shop-info {
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.receipt .shop-info p {
    margin: 5px 0;
}

.receipt .items {
    margin-bottom: 20px;
}

.receipt .items table {
    width: 100%;
    border-collapse: collapse;
}

.receipt .items th, .receipt .items td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.receipt .items th {
    background-color: #f2f2f2;
}

.receipt .total {
    text-align: right;
    margin-top: 20px;
}

.receipt .payment-method {
    margin-top: 10px;
}

.receipt .barcode {
    text-align: center;
    margin-top: 20px;
}

.receipt .barcode img {
    max-width: 100%;
    height: auto;
}


.receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

.footer {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
 .bd{
    display: flex;
  justify-content: center; /* Center items horizontally */
  align-items: center; 
 }       
        .download-btn {
     
  display: inline-block; /* Ensures it's displayed as a block-level element */

  background-color: green; /* Green background */
  color: white; /* White text color */
  text-align: center; /* Centers text horizontally */
  cursor: pointer; /* Changes cursor to pointer on hover */
  transition: background-color 0.3s ease; /* Smooth transition effect */

  
}

.download-btn:hover {
  background-color: black; /* Darker green on hover */
}

    </style>
</head>

<body>
<!-- Receipt section -->
<div class="receipt">
       
            <h2>Receipt</h2><br>
        <div class="items">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Item Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;

                    if (isset($_SESSION['PreCart'])) {
                        foreach ($_SESSION['PreCart'] as $value) {
                            $item_price = $value['Price'] * $value['Quantity'];
                            $total_amount += $item_price;
                            echo "<tr>
                                    <td>$value[Item_Name]</td>
                                    <td>$value[Price]</td>
                                    <td>$value[Quantity]</td>
                                    <td class='itotal'>$item_price</td>
                                </tr>";
                        }
                        
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="total">
            <p>Total: <?php echo number_format($total_amount, 2); ?></p>
        </div>

        <div style="padding: 10px; background-color: #eee; font-size: 14px; font-family: tahoma; text-align: center;">
            <?php if (file_exists('barcode.jpg')): ?>
                <img src="barcode.jpg?<?= rand(0, 9999) ?>" style="border: solid thin #888; width:100%">
            <?php endif ?>
        </div>

        <div class="footer">
            <p>Thank you!</p>
        </div>

        <!-- Button to download receipt -->
        <div class="bd">
        <button onclick="downloadReceipt()" class="download-btn">Download Receipt</button>

        
            </div>
</div>
<!-- End of receipt section -->

    <!-- JavaScript Libraries -->
    <script src="path/to/html2pdf.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
<!-- 
    <script>
    function downloadReceipt() {
        var receipt = document.querySelector('.receipt');
        html2pdf()
            .from(receipt)
            .save('receipt.pdf')
            .then(function() {
              <?php  unset($_SESSION['PreCart']);?>
                console.log('PDF saved successfully');
                setTimeout(function() {
                    window.location.href = 'index.php';
                    }, 1000);
              
            })
            .catch(function(error) {
                console.error('Error while saving PDF:', error);
            });

          
    }
</script> -->
<script>
function downloadReceipt() {
    var receipt = document.querySelector('.receipt');
    html2pdf()
        .from(receipt)
        .save('receipt.pdf')
        .then(function() {
            // Make an AJAX call to unset the session variable
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'unset_precart.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('PDF saved and session unset successfully');
                    window.location.href = 'index.php';
                    // setTimeout(function() {
                    //     window.location.href = 'index.php';
                    // }, 1000);
                } else {
                    console.error('Error while unsetting session:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Request failed');
            };
            xhr.send();
        })
        .catch(function(error) {
            console.error('Error while saving PDF:', error);
        });
}
</script>

</body>

</html>
