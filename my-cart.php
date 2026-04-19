<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
	// User is required to login first to see their cart
	echo '<script>alert("Please login first to see your cart.");window.location.href = "login.php";</script>';
} else {
	// Initialize variables
	$shipping_state = '';
	$subtotal = 0;

	// Fetch user's shipping address and subtotal
	$query = mysqli_query($con, "SELECT shippingState FROM users WHERE id='" . $_SESSION['id'] . "'");
	if ($row = mysqli_fetch_array($query)) {
		$shipping_state = $row['shippingState'];
	}

	// Calculate subtotal
	$subtotal = calculateSubtotal($con);

	// Calculate shipping fee based on shipping state and subtotal
	$shipping_fee = calculateShippingFee($con, $shipping_state, $subtotal);
	$grandtotal = $subtotal + $shipping_fee;

	if (isset($_POST['submit'])) {
		if (!empty($_SESSION['cart'])) {
			foreach ($_POST['quantity'] as $key => $val) {
				if ($val == 0) {
					unset($_SESSION['cart'][$key]);
				} else {
					$_SESSION['cart'][$key]['quantity'] = $val;
				}
			}
		}

		$shipping_state = ''; // Initialize variable to store shipping state
		$subtotal = 0; // Initialize subtotal
		// Fetch user's shipping address and subtotal
		$query = mysqli_query($con, "select shippingState from users where id='" . $_SESSION['id'] . "'");
		if ($row = mysqli_fetch_array($query)) {
			$shipping_state = $row['shippingState'];
		}
		// Calculate total price (subtotal) from the cart
		if (!empty($_SESSION['cart'])) {
			foreach ($_SESSION['cart'] as $id => $value) {
				$product_query = mysqli_query($con, "SELECT productPrice FROM products WHERE id='$id'");
				if ($product_row = mysqli_fetch_assoc($product_query)) {
					$subtotal += ($value['quantity'] * $product_row['productPrice']);
				}
			}
		}
		// Calculate shipping fee based on shipping state and subtotal
		$shipping_fee = calculateShippingFee($con, $shipping_state, $subtotal);
		$grandtotal = $subtotal + $shipping_fee;
	}

	// Process ordersubmit action
	if (isset($_POST['ordersubmit'])) {
		if (strlen($_SESSION['login']) == 0) {
			header('location:login.php');
		} else {
			$quantity = $_POST['quantity'];
			$pdd = $_SESSION['pid'];
			$value = array_combine($pdd, $quantity);
			$shipping_fee = 0;
			$grandtotal = 0;
			$subtotal = 0;

			$user_id = $_SESSION['id'];

			// Get shipping and billing information from POST data
			$shippingReceiver = $_POST['shippingreceiver'];
			$shippingPhone = $_POST['shippingphone'];
			$shippingAddress = $_POST['shippingaddress'];
			$shippingCity = $_POST['shippingcity'];
			$shippingState = $_POST['shippingstate'];
			$shippingPincode = $_POST['shippingpincode'];

			$billingReceiver = $_POST['billingreceiver'];
			$billingPhone = $_POST['billingphone'];
			$billingAddress = $_POST['billingaddress'];
			$billingCity = $_POST['billingcity'];
			$billingState = $_POST['billingstate'];
			$billingPincode = $_POST['billingpincode'];

			$fullShippingAddress = $shippingAddress . ', ' . $shippingCity . ', ' . $shippingState . ', ' . $shippingPincode;
			$fullBillingAddress = $billingAddress . ', ' . $billingCity . ', ' . $billingState . ', ' . $billingPincode;

			// Initialize an array to keep track of products to remove from the cart
			$productsToRemoveFromCart = [];

			// Calculate total and shipping fee
			foreach ($value as $pid => $qty) {
				$stock_query = mysqli_query($con, "SELECT Quantity, productAvailability, productPrice FROM products WHERE id='$pid'");
				$stock_info = mysqli_fetch_assoc($stock_query);
				$stock = $stock_info['productAvailability'];
				$quantity = $stock_info['Quantity'];
				$productPrice = $stock_info['productPrice'];
				$subtotal = $subtotal + ($productPrice * $qty);

				if ($qty == 0) {
					// If quantity is 0, mark this product for removal from the cart
					$productsToRemoveFromCart[] = $pid;
					continue; // Skip further processing for this product
				}

				if ($stock == "In Stock" && $quantity >= $qty) {
					$totalprice = $subtotal;
					if ($totalprice > 1000) {
						$shipping_fee = 0;
					} else {
						if ($shippingState == 'Melaka')
							$shipping_fee = 100.00;
						else if ($shippingState == 'Sabah' || $shippingState == 'Sarawak')
							$shipping_fee = 200.00;
						else
							$shipping_fee = 150.00;
					}
					$grandtotal = $totalprice + $shipping_fee;
					mysqli_query($con, "UPDATE products SET Quantity = Quantity - $qty WHERE id='$pid'");
				} else {
					header("Location: my-cart.php?message=Item%20out%20of%20stock.%20Please%20remove%20the%20item.");
					exit;
				}
			}

			// Remove products with quantity 0 from the session cart
			foreach ($productsToRemoveFromCart as $pidToRemove) {
				unset($_SESSION['cart'][$pidToRemove]);
			}

			// Check if there are no items left in the cart
			if (empty($_SESSION['cart'])) {
				header("Location: my-cart.php?message=No%20items%20left%20in%20cart.");
				exit;
			}

			// Insert the order into the orders table
			mysqli_query($con, "INSERT INTO orders(userId, subtotal, shippingCharge, grandtotal, shippingReceiver, shippingPhone, shippingAddress, billingReceiver, billingPhone, billingAddress) VALUES('" . $_SESSION['id'] . "', '$subtotal','$shipping_fee','$grandtotal','$shippingReceiver','$shippingPhone','$fullShippingAddress','$billingReceiver','$billingPhone','$fullBillingAddress')");

			// Get the last inserted order ID
			$order_id = mysqli_insert_id($con);

			// Loop through the items to insert into orderdetails table
			foreach ($value as $pid => $qty) {
				// Check if the quantity is greater than 0 before inserting into orderdetails
				if ($qty > 0) {
					// Insert order details for each product
					mysqli_query($con, "INSERT INTO orderdetails(orderId, productId, quantity) VALUES('$order_id', '$pid', '$qty')");
				}
			}

			unset($_SESSION['cart']);

			// Redirect to payment method page with order ID as parameter
			header('location:payment-method.php?order_id=' . $order_id);
			exit;
		}
	}

	// Process remove_code action
	if (isset($_POST['remove_code'])) {
		if (!empty($_SESSION['cart'])) {
			foreach ($_POST['remove_code'] as $key) {
				unset($_SESSION['cart'][$key]);
			}
			echo "<script>alert('Item removed from cart successfully.');</script>";
		}
		$shipping_state = ''; // Initialize variable to store shipping state
		$subtotal = 0; // Initialize subtotal
		// Fetch user's shipping address and subtotal
		$query = mysqli_query($con, "SELECT shippingState FROM users WHERE id='" . $_SESSION['id'] . "'");
		if ($row = mysqli_fetch_array($query)) {
			$shipping_state = $row['shippingState'];
		}
		// Calculate total price (subtotal) from the cart
		if (!empty($_SESSION['cart'])) {
			foreach ($_SESSION['cart'] as $id => $value) {
				$product_query = mysqli_query($con, "SELECT productPrice FROM products WHERE id='$id'");
				if ($product_row = mysqli_fetch_assoc($product_query)) {
					$subtotal += ($value['quantity'] * $product_row['productPrice']);
				}
			}
		}
		// Calculate shipping fee based on shipping state and subtotal
		$shipping_fee = calculateShippingFee($con, $shipping_state, $subtotal);
		$grandtotal = $subtotal + $shipping_fee;
	}

	// Process update action
	if (isset($_POST['update'])) {
		$breceiver = $_POST['billingreceiver'];
		$bphone = $_POST['billingphone'];
		$baddress = $_POST['billingaddress'];
		$bstate = $_POST['billingstate'];
		$bcity = $_POST['billingcity'];
		$bpincode = $_POST['billingpincode'];

		$sreceiver = $_POST['shippingreceiver'];
		$sphone = $_POST['shippingphone'];
		$saddress = $_POST['shippingaddress'];
		$sstate = $_POST['shippingstate'];
		$scity = $_POST['shippingcity'];
		$spincode = $_POST['shippingpincode'];
		$query = mysqli_query($con, "update users set billingReceiver='$breceiver', billingPhone='$bphone', shippingReceiver='$sreceiver', shippingPhone='$sphone', billingAddress='$baddress',billingState='$bstate',billingCity='$bcity',billingPincode='$bpincode',shippingAddress='$saddress',shippingState='$sstate',shippingCity='$scity',shippingPincode='$spincode' where id='" . $_SESSION['id'] . "'");
		if ($query) {
			echo "<script>alert('Billing and Shipping Address has been updated.');</script>";
		}
		if (!$query) {
			printf("Error: %s\n", mysqli_error($con));
			exit();
		}

		// Calculate shipping fee
		$shipping_state = $_POST['shippingstate'];

		$subtotal = calculateSubtotal($con);

		if ($subtotal > 1000) {
			$shipping_fee = 0; // Free shipping for subtotal over 1000
		} else {
			if ($shipping_state == 'Melaka') {
				$shipping_fee = 100.00;
			} elseif ($shipping_state == 'Sarawak' || $shipping_state == 'Sabah') {
				$shipping_fee = 200.00;
			} else {
				$shipping_fee = 150.00;
			}
		}

		// Update the total price including shipping fee
		$grandtotal = $subtotal + $shipping_fee;
	}
}

