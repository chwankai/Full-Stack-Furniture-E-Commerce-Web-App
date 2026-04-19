<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php'); //checking database connection
$selectedOrderId = isset($_GET['oid']) ? $_GET['oid'] : '';
if (strlen($_SESSION['login']) == 0) {
    header('location:login.php');
} else {
    if (isset($_POST['submit'])) {

        $cust_query = mysqli_query($con, "SELECT name FROM users WHERE id='" . $_SESSION['id'] . "'");
        $cust_info = mysqli_fetch_assoc($cust_query);

        $name = $cust_info['name'];
        $pid = mysqli_real_escape_string($con, $_POST['productid']);
        $qty = mysqli_real_escape_string($con, $_POST['quality']);
        $price = mysqli_real_escape_string($con, $_POST['price']);
        $value = mysqli_real_escape_string($con, $_POST['value']);
        $summary = mysqli_real_escape_string($con, $_POST['summary']);
        $review = mysqli_real_escape_string($con, $_POST['review']);
        $orderId = mysqli_real_escape_string($con, $_POST['orderId']);

        $query = mysqli_query($con, "INSERT INTO productreviews(productId, quality, price, value, name, summary, review) VALUES ('$pid', '$qty', '$price', '$value', '$name', '$summary', '$review')");
        $update_query = mysqli_query($con, "UPDATE orderdetails SET rate = 1 WHERE orderId = '$orderId' AND productId = '$pid'");
        if ($query && $update_query) {
            header("Location: product-rating.php?message=Rating%20added%20successfully.");
            exit;
        }
    }

    date_default_timezone_set('Asia/Kuala_Lumpur'); // change according timezone
    $currentTime = date('d-m-Y h:i:s A', time());

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Meta -->
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keywords" content="MediaCenter, Template, eCommerce">
        <meta name="robots" content="all">

        <title>Product Rating</title>

        <!-- Bootstrap Core CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

        <!-- Customizable CSS -->
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/orange.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css">
        <link href="assets/css/lightbox.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/rateit.css">
        <link rel="stylesheet" href="assets/css/config.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>

        <script type="text/javascript">
            // Retrieve the message from the URL parameter
            var message = '<?php echo isset($_GET["message"]) ? $_GET["message"] : ""; ?>';

            // Display the message in an alert
            if (message !== "") {
                alert(message);
            }
        </script>

    </head>

    <body class="cnt-home">
        <header class="header-style-1">

            <!-- ============================================== TOP MENU ============================================== -->
            <?php include ('includes/top-header.php'); ?>
            <!-- ============================================== TOP MENU : END ============================================== -->
            <?php include ('includes/main-header.php'); ?>
            <!-- ============================================== NAVBAR ============================================== -->
            <?php include ('includes/menu-bar.php'); ?>
            <!-- ============================================== NAVBAR : END ============================================== -->

        </header>
        <!-- ============================================== HEADER : END ============================================== -->
        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>Product Rating</li>
                    </ul>
                </div><!-- /.breadcrumb-inner -->
            </div><!-- /.container -->
        </div><!-- /.breadcrumb -->

        <div class="body-content outer-top-bd">
            <div class="container">
                <div class="checkout-box inner-bottom-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel-group checkout-steps" id="accordion">
                                <!-- checkout-step-01  -->
                                <div class="panel panel-default checkout-step-01">

                                    <!-- panel-heading -->
                                    <div class="panel-heading">
                                        <h4 class="unicase-checkout-title">
                                            <a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
                                                <span>1</span>Rate our product
                                            </a>
                                        </h4>
                                    </div>
                                    <!-- panel-heading -->

                                    <div id="collapseOne" class="panel-collapse collapse in">

                                        <!-- panel-body  -->
                                        <div class="panel-body">
                                            <div class="row">
                                                <form id="review-form" name="review-card" method="post">
                                                    <p>Select an item from delivered order</p>
                                                    <div class="review-card form-group" id="review-card">
                                                        <?php
                                                        // Retrieve saved cards for the logged-in user
                                                        $result = mysqli_query($con, "SELECT 
                                                        orders.id AS orderid, 
                                                        orders.userId, 
                                                        orders.orderDate, 
                                                        orders.orderStatus, 
                                                        products.id as productID,
                                                        products.productName AS productName, 
                                                        products.productImage1, 
                                                        orderdetails.rate 
                                                        FROM 
                                                        orders 
                                                        INNER JOIN 
                                                        orderdetails ON orders.id = orderdetails.orderId 
                                                        INNER JOIN 
                                                        products ON orderdetails.productId = products.id 
                                                        WHERE 
                                                        orders.userId = '" . $_SESSION['id'] . "' 
                                                        AND orders.orderStatus = 'Delivered' 
                                                        AND orderdetails.rate = 0");

                                                        if (mysqli_num_rows($result) > 0) {
                                                            ?>
                                                            <select name="review_card" id="review_info"
                                                                class="form-control unicase-form-control text-input"
                                                                onchange="assignFunction(event)">
                                                                <option value="" disabled selected>Select an item to leave a
                                                                    review</option>
                                                                <?php
                                                                while ($row = mysqli_fetch_assoc($result)) {
                                                                    $selected = ($row['orderid'] == $selectedOrderId) ? 'selected' : ''; // Check if this order ID should be selected
                                                                    echo "<option value='" . $row['orderid'] . "' data-product-id='" . $row['productID'] . "' data-product-name='" . $row['productName'] . "' data-order-status='" . $row['orderStatus'] . "' data-product-image='" . $row['productImage1'] . "' $selected>Order #" . $row['orderid'] . " - " . $row['productName'] . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <select name="review_card" id="review_info"
                                                                class="form-control unicase-form-control text-input" disabled>
                                                                <option value="" selected>No delivered orders at the moment.
                                                                </option>
                                                            </select>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div id="order-details"></div>
                                                </form><!-- /.cnt-form -->
                                            </div><!-- /.row -->
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel-collapse -->
                                </div><!-- /.panel -->
                            </div><!-- /.checkout-steps -->
                        </div><!-- /.col -->
                        <?php include ('includes/myaccount-sidebar.php'); ?>
                    </div><!-- /.row -->
                </div><!-- /.checkout-box -->
                <?php include ('includes/brands-slider.php'); ?>
            </div><!-- /.container -->
        </div><!-- /.body-content -->
        <?php include ('includes/footer.php'); ?>
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/echo.min.js"></script>
        <script src="assets/js/jquery.easing-1.3.min.js"></script>
        <script src="assets/js/jquery.rateit.min.js"></script>
        <script src="assets/js/lightbox.min.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        <script type="text/javascript">
            function assignFunction(event) {
                var selectedOption = event.target.options[event.target.selectedIndex];
                var productId = selectedOption.getAttribute('data-product-id');
                var productName = selectedOption.getAttribute('data-product-name');
                var orderStatus = selectedOption.getAttribute('data-order-status');
                var productImage = selectedOption.getAttribute('data-product-image');
                var orderId = selectedOption.value;

                var orderDetailsDiv = document.getElementById('order-details');
                orderDetailsDiv.innerHTML = `
                    <div class="order-details">
                        <form role="form" class="cnt-form" name="review" method="post">
                            <h3>Order Details</h3>
                            <input type="hidden" name="productid" value="${productId}">
                            <input type="hidden" name="orderId" value="${orderId}">
                            <p><strong>Order ID:</strong> #${orderId}</p>
                            <p><strong>Product Name:</strong> ${productName}</p>
                            <p><strong>Order Status:</strong> ${orderStatus}</p>
                            <img src="admin/productimages/${productId}/${productImage}" alt="${productName}" style="max-width: 200px;"/>
                
                            <div class="product-add-review">
                                <h4 class="title">Write your own review</h4>
                                <div class="review-table">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">    
                                            <thead>
                                                <tr>
                                                    <th class="cell-label">&nbsp;</th>
                                                    <th>1 star</th>
                                                    <th>2 stars</th>
                                                    <th>3 stars</th>
                                                    <th>4 stars</th>
                                                    <th>5 stars</th>
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <tr>
                                                    <td class="cell-label">Quality</td>
                                                    ${[1, 2, 3, 4, 5].map(num => `
                                                        <td><input type="radio" name="quality" class="radio" value="${num}" ${num === 5 ? 'checked' : ''} required></td>
                                                    `).join('')}
                                                </tr>
                                                <tr>
                                                    <td class="cell-label">Price</td>
                                                    ${[1, 2, 3, 4, 5].map(num => `
                                                        <td><input type="radio" name="price" class="radio" value="${num}" ${num === 5 ? 'checked' : ''} required></td>
                                                    `).join('')}
                                                </tr>
                                                <tr>
                                                    <td class="cell-label">Value</td>
                                                    ${[1, 2, 3, 4, 5].map(num => `
                                                        <td><input type="radio" name="value" class="radio" value="${num}" ${num === 5 ? 'checked' : ''} required></td>
                                                    `).join('')}
                                                </tr>
                                            </tbody>
                                        </table><!-- /.table .table-bordered -->
                                    </div><!-- /.table-responsive -->
                                </div><!-- /.review-table -->
                                <div class="review-form">
                                    <div class="form-container">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputSummary">Summary <span class="astk">*</span></label>
                                                    <textarea class="form-control txt" id="exampleInputSummary" rows="4" placeholder="" name="summary" required="required"></textarea>
                                                </div><!-- /.form-group -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputReview">Review <span class="astk">*</span></label>
                                                    <textarea class="form-control txt txt-review" id="exampleInputReview" rows="4" placeholder="" name="review" required="required"></textarea>
                                                </div><!-- /.form-group -->
                                            </div>
                                        </div><!-- /.row -->
                                        <div class="action text-right">
                                            <button name="submit" class="btn btn-primary btn-upper pull-right">SUBMIT REVIEW</button>
                                            <input type="reset" value="CLEAR" name="clear" style="margin:0px 10px;" class="btn btn-primary btn-upper outer-right-xs">
                                        </div><!-- /.action -->
                                    </div><!-- /.form-container -->
                                </div><!-- /.review-form -->
                            </div><!-- /.product-add-review -->    
                        </form><!-- /.cnt-form -->
                    </div><!-- /.order-details -->
                `;
            }
        </script>

    </body>

    </html>
<?php } ?>
