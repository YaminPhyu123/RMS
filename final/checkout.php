<?php
// Process the cart data (this is a simplified example)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = $_POST['cart'];
    $total = $_POST['total'];
    
    // Example: Save the order to a database or send order confirmation via email
    // In a real application, handle payment processing and order fulfillment
    
    // Clear the cart after processing the order
    $cart = [];
    $total = 0.00;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .confirmation {
            border: 1px solid #ccc;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h2>Order Confirmation</h2>
    
    <div class="confirmation">
        <h3>Thank you for your order!</h3>
        
        <h4>Order Details:</h4>
        <ul>
            <?php foreach ($cart as $item): ?>
                <li><?= $item['name'] ?> - $<?= number_format($item['price'], 2) ?></li>
            <?php endforeach; ?>
        </ul>
        
        <p>Total: $<?= number_format($total, 2) ?></p>
    </div>
</body>
</html>
