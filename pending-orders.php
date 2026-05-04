<?php
require_once __DIR__ . '/includes/customer_session.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('includes/config.php');
	if (strlen($_SESSION['login']) == 0) {
		echo '<script>alert("Please login first to see your pending orders.");window.location.href = "login.php";</script>';
		header('location:login.php');
	} else {
		if (isset($_GET['id'])) {
			$order_id = (int) $_GET['id'];

			$transaction_started = false;

			try {
				if (!mysqli_begin_transaction($con)) {
					throw new Exception('begin_failed');
				}
				$transaction_started = true;

			// Get the order details before deleting
			$order_query = mysqli_query($con, "SELECT * FROM orders WHERE userId='" . $_SESSION['id'] . "' AND paymentMethod IS NULL AND id='$order_id' FOR UPDATE");
			if (!$order_query) {
				throw new Exception('query_failed');
			}
			$order_details = mysqli_fetch_assoc($order_query);

			// Check if the order exists
			if ($order_details) {
				// Get the order items
				$order_items_query = mysqli_query($con, "SELECT * FROM orderdetails WHERE orderId='$order_id'");
				if (!$order_items_query) {
					throw new Exception('query_failed');
				}

				// Iterate through each item and add back the quantity to the product availability
				while ($order_item = mysqli_fetch_assoc($order_items_query)) {
					$product_id = $order_item['productId'];
					$quantity = $order_item['quantity'];

					// Update product availability
					$restore_stock_query = mysqli_query($con, "UPDATE products SET Quantity = Quantity + $quantity WHERE id='$product_id'");
					if (!$restore_stock_query) {
						throw new Exception('restore_failed');
					}
				}

				// Delete order details
				$delete_details_query = mysqli_query($con, "DELETE FROM orderdetails WHERE orderId='$order_id'");
				if ($delete_details_query === false) {
					throw new Exception('delete_failed');
				}
				// Delete order
				$delete_order_query = mysqli_query($con, "DELETE FROM orders WHERE id='$order_id'");
				if (!$delete_order_query || mysqli_affected_rows($con) <= 0) {
					throw new Exception('delete_failed');
				}

				if (!mysqli_commit($con)) {
					throw new Exception('commit_failed');
				}
				$transaction_started = false;

				// Redirect back to pending orders page
				header('location:pending-orders.php');
				exit;
			} else {
				if ($transaction_started) {
					mysqli_rollback($con);
					$transaction_started = false;
				}
				// If the order does not exist, redirect back to pending orders page
				header('location:pending-orders.php');
				exit;
			}
			} catch (Exception $e) {
				if ($transaction_started) {
					mysqli_rollback($con);
				}
				header('location:pending-orders.php');
				exit;
			}
		}

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

		<title>Pending Order History</title>
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/css/main.css">
		<link rel="stylesheet" href="assets/css/orange.css">
		<link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/config.css">

		<!-- Icons/Glyphs -->
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">

		<!-- HTML5 elements and media queries Support for IE8 : HTML5 shim and Respond.js -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

	</head>

	<body class="cnt-home">

		<!-- ============================================== HEADER ============================================== -->
		<header class="header-style-1">
			<?php include ('includes/top-header.php'); ?>
			<?php include ('includes/main-header.php'); ?>
			<?php include ('includes/menu-bar.php'); ?>
		</header>
		<!-- ============================================== HEADER : END ============================================== -->
		<div class="breadcrumb">
			<div class="container">
				<div class="breadcrumb-inner">
					<ul class="list-inline list-unstyled">
						<li><a href="#">Home</a></li>
						<li class='active'>Pending Orders</li>
					</ul>
				</div><!-- /.breadcrumb-inner -->
			</div><!-- /.container -->
		</div><!-- /.breadcrumb -->

		<div class="body-content outer-top-xs">
			<div class="container">
				<div class="row inner-bottom-sm">
					<div class="shopping-cart">
						<div class="col-md-12 col-sm-12 shopping-cart-table ">
							<div class="table-responsive">
								<form name="cart" method="post">

									<table class="table table-bordered">
										<thead>
											<tr>
												<th class="cart-romove item">Order ID#</th>
												<th colspan='3' class="cart-description item">Products</th>
												<th class="cart-sub-total item">Subtotal</th>
												<th class="cart-sub-total item">Shipping Fee</th>
												<th class="cart-total item">Grandtotal</th>
												<th class="cart-description item">Order Date</th>
												<th class="cart-status item">Status</th>
												<th class="cart-total last-item">Action</th>
											</tr>
										</thead><!-- /thead -->

										<tbody>

											<?php
											$query = mysqli_query($con, "SELECT orders.subtotal, orders.shippingCharge AS shippingcharge, orders.grandtotal,orders.orderDate AS odate,orders.id AS orderid,orders.orderStatus AS status FROM orders WHERE orders.userId='" . $_SESSION['id'] . "' AND orders.paymentMethod IS NULL ORDER BY orderid DESC;");
											$cnt = 1;
											$num = mysqli_num_rows($query);
											if ($num > 0) {
												while ($row = mysqli_fetch_array($query)) {
													$order_error = false; // Initialize order error flag
													$order_error_text = ''; // Initialize order error message
													$productquery = mysqli_query($con, "SELECT orderdetails.productID, products.productName AS pname, orderdetails.quantity, products.productAvailability FROM orderdetails JOIN products ON orderdetails.productId = products.id WHERE orderdetails.orderId = '" . $row['orderid'] . "'");
													while ($product_row = mysqli_fetch_array($productquery)) {
														// Check if product availability is not sufficient or quantity is less than 1
														if ($product_row['productAvailability'] != 'In Stock' || $product_row['quantity'] < 1) {
															$order_error = true; // Set order error flag to true
															$order_error_text = 'Order error: Please delete'; // Set order error message
															break; // Exit the loop if any product has an availability issue
														}
													}
													?>
													<tr>
														<td class="cart-product-id-info"><?php echo $orderid = $row['orderid']; ?>
														</td>
														<td colspan='3' class="cart-product-name-info">
															<?php
															if ($order_error) {
																echo "<p>$order_error_text</p>"; // Display order error message
															} else {
																// Display product details
																$productquery = mysqli_query($con, "SELECT orderdetails.productID, products.productName AS pname, orderdetails.quantity, orderdetails.rate FROM orderdetails JOIN products ON orderdetails.productId = products.id WHERE orderdetails.orderId = '" . $row['orderid'] . "'");
																while ($product_row = mysqli_fetch_array($productquery)) {
																	echo "<h4 class='cart-product-description'>" . $cnt . ".<b> </b><a href='product-details.php?pid=" . $product_row['productID'] . "'>" . $product_row['pname'] . "</a> x" . $product_row['quantity'] . "</h4>";
																	$cnt++;
																}
															}
															?>
														</td>
														<td class="cart-product-sub-total">RM
															<?php echo $price = $row['subtotal']; ?></td>
														<td class="cart-product-sub-total">RM
															<?php echo $shippcharge = $row['shippingcharge']; ?></td>
														<td class="cart-product-grand-total">RM <?php echo $row['grandtotal']; ?>
														</td>
														<td class="cart-product-sub-total"><?php echo $row['odate']; ?></td>
														<td class="cart-product-sub-total">To Pay</td>
														<td class="cart-product-sub-total" width="150px" style="text-align:center;">
															<?php
															if ($order_error) {
																echo "Order error: Please "; // Display order error message
															} else {
																echo "<a href='payment-method.php?order_id=" . $row['orderid'] . "'>Pay Now</a> | ";
															}
															?>
															<a
																href="pending-orders.php?id=<?php echo $row['orderid']; ?>">Delete</a>
														</td>
													</tr>
													<?php
													$cnt = 1;
												}
											} else {
												?>
												<tr>
													<td colspan="10" align="center">
														<h4>No Result Found</h4>
													</td>
												</tr>
											<?php } ?>
										</tbody><!-- /tbody -->
									</table><!-- /table -->
							</div>
						</div>
					</div><!-- /.shopping-cart -->
				</div> <!-- /.row -->
				</form>
				<!-- ============================================== BRANDS CAROUSEL ============================================== -->
				<?php include ('includes/brands-slider.php'); ?>
				<!-- ============================================== BRANDS CAROUSEL : END ============================================== -->
			</div><!-- /.container -->
		</div><!-- /.body-content -->
		<?php include ('includes/footer.php'); ?>

		<script src="assets/js/jquery-1.11.1.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/owl.carousel.min.js"></script>
		<script src="assets/js/echo.min.js"></script>
		<script src="assets/js/jquery.easing-1.3.min.js"></script>
		<script src="assets/js/jquery.rateit.min.js"></script>
		<script type="text/javascript" src="assets/js/lightbox.min.js"></script>
		<script src="assets/js/wow.min.js"></script>
		<script src="assets/js/scripts.js"></script>
		<script src="switchstylesheet/switchstylesheet.js"></script>

		<script>
			$(document).ready(function () {
				$(".changecolor").switchstylesheet({ seperator: "color" });
				$('.show-theme-options').click(function () {
					$(this).parent().toggleClass('open');
					return false;
				});
			});

			$(window).bind("load", function () {
				$('.show-theme-options').delay(2000).trigger('click');
			});
		</script>
	</body>

	</html>
<?php } ?>
