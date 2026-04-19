<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
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

	<title>Order History</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
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
	<script language="javascript" type="text/javascript">
		var popUpWin = 0;
		function popUpWindow(URLStr, left, top, width, height) {
			if (popUpWin) {
				if (!popUpWin.closed) popUpWin.close();
			}
			popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=' + 600 + ',height=' + 600 + ',left=' + left + ', top=' + top + ',screenX=' + left + ',screenY=' + top + '');
		}

	</script>

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
					<li><a href="index.php">Home</a></li>
					<li class='active'>Order Details</li>
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
											<th colspan="3" class="cart-description item">Image</th>
											<th class="cart-sub-total item">Subtotal</th>
											<th class="cart-sub-total item">Shipping Fee</th>
											<th class="cart-total item">Grandtotal</th>
											<th class="cart-total item">Payment Method</th>
											<th class="cart-description item">Order Date</th>
											<th class="cart-description item">Status</th>
											<th class="cart-total last-item">Action</th>
										</tr>
									</thead><!-- /thead -->

									<tbody>

										<?php
										$orderid = $_POST['orderid'];
										$email = $_POST['email'];
										$query = mysqli_query($con, "select users.email, orders.subtotal, orders.shippingCharge as shippingcharge, orders.grandtotal, orders.paymentMethod as paym,orders.orderDate as odate,orders.id as orderid ,orders.orderStatus as status from orders join users on orders.userId=users.id where orders.userId='" . $_SESSION['id'] . "' and email='$email' and orders.id='$orderid' and orders.paymentMethod is not null;");

										$cnt = 1;
										$num = mysqli_num_rows($query);
										if ($num > 0) {
											while ($row = mysqli_fetch_array($query)) {
												?>
												<tr>
													<td class="cart-product-id-info"><?php echo $orderid = $row['orderid']; ?>
													</td>
													<td colspan='3' class="cart-product-name-info">
														<?php
														$productquery = mysqli_query($con, "SELECT orderdetails.productID, products.productName AS pname, orderdetails.quantity, orderdetails.rate
														FROM orderdetails
														JOIN products ON orderdetails.productId = products.id
														WHERE orderdetails.orderId = '" . $row['orderid'] . "'
														ORDER BY orderID DESC");
														$allRated = true; // Flag to check if all products are rated
												
														while ($product_row = mysqli_fetch_array($productquery)) {
															echo "<h4 class='cart-product-description'>" . $cnt . ".<b> </b><a href='product-details.php?pid=" . $product_row['productID'] . "'>" . $product_row['pname'] . "</a> x" . $product_row['quantity'] . "</h4>";
															$cnt++;
															if ($product_row['rate'] == 0) {
																$allRated = false;
															}
														}
														?>
													<td class="cart-product-sub-total">RM <?php echo $price = $row['subtotal']; ?>
													</td>
													<td class="cart-product-sub-total">RM
														<?php echo $shippcharge = $row['shippingcharge']; ?> </td>
													<td class="cart-product-grand-total">RM <?php echo $row['grandtotal']; ?>
													</td>
													<td class="cart-product-sub-total"><?php echo $row['paym']; ?> </td>
													<td class="cart-product-sub-total"><?php echo $row['odate']; ?> </td>
													<td class="cart-product-sub-total"><?php echo $row['status']; ?> </td>

													<td class="cart-product-sub-total">
														<?php
														if ($row['status'] == 'Delivered') {
															if (!$allRated) { ?>
																<a href="javascript:void(0);"
																	onClick="popUpWindow('track-order.php?oid=<?php echo htmlentities($row['orderid']); ?>');"
																	title="Track order">
																	Check
																</a>
																|
																<a href="product-rating.php" title="Rate">
																	Rate
																</a>
															<?php }
															// If rate is 1, display nothing (blank)
															else { ?>
																<a href="javascript:void(0);"
																	onClick="popUpWindow('track-order.php?oid=<?php echo htmlentities($row['orderid']); ?>');"
																	title="Track order">
																	Check
																</a>
															<?php }
														} else { ?>
															<a href="javascript:void(0);"
																onClick="popUpWindow('track-order.php?oid=<?php echo htmlentities($row['orderid']); ?>');"
																title="Track order">
																Check
															</a>
														<?php } ?>
													</td>
												</tr>
												<?php $cnt = 1;
											} ?>
										<?php } else { ?>
											<tr>
												<td colspan="8">Either order id or Registered email id is invalid</td>
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
