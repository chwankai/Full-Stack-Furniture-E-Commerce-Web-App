<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
$tvalue = $_GET['value'];
if (strlen($_SESSION['login']) == 0) {
	header('location:login.php');
} else {
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
				header("Location: wallet-checkout.php?value=$tvalue&message=CVV%20not%20match%20with%20our%20system.%20Please%20try%20again.%20You%20may%20change%20card%20information%20at%20account%20page.");
				exit;
			}
		}

		// Insert data into card_information table only if the checkbox is checked and not selecting from saved cards
		if ($is_favourite && !isset($_POST['saved_card'])) {
			mysqli_query($con, "INSERT INTO card_information (cust_id, card_number, exp_date, cvv) VALUES ('" . $_SESSION['id'] . "', '$card_number', '$exp_date', '$cvv')");
		}

		// // Update the order regardless of the CVV check if the card is not from saved cards
		// if (!isset($_POST['saved_card'])) {
		$old_value = mysqli_query($con, "SELECT balance FROM users WHERE id ='" . $_SESSION['id'] . "'");
		$old_value_info = mysqli_fetch_assoc($old_value);

		$old_value = $old_value_info['balance'];

		$new_value = $old_value + $tvalue;

		mysqli_query($con, "UPDATE users SET balance = $new_value WHERE id='" . $_SESSION['id'] . "'");
		mysqli_query($con, "INSERT INTO transaction (user_id, action, amount)  VALUES ('" . $_SESSION['id'] . "', 'Reload', '$tvalue')");
		unset($tvalue);
		?>
		<script>
			// Hide the loader modal popup when the processing is complete
			$('#loaderModal').modal('hide');
		</script>
		<?php
		// Redirect to order history with a success message
		header("Location: wallet.php?message=Top%20up%20successfully.");
		exit;
		// }
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

		<title>Shopping Portal | Top Up Wallet</title>
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
						<li class='active'>Top Up Wallet</li>
					</ul>
				</div><!-- /.breadcrumb-inner -->
			</div><!-- /.container -->
		</div><!-- /.breadcrumb -->

		<div class="body-content outer-top-bd">
			<div class="container">
				<div class="checkout-box faq-page inner-bottom-sm">
					<div class="row">
						<div class="col-md-12">
							<h2>TOP UP YOUR WALLET: RM <?php echo $tvalue; ?></h2>
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
											<div class="card-details">
												<div>
													<img alt="Credit Card Logos" class="cc-logo" title="Credit Card Logos"
														src="assets/images/payment_icon.png" width="250" height="auto" />
												</div>
												<br>
												<hr style="border:1px solid #ccc; margin: 5px 30px;">
												<br>
												<form id="payment-form" name="payment" method="post">

													<p>Select a saved card</p>

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
													<div class="g-recaptcha" id="divRecaptcha"></div>
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
											<!-- Modal Popup -->
											<div id="loaderModal" class="modal fade" data-backdrop="static" data-keyboard="false">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-body">
															<div id="loader"></div>
															<!-- Text: Please wait while we are submitting your order -->
															<p style="text-align:center;">Please wait while we are processing your request...</p>
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
				grecaptcha.render('divRecaptcha', {
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

				// Add event listener to the form submit button
				document.getElementById('submitBtn').addEventListener('click', function (event) {
					var cardNumber = document.getElementById('number').value;
					var cvv = document.getElementById('cvv').value;

					// Card number validation
					if (!validateCardNumber(cardNumber)) {
						event.preventDefault(); // Prevent form submission
						alert("Please enter a valid card number (16 digits).");
					}

					// CVV validation
					if (cvv.length !== 3) {
						event.preventDefault(); // Prevent form submission
						alert("CVV must be 3 digits.");
					}

					// reCAPTCHA validation
					if (!$recaptcha || !$recaptcha.value) {
						event.preventDefault(); // Prevent form submission
						alert("Please complete the reCAPTCHA before proceeding.");
					}
				});
			};

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

			//To show the loader modal popup
			document.getElementById('submitBtn').addEventListener('click', function (event) {
				$('#loaderModal').modal('show');
			});

		</script>

	</body>

	</html>
<?php } ?>
