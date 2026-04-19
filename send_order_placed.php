<?php
require_once __DIR__ . '/includes/customer_session.php';
include ('includes/config.php');

// Check if user is logged in
if (strlen($_SESSION['login']) == 0) {
    header('location:login.php');
} else {
    // Get userId and order_id from session or GET parameters
    $userId = $_SESSION['id'];
    $order_id = $_GET['order_id'];

    // SQL query to get order and user details
    $query = "
    SELECT 
    orders.id AS orderId,
    orders.userId,
    orders.orderDate,
    orders.paymentMethod,
    orders.orderStatus,
    orders.subtotal,
    orders.shippingCharge,
    orders.grandtotal,
    orders.shippingReceiver,
    orders.shippingPhone,
    orders.shippingAddress,
    orders.billingReceiver,
    orders.billingPhone,
    orders.billingAddress,
    users.name AS userName,
    users.email AS userEmail
FROM 
    orders
INNER JOIN 
    users ON orders.userId = users.id
WHERE 
    orders.userId = '$userId' AND
    orders.id = '$order_id';";

    $result = mysqli_query($con, $query);
    $orderDetails = mysqli_fetch_assoc($result);

    // Prepare email variables
    $userName = $orderDetails['userName'];
    $userEmail = $orderDetails['userEmail'];
    $orderId = $orderDetails['orderId'];
    $orderDate = $orderDetails['orderDate'];
    $paymentMethod = $orderDetails['paymentMethod'];
    $shippingAddress = $orderDetails['shippingAddress'];
    $shippingReceiver = $orderDetails['shippingReceiver'];
    $shippingPhone = $orderDetails['shippingPhone'];
    $billingAddress = $orderDetails['billingAddress'];
    $billingReceiver = $orderDetails['billingReceiver'];
    $billingPhone = $orderDetails['billingPhone'];
    $subtotal = $orderDetails['subtotal'];
    $shippingCharge = $orderDetails['shippingCharge'];
    $grandtotal = $orderDetails['grandtotal'];

    // Email content
    $subject = "Your Order Placed Successfully";

    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Confirmation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .container {
                max-width: 600px;
                background-color: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header img {
                max-width: 300px;
            }
            .content {
                padding: 20px;
            }
            .content h1 {
                color: #333333;
            }
            .order-details, .address-details, .financial-details {
                margin-top: 20px;
            }
            .order-details div, .address-details div, .financial-details div {
                padding: 10px;
                border-bottom: 1px solid #dddddd;
            }
            .order-details div:last-child, .address-details div:last-child, .financial-details div:last-child {
                border-bottom: none;
            }
            .footer {
                text-align: center;
                padding: 20px;
                background-color: #f57336;
                color: #ffffff;
                border-radius: 0 0 8px 8px;
            }
            .track-order {
                margin-top: 20px;
                text-align: center;
            }
            .track-order a {
                display: inline-block;
                padding: 10px 20px;
                background-color: #f57336;
                color: #ffffff;
                text-decoration: none;
                border-radius: 4px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid #dddddd;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            .address-columns {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }
            .address-column {
                width: 48%;
            }
            .address-column h2 {
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <a href="http://localhost/final-year-project/index.php">
                    <img src="https://i.ibb.co/2Ymx8Lc/Main-logo.png" alt="Main-logo" border="0">
                </a>
            </div>
            <div class="content">
                <h1>Dear ' . $userName . ',</h1>
                <p>Thank you for placing your order with us. Here are the details of your order:</p>
                <div class="order-details">
                    <div><strong>Order ID:</strong> #' . $orderId . '</div>
                    <div><strong>Order Date:</strong> ' . $orderDate . '</div>
                    <div><strong>Payment Method:</strong> ' . $paymentMethod . '</div>
                    <table>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                        </tr>';
    $orderquery = "SELECT orderdetails.orderId, orderdetails.productId, orderdetails.quantity, orders.id AS oid, products.id AS pid, products.productName, products.productImage1 FROM orderdetails INNER JOIN products ON orderdetails.productId = products.id INNER JOIN orders ON orderdetails.orderId = orders.id WHERE orders.userId = '$userId' AND orders.id = '$order_id';";
    $orderresult = mysqli_query($con, $orderquery);
    while ($orderrow = mysqli_fetch_assoc($orderresult)) {
        $productName = $orderrow['productName'];
        $quantity = $orderrow['quantity'];
        $message .= '
                        <tr>
                            <td>' . $productName . '</td>
                            <td>' . $quantity . '</td>
                        </tr>';
    }

    $message .= '
                    </table>
                </div>
                <div class="financial-details">
                    <div><strong>Subtotal:</strong> RM ' . number_format($subtotal, 2) . '</div>
                    <div><strong>Shipping Charge:</strong> RM ' . number_format($shippingCharge, 2) . '</div>
                    <div><strong>Grand Total:</strong> RM ' . number_format($grandtotal, 2) . '</div>
                </div>
                <div class="address-columns">
                    <div class="address-column">
                        <h2>Shipping Address</h2>
                        <div>' . $shippingReceiver . '</div>
                        <div>' . $shippingAddress . '</div>
                        <div>' . $shippingPhone . '</div>
                    </div>
                    <div class="address-column">
                        <h2>Billing Address</h2>
                        <div>' . $billingReceiver . '</div>
                        <div>' . $billingAddress . '</div>
                        <div>' . $billingPhone . '</div>
                    </div>
                </div>
                <div class="track-order">
                    <a href="http://localhost/final-year-project/track-order.php?oid=' . $orderId . '">Track Your Order</a>
                </div>
                <p>If you have any questions or concerns about your order, feel free to contact us.</p>
                <p>Best regards,<br>Online Furniture Store</p>
            </div>
            <div class="footer">
                <p>&copy; 2024 Online Furniture Store. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    // Include the mailer library and set up the email
    $mail = require __DIR__ . "/mailer.php"; // Adjust the path to your mailer.php file
    $mail->setFrom("ofurstore@gmail.com", "Online Furniture Store");
    $mail->addAddress($userEmail); // Send email to the user's email address
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->isHTML(true);

    try {
        $mail->send();
        echo "Order confirmation email sent.";
    } catch (Exception $e) {
        echo "Error sending email: " . $mail->ErrorInfo;
    }
}
?>
