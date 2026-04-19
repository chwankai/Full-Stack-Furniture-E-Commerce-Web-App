<?php
require_once __DIR__ . '/includes/customer_session.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('includes/config.php');
$order_id = $_GET['order_id'];
if (strlen($_SESSION['login']) == 0) {
	header('location:login.php');
} else {
	date_default_timezone_set('Asia/Kuala_Lumpur');

	if (isset($_POST['submit'])) {
		// Set variables
		$card_number = isset($_POST['saved_card']) ? $_POST['saved_card'] : $_POST['card_number'];
		$exp_date = isset($_POST['saved_card']) ? $_POST['saved_info'] : $_POST['exp_date'];
		$cvv = $_POST['cvv'];
		$order_id = $_GET['order_id'];
		$is_favourite = isset($_POST['favourite']) ? 1 : 0;

		if (isset($_POST['saved_card'])) {
			// Check if CVV matches with CVV in the database
			$old_card_query = mysqli_query($con, "SELECT card_number, cvv FROM card_information WHERE cust_id='" . $_SESSION['id'] . "' AND card_number='$card_number'");
			$old_card_info = mysqli_fetch_assoc($old_card_query);

			$Correct_CVV = $old_card_info['cvv'];

			if ($cvv != $Correct_CVV) {
				// CVV does not match
				header("Location: payment-method.php?message=CVV%20not%20match%20with%20our%20system.%20Please%20try%20again.%20You%20may%20change%20card%20information%20at%20account%20page.");
				exit;
			}
		}

		// Insert data into card_information table only if the checkbox is checked and not selecting from saved cards
		if ($is_favourite && !isset($_POST['saved_card'])) {
			mysqli_query($con, "INSERT INTO card_information (cust_id, card_number, exp_date, cvv) VALUES ('" . $_SESSION['id'] . "', '$card_number', '$exp_date', '$cvv')");
		}

		//Retrieve datetime to update orderDate
		$date = date('Y-m-d H:i:s');

		// // Update the order regardless of the CVV check if the card is not from saved cards
		// if (!isset($_POST['saved_card'])) {
		mysqli_query($con, "UPDATE orders SET orderDate='$date', paymentMethod='Debit / Credit Card', orderStatus='Order Placed' WHERE userId='" . $_SESSION['id'] . "' AND id = '$order_id' AND paymentMethod IS NULL");
		// Call the send order placed PHP script
		include ('send_order_placed.php');
		?>
		<script>
			// Hide the loader modal popup when the processing is complete
			$('#loaderModal').modal('hide');
		</script>
		<?php
		// Redirect to order history with a success message
		header("Location: order-history.php?message=Order%20placed%20successfully.");
		exit;
		// }
	} else if (isset($_POST["e-submit"])) {
		$order_id = $_GET['order_id'];
		$query = mysqli_query($con, "SELECT users.balance as balance FROM users WHERE id = '" . mysqli_real_escape_string($con, $_SESSION['id']) . "'");
		$row = mysqli_fetch_array($query);
		$orderquery = mysqli_query($con, "SELECT grandtotal FROM orders where userId='" . $_SESSION['id'] . "' AND id = '$order_id'");
		$orderrow = mysqli_fetch_array($orderquery);

		$balance = $row['balance'];
		$grandtotal = $orderrow['grandtotal'];

		if ($balance < $grandtotal) {
			unset($balance);
			unset($grandtotal); ?>
				<script>
					// Hide the loader modal popup when the processing is complete
					$('#loaderModal').modal('hide');
				</script>
				<?php
				header("Location: wallet.php?message=Balance%20insufficient.%20Please%20top%20up%20and%20try%20again.");
				exit;
		}

		$new_balance = $balance - $grandtotal;

		//Retrieve datetime to update orderDate
		$date = date('Y-m-d H:i:s');

		mysqli_query($con, "UPDATE users SET balance='$new_balance' WHERE id='" . $_SESSION['id'] . "'");
		mysqli_query($con, "UPDATE orders SET orderDate='$date', paymentMethod='e-Wallet', orderStatus='Order Placed' WHERE userId='" . $_SESSION['id'] . "' AND id = '$order_id' AND paymentMethod IS NULL");
		mysqli_query($con, "INSERT INTO transaction (user_id, order_id, action, amount)  VALUES ('" . $_SESSION['id'] . "','$order_id', 'Pay', '$grandtotal')");
		unset($balance);
		unset($grandtotal);
		unset($new_balance);
		include ('send_order_placed.php');
		?>
			<script>
				// Hide the loader modal popup when the processing is complete
				$('#loaderModal').modal('hide');
			</script>
			<?php
			// Redirect to order history with a success message
			header("Location: order-history.php?message=Order%20placed%20successfully.");
			exit;
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

		<title>Shopping Portal | Checkout</title>
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/css/main.css">
		<link rel="stylesheet" href="assets/css/orange.css">
		<link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/card_payment.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/config.css">
		<link rel="stylesheet" href="assets/css/loader.css">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">
		<script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
		<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
		<!-- for Recaptcha -->
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

		<header class="header-style-1">
			<?php include ('includes/top-header.php'); ?>
			<?php include ('includes/main-header.php'); ?>
			<?php include ('includes/menu-bar.php'); ?>
		</header>
		<div class="breadcrumb">
			<div class="container">
				<div class="breadcrumb-inner">
					<ul class="list-inline list-unstyled">
						<li><a href="index.php">Home</a></li>
						<li class='active'>Checkout</li>
					</ul>
				</div><!-- /.breadcrumb-inner -->
			</div><!-- /.container -->
		</div><!-- /.breadcrumb -->

		<div class="body-content outer-top-bd">
			<div class="container">
				<div class="checkout-box faq-page inner-bottom-sm">
					<div class="row">
						<div class="col-md-12">
							<h2>CHECKOUT</h2>
							<div class="panel-group checkout-steps" id="accordion">
								<!-- checkout-step-01  -->
								<div class="panel panel-default checkout-step-01">

									<!-- panel-heading -->
									<div class="panel-heading">
										<h4 class="unicase-checkout-title">
											<a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
												Enter your credit/debit card information
											</a>
										</h4>
									</div>
									<!-- panel-heading -->

									<div id="collapseOne" class="panel-collapse collapse in">

										<!-- panel-body  -->
										<div class="panel-body">
											<div class="methods" style="display:flex;">
												<div onclick="doFun()" id="tColorA" class="methods_button"><i
														class="fa-solid fa-credit-card"></i>Pay by Card</div>
												<div onclick="doFunA()" id="tColorB" class="methods_button"><i
														class="fa-solid fa-wallet"></i>OFS e-Wallet</div>
											</div>
											<div class="card-details">
												<div>
													<img alt="Credit Card Logos" class="cc-logo" title="Credit Card Logos"
														src="assets/images/payment_icon.png" width="250" height="77px" />
												</div>
												<br>
												<hr style="border:1px solid #ccc; margin: 5px 0px;">
												<?php $total = mysqli_query($con, "SELECT orders.subtotal, orders.shippingCharge, orders.grandtotal FROM orders where userId='" . $_SESSION['id'] . "' AND id='$order_id'");
												$total_info = mysqli_fetch_array($total); ?>
												<div class="order-details">
													<div><b>Order ID:</b> #<?php echo $order_id ?></div>
													<div><b>Subtotal:</b> RM<?php echo $total_info['subtotal']; ?></div>
													<div><b>Shipping Charge:</b>
														RM<?php echo $total_info['shippingCharge']; ?></div>
													<div><b>Grandtotal:</b> RM<?php echo $total_info['grandtotal']; ?></div>
												</div>

												<hr style="border:1px solid #ccc; margin: 5px 0px;">

												<form id="payment-form" name="payment" method="post">

													<p style="margin-top:10px;">Select a saved card</p>

													<div class="saved-card" id="saved-card">
														<select name="saved_card" id="saved_info"
															onchange="assignFunction(event)">
															<option value="" disabled selected>Select a saved card</option>
															<?php
															// Retrieve saved cards for the logged-in user
															$result = mysqli_query($con, "SELECT card_number, exp_date, cvv FROM card_information WHERE cust_id = '" . $_SESSION['id'] . "'");
															while ($row = mysqli_fetch_assoc($result)) {
																// Display each saved card as an option in the selection box
																echo "<option value='" . $row['card_number'] . "' data-expiry-date='" . $row['exp_date'] . "' data-cvv='" . $row['cvv'] . "' >" . $row['card_number'] . " - " . $row['exp_date'] . "</option>";
															}
															?>
														</select>
													</div>

													<p>Card number</p>
													<div class="c-number" id="c-number">
														<input id="number" name="card_number" class="cc-number"
															placeholder="Card number" maxlength="19" required>
														<i class="fa-solid fa-credit-card" style="margin: 0;"></i>
													</div>
													<span id="number-error" class="error-message" style="color:red;"></span>

													<div class="c-details">
														<div>
															<p>Expiry date</p>
															<!-- <input id="e-date" name="exp_date" class="cc-exp" placeholder="MM/YY" required maxlength="5"> -->
															<input type="month" id="e-date" name="exp_date" class="cc-exp"
																required>
															<!-- <span id="expiry-date-error" class="error-message" style="color:red;"></span> -->
														</div>

														<div>
															<p>CVV</p>
															<div class="cvv-box" id="cvv-box">
																<input id="cvv" name="cvv" class="cc-cvv" placeholder="CVV"
																	required maxlength="3">
																<i class="fa-solid fa-circle-question"
																	title="3 digits on the back of the card"
																	style="cursor: pointer;"></i>
															</div>
														</div>
													</div>
													<div>
														<span id="cvv-error" class="error-message"
															style="color:red;text-align: right;"></span>
													</div>
													<div class="fvrt">
														<input type="checkbox" name="favourite" id="favourite"
															class="favourite">
														<label for="favourite"> Save card information</label>
													</div>
													<br>
													<!-- Recaptcha here -->
													<div class="g-recaptcha" id="divRecaptcha1"></div>
													<div>
														<input type="submit" value="PAY NOW" name="submit" id="submitBtn"
															class="btn btn-upper btn-primary pull-right outer-right-xs">
														<input type="reset" value="CLEAR" name="clear"
															style="margin:0px 10px;"
															class="btn btn-upper btn-primary pull-right outer-right-xs">
													</div>

												</form>
												<br><br>
											</div>

											<div class="ewallet-details" style="display:none;">
												<div>
													<img alt="Credit Card Logos" class="cc-logo" title="Credit Card Logos"
														src="assets/images/ewallet_icon.png" width="auto" height="77px"
														style="width:13% !important;" />
												</div>
												<br>
												<hr style="border:1px solid #ccc; margin: 5px 0px;">
												<div class="order-details">
													<div><b>Order ID:</b> #<?php echo $order_id ?></div>
													<div><b>Subtotal:</b> RM<?php echo $total_info['subtotal']; ?></div>
													<div><b>Shipping Charge:</b>
														RM<?php echo $total_info['shippingCharge']; ?></div>
													<div><b>Grandtotal:</b> RM<?php echo $total_info['grandtotal']; ?></div>
												</div>
												<hr style="border:1px solid #ccc; margin: 5px 0px;">
												<form id="payment-form" name="payment" method="post">
													<?php $tvalue = mysqli_query($con, "SELECT users.balance FROM users WHERE id='" . $_SESSION['id'] . "'");
													$tvalue_info = mysqli_fetch_array($tvalue); ?>
													<div class="wallet-balance"><b>Wallet Balance:</b> RM
														<?php echo $tvalue_info['balance']; ?></div>
													<br>
													<!-- Recaptcha here -->
													<div class="g-recaptcha" style="  margin: 0 auto; width: 304px;" id="divRecaptcha2"></div>
													<div>
														<input type="submit" value="PAY WITH YOUR WALLET" name="e-submit"
															id="e-submit"
															style="text-align:center !important; margin:40px auto 0px;display:block;"
															class="btn btn-upper btn-primary outer-right-xs">
													</div>
												</form>
												<br><br>
											</div>
											<!-- Modal Popup -->
											<div id="loaderModal" class="modal fade" role="dialog">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-body">
															<div id="loader"></div>
															<!-- Text: Please wait while we are submitting your order -->
															<p style="text-align:center;">Please wait while we are
																processing your order...</p>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- panel-body  -->
									</div><!-- row -->
								</div>
								<!-- checkout-step-01  -->
							</div><!-- /.checkout-steps -->
						</div>
					</div><!-- /.row -->
				</div><!-- /.checkout-box -->
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
		<script src="assets/js/card.js"></script>
		<script src="switchstylesheet/switchstylesheet.js"></script>

		<script>
			let cDetails = document.querySelector('.card-details'),
				eDetails = document.querySelector('.ewallet-details')
			buttonA = document.getElementById('tColorA'),
				buttonB = document.getElementById('tColorB');

			function doFun() {
				cDetails.style.display = "block";
				eDetails.style.display = "none";
				buttonA.style.backgroundColor = "#ccc";
				buttonB.style.backgroundColor = "#e8e6e6";
			}
			function doFunA() {
				cDetails.style.display = "none";
				eDetails.style.display = "block";
				buttonA.style.backgroundColor = "#e8e6e6";
				buttonB.style.backgroundColor = "#ccc";
			}

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

			function assignFunction(e) {
				var selectedOption = e.target.options[e.target.selectedIndex];
				var expiryDate = selectedOption.getAttribute('data-expiry-date');
				//var cardcvv = selectedOption.getAttribute('data-cvv');
				document.getElementById("number").value = selectedOption.value;
				document.getElementById("e-date").value = expiryDate;
				//document.getElementById("cvv").value = cardcvv;

				// Disable the "Save card information" checkbox
				document.getElementById("favourite").disabled = true;
				// document.getElementById("number").disabled = true;
				// document.getElementById("e-date").disabled = true;
				var cvvInput = document.getElementById('cvv');
				var cvvValue = cvvInput.value.trim();
				var errorMessageElement = document.getElementById('cvv-error');
				if (cvvValue === '') {
					errorMessageElement.textContent = 'Please enter your CVV to verify.';
				}

			}

			function onloadCallback() {
				grecaptcha.render('divRecaptcha1', {
					'sitekey': '6Le_h8opAAAAAAnGwFtaAqF3l_ZKcTrxvOhq1hFO',
					//for debug
					callback: successcallback
				});
				grecaptcha.render('divRecaptcha2', {
					'sitekey': '6Le_h8opAAAAAAnGwFtaAqF3l_ZKcTrxvOhq1hFO',
					//for debug
					callback: successcallback
				});
			};

			//for debug Recaptcha
			function successcallback(token) {
				debugger;
			}

			//to prevent user skip the recaptcha (make recaptcha required)
			window.onload = function () {
				var $recaptcha = document.querySelector('#g-recaptcha-response');

				if ($recaptcha) {
					$recaptcha.setAttribute("required", "required");
				}

				var $recaptcha2 = document.querySelector('#divRecaptcha2 textarea');

				if ($recaptcha2) {
					$recaptcha2.setAttribute("required", "required");
				}

				// Add event listener to the form submit button
				document.getElementById('submitBtn').addEventListener('click', function (event) {
					var cardNumber = document.getElementById('number').value;
					var cvv = document.getElementById('cvv').value;
					var $recaptcha = document.querySelector('#g-recaptcha-response');

					// Card number validation
					if (!validateCardNumber(cardNumber)) {
						event.preventDefault(); // Prevent form submission
						alert("Please enter a valid card number (16 digits).");
						return; // Exit the function if validation fails
					}

					// CVV validation
					if (cvv.length !== 3) {
						event.preventDefault(); // Prevent form submission
						alert("CVV must be 3 digits.");
						return; // Exit the function if validation fails
					}

					// reCAPTCHA validation
					if (!$recaptcha || !$recaptcha.value) {
						event.preventDefault(); // Prevent form submission
						alert("Please complete the reCAPTCHA before proceeding.");
						return; // Exit the function if validation fails
					}
					else {
						// Show the loader modal popup
						$('#loaderModal').modal('show');
					}
				});

				document.getElementById('e-submit').addEventListener('click', function (event) {

					var $recaptcha = document.querySelector('#divRecaptcha2 textarea');

					// reCAPTCHA validation
					if (!$recaptcha || !$recaptcha.value) {
						event.preventDefault(); // Prevent form submission
						alert("Please complete the reCAPTCHA before proceeding.");
						return; // Exit the function if validation fails
					}
					else {
						// Show the loader modal popup
						$('#loaderModal').modal('show');
					}

				});
			};

			// Function to validate reCAPTCHA for e-wallet section
			function validateRecaptchaEwallet() {
				var $recaptcha = document.querySelector('#divRecaptcha2 textarea');
				if (!$recaptcha || !$recaptcha.value) {
					alert("Please complete the fffreCAPTCHA before proceeding.");
					return false; // Exit the function if reCAPTCHA is not completed
				}
				return true; // Return true if reCAPTCHA is completed
			}

			// Function to validate card number format
			function validateCardNumber(cardNumber) {
				var cardRegex = /^\d{4}\s\d{4}\s\d{4}\s\d{4}$/;
				return cardRegex.test(cardNumber);
			}

			// Set the dafault and min value of expiry date to this month
			var today = new Date();
			// Format the current date as YYYY-MM
			var year = today.getFullYear();
			var month = today.getMonth() + 1; // January is 0
			if (month < 10) {
				month = "0" + month; // Add leading zero if month is less than 10
			}
			var currentDate = year + "-" + month;
			// Set the value of the input field to the current date
			document.getElementById("e-date").value = currentDate;
			document.getElementById("e-date").min = currentDate;
		</script>

	</body>

	</html>
<?php } ?>