// Function to calculate subtotal
function calculateSubtotal($con)
{
	$subtotal = 0;
	if (!empty($_SESSION['cart'])) {
		foreach ($_SESSION['cart'] as $id => $value) {
			$product_query = mysqli_query($con, "SELECT productPrice FROM products WHERE id='$id'");
			if ($product_row = mysqli_fetch_assoc($product_query)) {
				$subtotal += ($value['quantity'] * $product_row['productPrice']);
			}
		}
	}
	return $subtotal;
}

// Function to calculate shipping fee
function calculateShippingFee($con, $state, $subtotal)
{
	$shipping_fee = 0;
	if ($subtotal > 1000) {
		// If subtotal exceeds 1000, set shipping fee to 0
		return $shipping_fee;
	}

	// Otherwise, calculate shipping fee based on the shipping state
	if ($state == 'Melaka') {
		$shipping_fee = 100;
	} elseif ($state == 'Sarawak' || $state == 'Sabah') {
		$shipping_fee = 200;
	} else {
		$shipping_fee = 150;
	}
	return $shipping_fee;
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

	<title>My Cart</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/orange.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<link rel="stylesheet" href="assets/css/owl.transitions.css">
	<link href="assets/css/lightbox.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/rateit.css">
	<link rel="stylesheet" href="assets/css/config.css">
	<script src="assets/js/statecitylist.js"></script>

	<!-- Icons/Glyphs -->
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">

	<script>
		// Retrieve the message from the URL parameter
		var message = '<?php echo isset($_GET["message"]) ? $_GET["message"] : ""; ?>';

		// Display the message in an alert
		if (message !== "") {
			alert(message);
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
					<li class='active'>Shopping Cart</li>
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
							<form name="cart" method="post" onsubmit="enableBillingFieldsBeforeSubmit()">
								<?php
								if (!empty($_SESSION['cart'])) {
									?>
									<div class="container">
										<div class="my-wishlist-page inner-bottom-sm">
											<div class="col-md-12 my-wishlist">
												<div class="table-responsive">
													<table class="table">
														<thead>
															<tr>
																<th colspan="4"
																	style="padding-top:0px; padding-bottom:0px;">my cart
																</th>
															</tr>
															<tr>
																<th colspan="4" style="padding:0px; padding-bottom:30px;">
																	<hr>
																</th>
															</tr>
														</thead>
													</table>
												</div>
											</div>
										</div>
									</div>
									<table class="table table-bordered">
										<thead>
											<tr>
												<th class="cart-romove item">Remove</th>
												<th class="cart-description item">Image</th>
												<th class="cart-product-name item">Product Name</th>
												<th class="cart-sub-total item">Price Per unit</th>
												<th class="cart-qty item">Quantity</th>
												<th class="cart-total last-item">Total</th>
											</tr>
										</thead><!-- /thead -->
										<tfoot>
											<tr>
												<td colspan="7">
													<div class="shopping-cart-btn">
														<span class="">
															<a href="index.php"
																class="btn btn-upper btn-primary outer-left-xs">Continue
																Shopping</a>
															<input type="submit" name="submit"
																value="Delete/Update Shopping Cart"
																class="btn btn-upper btn-primary pull-right outer-right-xs">
														</span>
													</div><!-- /.shopping-cart-btn -->
												</td>
											</tr>
										</tfoot>
										<tbody>
											<?php
											$pdtid = array();
											$sql = "SELECT * FROM products WHERE id IN(";
											foreach ($_SESSION['cart'] as $id => $value) {
												$sql .= $id . ",";
											}
											$sql = substr($sql, 0, -1) . ") ORDER BY id ASC";
											$query = mysqli_query($con, $sql);
											$totalprice = 0;
											$totalqunty = 0;
											if (!empty($query)) {
												while ($row = mysqli_fetch_array($query)) {
													$quantity = $_SESSION['cart'][$row['id']]['quantity'];
													$subtotal = $_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'];
													$totalprice += $subtotal;
													$_SESSION['qnty'] = $totalqunty += $quantity;

													array_push($pdtid, $row['id']);
													//print_r($_SESSION['pid'])=$pdtid;exit;
													?>

													<tr>
														<td class="romove-item"><input type="checkbox" name="remove_code[]"
																value="<?php echo htmlentities($row['id']); ?>" /></td>
														<td class="cart-image">
															<a class="entry-thumbnail" href="detail.html">
																<img src="admin/productimages/<?php echo $row['id']; ?>/<?php echo $row['productImage1']; ?>"
																	alt="" width="114" height="146">
															</a>
														</td>
														<td class="cart-product-name-info">
															<h4 class='cart-product-description'><a
																	href="product-details.php?pid=<?php echo htmlentities($pd = $row['id']); ?>"><?php echo $row['productName'];

																		 $_SESSION['sid'] = $pd;
																		 ?></a></h4>

														</td>
														<td class="cart-product-sub-total">
															<span class="cart-sub-total-price"
																id="product-price-<?php echo $row['id']; ?>">
																<?php echo "RM " . number_format($row['productPrice'], 2); ?>
															</span>
														</td>
														<td class="cart-product-quantity">
															<div class="quant-input">
																<div class="arrows">
																	<div class="arrow plus gradient"
																		data-id="<?php echo $row['id']; ?>"><span class="ir"><i
																				class="icon fa fa-sort-asc"></i></span></div>
																	<div class="arrow minus gradient"
																		data-id="<?php echo $row['id']; ?>"><span class="ir"><i
																				class="icon fa fa-sort-desc"></i></span></div>
																</div>
																<input type="text" class="quantity-input"
																	id="quantity-<?php echo $row['id']; ?>"
																	value="<?php echo $_SESSION['cart'][$row['id']]['quantity']; ?>"
																	name="quantity[<?php echo $row['id']; ?>]" min="1">
															</div>
														</td>
														<td class="cart-product-grand-total">
															<span class="cart-grand-total-price"
																id="grand-total-<?php echo $row['id']; ?>">
																<?php echo "RM " . number_format($_SESSION['cart'][$row['id']]['quantity'] * $row['productPrice'], 2); ?>
															</span>
														</td>
													</tr>

												<?php }
											}
											$_SESSION['pid'] = $pdtid;
											?>

										</tbody><!-- /tbody -->
									</table><!-- /table -->

							</div>
						</div><!-- /.shopping-cart-table -->
						<div class="col-md-8 col-sm-12 estimate-ship-tax">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>
											<span class="estimate-title">Shipping Address</span>
										</th>
										<th>
											<span class="estimate-title">Billing Address</span>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div class="form-group">

												<?php
												$query = mysqli_query($con, "select * from users where id='" . $_SESSION['id'] . "'");
												while ($row = mysqli_fetch_array($query)) {
													$selectedshippingState = $row['shippingState'];
													$selectedshippingCity = $row['shippingCity'];
													$selectedbillingState = $row['billingState'];
													$selectedbillingCity = $row['billingCity'];
													?>

													<div class="form-group">
														<label class="info-title" for="Receiver Name">Receiver Name
															<span>*</span></label>
														<textarea class="form-control unicase-form-control text-input"
															name="shippingreceiver"
															required="required"><?php echo $row['shippingReceiver']; ?></textarea>
													</div>

													<div class="form-group">
														<label class="info-title" for="Receiver Phone">Receiver Phone
															<span>*</span></label>
														<textarea class="form-control unicase-form-control text-input"
															name="shippingphone" id="shippingphone" minlength="11"
															maxlength="12"
															required="required"><?php echo $row['shippingPhone']; ?></textarea>
													</div>

													<div class="form-group">
														<label class="info-title" for="Shipping Address">Shipping Address
															<span>*</span></label>
														<textarea class="form-control unicase-form-control text-input"
															name="shippingaddress"
															required="required"><?php echo $row['shippingAddress']; ?></textarea>
													</div>
													<div class="form-group">
														<label class="info-title" for="Shipping State">Shipping State
															<span>*</span></label>
														<select type="text" class="form-control unicase-form-control text-input"
															id="shippingstate" name="shippingstate"
															onchange="print_city('shippingcity', this.value);" required>
															<option value="">Select State</option>
															<option value="Johor" <?php echo ($selectedshippingState == 'Johor') ? 'selected' : ''; ?>>Johor</option>
															<option value="Kedah" <?php echo ($selectedshippingState == 'Kedah') ? 'selected' : ''; ?>>Kedah</option>
															<option value="Kelantan" <?php echo ($selectedshippingState == 'Kelantan') ? 'selected' : ''; ?>>
																Kelantan</option>
															<option value="Kuala Lumpur" <?php echo ($selectedshippingState == 'Kuala Lumpur') ? 'selected' : ''; ?>>
																Kuala Lumpur</option>
															<option value="Melaka" <?php echo ($selectedshippingState == 'Melaka') ? 'selected' : ''; ?>>Melaka</option>
															<option value="Negeri Sembilan" <?php echo ($selectedshippingState == 'Negeri Sembilan') ? 'selected' : ''; ?>>Negeri Sembilan</option>
															<option value="Pahang" <?php echo ($selectedshippingState == 'Pahang') ? 'selected' : ''; ?>>Pahang</option>
															<option value="Perak" <?php echo ($selectedshippingState == 'Perak') ? 'selected' : ''; ?>>Perak</option>
															<option value="Perlis" <?php echo ($selectedshippingState == 'Perlis') ? 'selected' : ''; ?>>Perlis</option>
															<option value="Pulau Pinang" <?php echo ($selectedshippingState == 'Pulau Pinang') ? 'selected' : ''; ?>>
																Pulau Pinang</option>
															<option value="Sabah" <?php echo ($selectedshippingState == 'Sabah') ? 'selected' : ''; ?>>Sabah</option>
															<option value="Sarawak" <?php echo ($selectedshippingState == 'Sarawak') ? 'selected' : ''; ?>>
																Sarawak</option>
															<option value="Selangor" <?php echo ($selectedshippingState == 'Selangor') ? 'selected' : ''; ?>>
																Selangor</option>
															<option value="Terengganu" <?php echo ($selectedshippingState == 'Terengganu') ? 'selected' : ''; ?>>
																Terengganu</option>
														</select>
													</div>
													<div class="form-group">
														<label class="info-title" for="Shipping City">Shipping City
															<span>*</span></label>
														<select type="text" class="form-control unicase-form-control text-input"
															id="shippingcity" name="shippingcity" required>
															<option value=""></option>
														</select>
													</div>

												</div>
												<div class="form-group">
													<label class="info-title" for="Shipping Pincode">Shipping Postcode
														<span>*</span></label>
													<input type="text" class="form-control unicase-form-control text-input"
														style="color:black;" id="shippingpincode" name="shippingpincode"
														required="required" value="<?php echo $row['shippingPincode']; ?>"
														maxlength="5" minlength="5" pattern="\d{5}"
														title="Postcode only can be numbers">
												</div>

							</div>
							</td>
							<td>
								<div class="form-group">

									<div class="form-group">
										<label class="info-title" for="Receiver Name">Receiver Name <span>*</span></label>
										<textarea class="form-control unicase-form-control text-input" name="billingreceiver"
											required="required"><?php echo $row['billingReceiver']; ?></textarea>
									</div>

									<div class="form-group">
										<label class="info-title" for="Receiver Phone">Receiver Phone <span>*</span></label>
										<textarea class="form-control unicase-form-control text-input" name="billingphone"
											id="billingphone" minlength="11" maxlength="12"
											required="required"><?php echo $row['billingPhone']; ?></textarea>
									</div>

									<div class="form-group">
										<label class="info-title" for="Billing Address">Billing Address <span>*</span></label>
										<textarea class="form-control unicase-form-control text-input" name="billingaddress"
											required="required"><?php echo $row['billingAddress']; ?></textarea>
									</div>
									<div class="form-group">
										<label class="info-title" for="Billing State">Billing State <span>*</span></label>
										<select type="text" class="form-control unicase-form-control text-input"
											id="billingstate" name="billingstate"
											onchange="print_city('billingcity', this.value);" required>
											<option value="">Select State</option>
											<option value="Johor" <?php echo ($selectedbillingState == 'Johor') ? 'selected' : ''; ?>>Johor</option>
											<option value="Kedah" <?php echo ($selectedbillingState == 'Kedah') ? 'selected' : ''; ?>>Kedah</option>
											<option value="Kelantan" <?php echo ($selectedbillingState == 'Kelantan') ? 'selected' : ''; ?>>Kelantan</option>
											<option value="Kuala Lumpur" <?php echo ($selectedbillingState == 'Kuala Lumpur') ? 'selected' : ''; ?>>Kuala Lumpur</option>
											<option value="Melaka" <?php echo ($selectedbillingState == 'Melaka') ? 'selected' : ''; ?>>Melaka</option>
											<option value="Negeri Sembilan" <?php echo ($selectedbillingState == 'Negeri Sembilan') ? 'selected' : ''; ?>>Negeri Sembilan</option>
											<option value="Pahang" <?php echo ($selectedbillingState == 'Pahang') ? 'selected' : ''; ?>>Pahang</option>
											<option value="Perak" <?php echo ($selectedbillingState == 'Perak') ? 'selected' : ''; ?>>Perak</option>
											<option value="Perlis" <?php echo ($selectedbillingState == 'Perlis') ? 'selected' : ''; ?>>Perlis</option>
											<option value="Pulau Pinang" <?php echo ($selectedbillingState == 'Pulau Pinang') ? 'selected' : ''; ?>>Pulau Pinang</option>
											<option value="Sabah" <?php echo ($selectedbillingState == 'Sabah') ? 'selected' : ''; ?>>Sabah</option>
											<option value="Sarawak" <?php echo ($selectedbillingState == 'Sarawak') ? 'selected' : ''; ?>>Sarawak</option>
											<option value="Selangor" <?php echo ($selectedbillingState == 'Selangor') ? 'selected' : ''; ?>>Selangor</option>
											<option value="Terengganu" <?php echo ($selectedbillingState == 'Terengganu') ? 'selected' : ''; ?>>Terengganu</option>
										</select>
									</div>
									<div class="form-group">
										<label class="info-title" for="Billing City">Billing City <span>*</span></label>
										<select class="form-control unicase-form-control text-input" id="billingcity"
											name="billingcity" required>
											<option value="">Select City</option>
										</select>
									</div>
									<div class="form-group">
										<label class="info-title" for="Billing Pincode">Billing Postcode <span>*</span></label>
										<input type="text" class="form-control unicase-form-control text-input"
											style="color:black;" id="billingpincode" name="billingpincode" required="required"
											value="<?php echo $row['billingPincode']; ?>" maxlength="5" minlength="5"
											pattern="\d{5}" title="Postcode only can be numbers">
									</div>

								</div>

							</td>
							</tr>
							<tr>
								<td colspan="2" style="padding: 0px 24px !important;">
									<input type="checkbox" id="sameAddress">
									<label for="sameAddress">Billing address is same with shipping address.</label>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<button type="submit" name="update"
										class="btn-upper btn btn-primary pull-right checkout-page-button">Update</button>
								</td>
							<?php } ?>
						</tr>
						</tbody><!-- /tbody -->
						</table><!-- /table -->
					</div>

					<div class="col-md-4 col-sm-12 cart-shopping-total">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th colspan='2' style="padding:24px 24px 20px;">
										<div class="cart-grand-total" style="text-align:left;">Summary</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;">Subtotal </div>
									</td>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;text-align:right;">RM
											<?php echo number_format($_SESSION['tp'] = "$totalprice", 2); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;">Shipping Fee</div>
									</td>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;text-align:right;">RM
											<?php
											if ($shipping_fee == 0) {
												echo "0.00";
											} else {
												echo number_format($shipping_fee, 2);
											}
											?>
										</div>
									</td>
								</tr>
								<?php if ($subtotal > 1000) { ?>
									<tr>
										<td colspan="2" style="padding: 5px 24px 5px 24px;">
											<div class="promo-note">
												<span class="promo-text"
													style="font-family:roboto; font-size:12px; padding:0px;color:red;">Congratulations!
													&#127881; You are eligible for free shipping!</span>
											</div>
										</td>
									</tr>

								<?php } ?>
								<tr>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;">Grand Total </div>
									</td>
									<td style="padding:24px 24px 20px; width: 50%;">
										<div style="font-family: 'FjallaOneRegular';font-size:20px;text-align:right;">RM
											<?php echo number_format(($grandtotal), 2); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding:24px;">
										<div class="cart-checkout-btn pull-right">
											<button type="submit" name="ordersubmit" class="btn btn-primary">PROCEED TO
												CHECKOUT</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

					</div>

					<div class="col-md-4 col-sm-12 cart-shopping-total">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th colspan='2' style="padding:24px 24px 20px;">
										<div class="cart-grand-total" style="text-align:left;color:#555;">NOTES</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan='2' style="padding:24px 24px 10px;">
										<div style="font-family: 'Roboto';font-size:15px;"><b>Shipping Rates</b></div>
									</td>
								</tr>
								<tr>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;">Melaka</div>
									</td>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;text-align:right;">RM 100.00</div>
									</td>
								</tr>
								<tr>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;">West Malaysia</div>
									</td>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;text-align:right;">RM 150.00</div>
									</td>
								</tr>
								<tr>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;">East Malaysia</div>
									</td>
									<td style="padding:10px 24px 10px; width: 50%;">
										<div style="font-family: 'Roboto';font-size:15px;text-align:right;">RM 200.00</div>
									</td>
								</tr>
								<tr>
									<td colspan='2' style="padding:10px 24px 24px;">
										<div style="font-family: 'Roboto';font-size:15px;color:red;"><i>** Free shipping for
												all orders over RM 1000.00!</i></div>
									</td>
								</tr>

							</tbody>
						</table>

					<?php } else {
								echo
									"<div>
									<h1 style='text-align:center'>Nothing added to cart.</h1>
								</div>
								<div>
									<img src='assets/images/nothing-here.avif' style='display: block;margin-left: auto;margin-right: auto;width: 30%;' alt='nothing here image'>
								</div>";
							} ?>
				</div>
			</div>
		</div>
		</form>
		<?php include ('includes/brands-slider.php'); ?>
	</div>
	</div>
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

		function enableBillingFieldsBeforeSubmit() {
			document.getElementsByName('billingreceiver')[0].disabled = false;
			document.getElementsByName('billingphone')[0].disabled = false;
			document.getElementsByName('billingaddress')[0].disabled = false;
			document.getElementById('billingstate').disabled = false;
			document.getElementById('billingcity').disabled = false;
			document.getElementById('billingpincode').disabled = false;
		}

		document.addEventListener("DOMContentLoaded", function () {
			// Populate cities based on the selected state
			print_city('shippingcity', '<?php echo $selectedshippingState; ?>', '<?php echo $selectedshippingCity; ?>');
			print_city('billingcity', '<?php echo $selectedbillingState; ?>', '<?php echo $selectedbillingCity; ?>');

			// Store original billing information when the page loads
			var originalBillingReceiver = document.getElementsByName('billingreceiver')[0].value;
			var originalBillingPhone = document.getElementsByName('billingphone')[0].value;
			var originalBillingAddress = document.getElementsByName('billingaddress')[0].value;
			var originalBillingState = document.getElementById('billingstate').value;
			var originalBillingCity = document.getElementById('billingcity').value;
			var originalBillingPincode = document.getElementById('billingpincode').value;

			// Function to copy shipping information to billing information fields
			function copyShippingToBilling() {
				var shippingReceiver = document.getElementsByName('shippingreceiver')[0].value;
				var shippingPhone = document.getElementsByName('shippingphone')[0].value;
				var shippingAddress = document.getElementsByName('shippingaddress')[0].value;
				var shippingState = document.getElementById('shippingstate').value;
				var shippingCity = document.getElementById('shippingcity').value;
				var shippingPincode = document.getElementById('shippingpincode').value;

				// Copy values to billing fields
				document.getElementsByName('billingreceiver')[0].value = shippingReceiver;
				document.getElementsByName('billingphone')[0].value = shippingPhone;
				document.getElementsByName('billingaddress')[0].value = shippingAddress;
				document.getElementById('billingstate').value = shippingState;
				document.getElementById('billingcity').innerHTML = ''; // Clear previous options
				print_city('billingcity', shippingState, shippingCity); // Repopulate billing city based on shipping state
				document.getElementById('billingpincode').value = shippingPincode;
			}

			// Function to restore original billing information
			function restoreOriginalBillingInfo() {
				document.getElementsByName('billingreceiver')[0].value = originalBillingReceiver;
				document.getElementsByName('billingphone')[0].value = originalBillingPhone;
				document.getElementsByName('billingaddress')[0].value = originalBillingAddress;
				document.getElementById('billingstate').value = originalBillingState;
				document.getElementById('billingcity').innerHTML = ''; // Clear previous options
				print_city('billingcity', originalBillingState, originalBillingCity); // Repopulate billing city
				document.getElementById('billingpincode').value = originalBillingPincode;
			}

			// Function to disable billing information fields
			function disableBillingFields() {
				document.getElementsByName('billingreceiver')[0].disabled = true;
				document.getElementsByName('billingphone')[0].disabled = true;
				document.getElementsByName('billingaddress')[0].disabled = true;
				document.getElementById('billingstate').disabled = true;
				document.getElementById('billingcity').disabled = true;
				document.getElementById('billingpincode').disabled = true;
			}

			// Function to enable billing information fields
			function enableBillingFields() {
				document.getElementsByName('billingreceiver')[0].disabled = false;
				document.getElementsByName('billingphone')[0].disabled = false;
				document.getElementsByName('billingaddress')[0].disabled = false;
				document.getElementById('billingstate').disabled = false;
				document.getElementById('billingcity').disabled = false;
				document.getElementById('billingpincode').disabled = false;
			}

			// Listen for changes to the checkbox state
			document.getElementById('sameAddress').addEventListener('change', function () {
				if (this.checked) {
					// If checkbox is checked, copy shipping information to billing information
					copyShippingToBilling();
					// Disable billing information fields
					disableBillingFields();
				} else {
					// If checkbox is unchecked, restore original billing information
					restoreOriginalBillingInfo();
					// Enable billing information fields
					enableBillingFields();
				}
			});
		});

		const phoneInput = document.getElementById('shippingphone');
		// Add an event listener for input changes
		phoneInput.addEventListener('input', function () {
			// Get the phone number value
			const phoneValue = phoneInput.value;

			// Check if the phone number is valid
			if (phoneValue.match(/^\+?[\d\s()-]*$/)) {
				// Phone number is valid
				phoneInput.setCustomValidity('');
			} else {
				// Phone number is invalid
				phoneInput.setCustomValidity('Please enter a valid phone number.');
			}
		});

		const phoneInputb = document.getElementById('billingphone');
		// Add an event listener for input changes
		phoneInputb.addEventListener('input', function () {
			// Get the phone number value
			const phoneValueb = phoneInputb.value;

			// Check if the phone number is valid
			if (phoneValueb.match(/^\+?[\d\s()-]*$/)) {
				// Phone number is valid
				phoneInputb.setCustomValidity('');
			} else {
				// Phone number is invalid
				phoneInputb.setCustomValidity('Please enter a valid phone number.');
			}
		});

	</script>
</body>

</html>
