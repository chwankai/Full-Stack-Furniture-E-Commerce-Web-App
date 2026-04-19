<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');//checking database connection
if (strlen($_SESSION['login']) == 0) {
	header('location:login.php');
} else {
	if (isset($_POST['update']))//update payment info and profile
	{
		$card_number = $_POST['card_number'];
		$exp_date = $_POST['exp_date'];
		$cvv = $_POST['cvv'];
		$card_id = $_POST['saved_card'];

		$old_card_query = mysqli_query($con, "SELECT card_number, exp_date, cvv FROM card_information WHERE cust_id='" . $_SESSION['id'] . "' AND card_id='$card_id'");
		$old_card_info = mysqli_fetch_assoc($old_card_query);

		// Store old card information into old_card_information table
		$old_card_number = $old_card_info['card_number'];
		$old_exp_date = $old_card_info['exp_date'];
		$old_cvv = $old_card_info['cvv'];
		$update_old_card_query = mysqli_query($con, "INSERT INTO old_card_information (card_id, cust_id, card_number, exp_date, cvv, action) VALUES ('$card_id', '" . $_SESSION['id'] . "', '$old_card_number', '$old_exp_date', '$old_cvv', 'UPDATE')");

		$query = mysqli_query($con, "update card_information set card_number='$card_number',exp_date='$exp_date',cvv='$cvv' where cust_id='" . $_SESSION['id'] . "' and card_id='$card_id' ");
		if ($query && $update_old_card_query) {
			header("Location: saved_payment.php?message=Card%20updated%20successfully.");

			exit;
		}
	} else if (isset($_POST['delete'])) {
		$card_number = $_POST['card_number'];
		$card_id = $_POST['saved_card'];

		$old_card_query = mysqli_query($con, "SELECT card_number, exp_date, cvv FROM card_information WHERE cust_id='" . $_SESSION['id'] . "' AND card_id='$card_id'");
		$old_card_info = mysqli_fetch_assoc($old_card_query);

		// Store old card information into old_card_information table
		$old_card_number = $old_card_info['card_number'];
		$old_exp_date = $old_card_info['exp_date'];
		$old_cvv = $old_card_info['cvv'];
		$delete_old_card_query = mysqli_query($con, "INSERT INTO old_card_information (card_id, cust_id, card_number, exp_date, cvv, action) VALUES ('$card_id', '" . $_SESSION['id'] . "', '$old_card_number', '$old_exp_date', '$old_cvv', 'DELETE')");

		$query = mysqli_query($con, "delete from card_information where cust_id='" . $_SESSION['id'] . "' and card_id='$card_id' ");

		if ($query && $delete_old_card_query) {
			header("Location: saved_payment.php?message=Card%20deleted%20successfully.");
			exit;
		}
	} else if (isset($_POST['add'])) {
		$card_number = $_POST['card_number'];
		$exp_date = $_POST['exp_date'];
		$cvv = $_POST['cvv'];

		$query = mysqli_query($con, "INSERT INTO card_information (cust_id, card_number, exp_date, cvv) VALUES ('" . $_SESSION['id'] . "', '$card_number', '$exp_date', '$cvv')");
		if ($query) {
			header("Location: saved_payment.php?message=Card%20added%20successfully.");
			exit;
		}

	}


	date_default_timezone_set('Asia/Kuala_Lumpur');// change according timezone
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

		<title>Saved Payment Mehotd</title>

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
			function valid()//checking validation of password 
			{
				if (document.chngpwd.cpass.value == "")//check password field is empty or not
				{
					alert("Current Password Filed is Empty !!");
					document.chngpwd.cpass.focus();
					return false;
				}
				else if (document.chngpwd.newpass.value == "") {
					alert("New Password Filed is Empty !!");
					document.chngpwd.newpass.focus();//focus on new password field
					return false;
				}
				else if (document.chngpwd.cnfpass.value == "") {
					alert("Confirm Password Filed is Empty !!");
					document.chngpwd.cnfpass.focus();
					return false;
				}
				else if (document.chngpwd.newpass.value != document.chngpwd.cnfpass.value) {
					alert("Password and Confirm Password Field do not match  !!");
					document.chngpwd.cnfpass.focus();
					return false;
				} else if (document.chngpwd.newpass.value == document.chngpwd.cpass.value) {
					// Check if new password is the same as the current password
					alert("New Password should not be the same as the Current Password !!");
					document.chngpwd.newpass.focus();
					return false;
				} else if (document.chngpwd.newpass.value.length < 6) {
					alert("Password must be at least 6 characters long.");
					document.chngpwd.newpass.focus();
					return false;
				}

				return true;

			}

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
						<li class='active'>Saved Payment</li>
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
												<span>1</span>Update / Delete Saved Card
											</a>
										</h4>
									</div>
									<!-- panel-heading -->

									<div id="collapseOne" class="panel-collapse collapse in">

										<!-- panel-body  -->
										<div class="panel-body">
											<div class="row">
												<form id="update-card-form" name="update-card" method="post">
													<p>Select a saved card</p>
													<div class="saved-card form-group" id="saved-card">
														<select name="saved_card" id="saved_info"
															class="form-control unicase-form-control text-input"
															onchange="assignFunction(event)">
															<option value="" disabled selected>Select a card to update
																information</option>
															<?php
															// Retrieve saved cards for the logged-in user
															$result = mysqli_query($con, "SELECT card_id, card_number, exp_date, cvv FROM card_information WHERE cust_id = '" . $_SESSION['id'] . "'");
															while ($row = mysqli_fetch_assoc($result)) {
																// Display each saved card as an option in the selection box
																echo "<option value='" . $row['card_id'] . "' card-number='" . $row['card_number'] . "' data-expiry-date='" . $row['exp_date'] . "' data-cvv='" . $row['cvv'] . "' >" . $row['card_number'] . " - " . $row['exp_date'] . "</option>";
															}
															?>
														</select>
													</div>

													<p>Card number</p>
													<div class="c-number form-group" id="c-number">
														<input id="number" name="card_number"
															class="form-control unicase-form-control text-input"
															placeholder="Card number" disabled maxlength="19" required>
													</div>
													<span id="number-error" class="error-message" style="color:red;"></span>

													<div class="c-details">
														<div class=" form-group">
															<p>Expiry date</p>
															<input type="month" id="e-date" name="exp_date"
																class="form-control unicase-form-control text-input"
																placeholder="MM/YY" required disabled maxlength="5">
															<span id="expiry-date-error" class="error-message"
																style="color:red;"></span>
														</div>
														<div>
															<p>CVV</p>
															<div class="cvv-box form-group" id="cvv-box">
																<input id="cvv" name="cvv"
																	class="form-control unicase-form-control text-input"
																	placeholder="CVV" required disabled maxlength="3">
																<!-- <span id="cvv-error" class="error-message" style="color:red;"></span> -->
															</div>
														</div>
													</div>

													<!-- DF -->
													<input type="submit" value="UPDATE" name="update"
														class="btn btn-primary pull-right outer-right-xs">
													<input type="submit" value="DELETE" name="delete"
														style="margin:0px 10px;"
														class="btn btn-primary pull-right outer-right-xs">
													<input type="reset" value="CLEAR" name="clear" style="margin:0px 10px;"
														class="btn btn-default pull-left outer-right-xs">
												</form>

											</div>
										</div>
										<!-- panel-body  -->

									</div><!-- row -->
								</div>
								<!-- checkout-step-01  -->
								<!-- checkout-step-02  -->
								<div class="panel panel-default checkout-step-02">
									<div class="panel-heading">
										<h4 class="unicase-checkout-title">
											<a data-toggle="collapse" class="collapsed" data-parent="#accordion"
												href="#collapseTwo">
												<span>2</span>Save new card
											</a>
										</h4>
									</div>
									<div id="collapseTwo" class="panel-collapse collapse">
										<div class="panel-body">

											<form id="update-card-form" name="update-card" method="post">
												<p>Card number</p>
												<div class="c-number form-group" id="c-number2">
													<input id="number2" name="card_number"
														class="form-control unicase-form-control text-input"
														placeholder="Card number" maxlength="19" required>
												</div>
												<span id="number-error2" class="error-message" style="color:red;"></span>

												<div class="c-details">
													<div class=" form-group">
														<p>Expiry date</p>
														<input type="month" id="e-date2" name="exp_date"
															class="form-control unicase-form-control text-input"
															placeholder="MM/YY" required maxlength="5">
														<span id="expiry-date-error2" class="error-message"
															style="color:red;"></span>
													</div>
													<div>
														<p>CVV</p>
														<div class="cvv-box form-group" id="cvv-box2">
															<input id="cvv2" name="cvv"
																class="form-control unicase-form-control text-input"
																placeholder="CVV" required maxlength="3">
															<!-- <span id="cvv-error" class="error-message" style="color:red;"></span> -->
														</div>
													</div>
												</div>

												<input type="submit" value="SAVE CARD" name="add"
													class="btn btn-primary pull-right outer-right-xs">
												<input type="reset" value="CLEAR" name="clear" style="margin:0px 10px;"
													class="btn btn-default pull-left outer-right-xs">
											</form>


										</div>
									</div>
								</div>
								<!-- checkout-step-02  -->

							</div><!-- /.checkout-steps -->
						</div>
						<?php include ('includes/myaccount-sidebar.php'); ?>
					</div><!-- /.row -->
				</div><!-- /.checkout-box -->
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

				var cardnumber = selectedOption.getAttribute('card-number');
				var expiryDate = selectedOption.getAttribute('data-expiry-date');
				var cardcvv = selectedOption.getAttribute('data-cvv');
				document.getElementById("number").value = cardnumber;
				document.getElementById("e-date").value = expiryDate;
				document.getElementById("cvv").value = cardcvv;

				// Disable the "Save card information" checkbox
				document.getElementById("number").disabled = false;
				document.getElementById("e-date").disabled = false;
				document.getElementById("cvv").disabled = false;
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
			document.getElementById("e-date2").value = currentDate;
			document.getElementById("e-date2").min = currentDate;

		</script>
	</body>

	</html>
<?php } ?>
