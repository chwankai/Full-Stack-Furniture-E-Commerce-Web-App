<?php
require_once __DIR__ . '/includes/customer_session.php';
//error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['login']) == 0) {
	header('location:.php');
} else {
	// code for billing address updation
	if (isset($_POST['update'])) {
		$baddress = $_POST['billingaddress'];
		$bstate = $_POST['billingstate'];
		$bcity = $_POST['billingcity'];
		$bpincode = $_POST['billingpincode'];
		$query = mysqli_query($con, "update users set billingAddress='$baddress',billingState='$bstate',billingCity='$bcity',billingPincode='$bpincode' where id='" . $_SESSION['id'] . "'");
		if ($query) {
			echo "<script>alert('Billing Address has been updated');</script>";
		}
	}

	// code for Shipping address updation
	if (isset($_POST['shipupdate'])) {
		$saddress = $_POST['shippingaddress'];
		$sstate = $_POST['shippingstate'];
		$scity = $_POST['shippingcity'];
		$spincode = $_POST['shippingpincode'];
		$query = mysqli_query($con, "update users set shippingAddress='$saddress',shippingState='$sstate',shippingCity='$scity',shippingPincode='$spincode' where id='" . $_SESSION['id'] . "'");
		if ($query) {
			echo "<script>alert('Shipping Address has been updated');</script>";
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

		<title>My Account</title>

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
		<script src="assets/js/statecitylist.js"></script>
	</head>

	<body class="cnt-home">
		<header class="header-style-1">

			<!-- ============================================== TOP MENU ============================================== -->
			<?php include('includes/top-header.php'); ?>
			<!-- ============================================== TOP MENU : END ============================================== -->
			<?php include('includes/main-header.php'); ?>
			<!-- ============================================== NAVBAR ============================================== -->
			<?php include('includes/menu-bar.php'); ?>
			<!-- ============================================== NAVBAR : END ============================================== -->

		</header>
		<!-- ============================================== HEADER : END ============================================== -->
		<div class="breadcrumb">
			<div class="container">
				<div class="breadcrumb-inner">
					<ul class="list-inline list-unstyled">
						<li><a href="index.php">Home</a></li>
						<li class='active'>Saved Address</li>
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
									<div class="panel-heading">
										<h4 class="unicase-checkout-title">
											<a data-toggle="collapse" class="collapsed" data-parent="#accordion"
												href="#collapseTwo">
												<span>1</span>Shipping Address
											</a>
										</h4>
									</div>
									<div id="collapseTwo" class="panel-collapse collapse">
										<div class="panel-body">

											<?php
											$query = mysqli_query($con, "select * from users where id='" . $_SESSION['id'] . "'");
											while ($row = mysqli_fetch_array($query)) {
												?>

												<form class="register-form" role="form" method="post">
													<div class="form-group">
														<label class="info-title" for="Shipping Address">Shipping
															Address<span>*</span></label>
														<textarea class="form-control unicase-form-control text-input"
															name="shippingaddress"
															required="required"><?php echo $row['shippingAddress']; ?></textarea>
													</div>

													<div class="form-group">
														<label class="info-title" for="Billing State ">Shipping State
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
														<label class="info-title" for="Billing City">Shipping City
															<span>*</span></label>
														<select type="text" class="form-control unicase-form-control text-input"
															id="shippingcity" name="shippingcity" required>
															<option value="<?php echo $row['shippingCity']; ?>">
																<?php echo $row['shippingCity']; ?>
															</option>
															<option value=""></option>
														</select>
													</div>
													<div class="form-group">
														<label class="info-title" for="Billing Pincode">Shipping Postcode
															<span>*</span></label>
														<input type="text" class="form-control unicase-form-control text-input"
															id="shippingpincode" name="shippingpincode" required="required"
															value="<?php echo $row['shippingPincode']; ?>" maxlength="5"
															minlength="5" pattern="\d{5}" title="Postcode only can be numbers">
													</div>


													<button type="submit" name="shipupdate"
														class="btn-upper btn btn-primary checkout-page-button">Update</button>
												</form>
											<?php } ?>
										</div>
									</div>

								</div>
								<!-- checkout-step-01  -->
								<!-- checkout-step-02  -->
								<div class="panel panel-default checkout-step-02">
									<!-- panel-heading -->
									<div class="panel-heading">
										<h4 class="unicase-checkout-title">
											<a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
												<span>1</span>Billing Address
											</a>
										</h4>
									</div>
									<!-- panel-heading -->

									<div id="collapseOne" class="panel-collapse collapse in">

										<!-- panel-body  -->
										<div class="panel-body">
											<div class="row">
												<div class="col-md-12 col-sm-12 already-registered-login">

													<?php
													$query = mysqli_query($con, "select * from users where id='" . $_SESSION['id'] . "'");
													while ($row = mysqli_fetch_array($query)) {
														$selectedshippingState = $row['shippingState'];
														$selectedshippingCity = $row['shippingCity'];
														$selectedbillingState = $row['billingState'];
														$selectedbillingCity = $row['billingCity'];
														?>

														<form class="register-form" role="form" method="post">
															<div class="form-group">
																<label class="info-title" for="Billing Address">Billing
																	Address<span>*</span></label>
																<textarea class="form-control unicase-form-control text-input"
																	name="billingaddress"
																	required="required"><?php echo $row['billingAddress']; ?></textarea>
															</div>
															<div class="form-group">
																<label class="info-title" for="Billing State ">Billing State
																	<span>*</span></label>
																<select type="text"
																	class="form-control unicase-form-control text-input"
																	id="billingstate" name="billingstate"
																	onchange="print_city('billingcity', this.value);" required>
																	<option value="">Select State</option>
																	<option value="Johor" <?php echo ($selectedbillingState == 'Johor') ? 'selected' : ''; ?>>
																		Johor</option>
																	<option value="Kedah" <?php echo ($selectedbillingState == 'Kedah') ? 'selected' : ''; ?>>
																		Kedah</option>
																	<option value="Kelantan" <?php echo ($selectedbillingState == 'Kelantan') ? 'selected' : ''; ?>>Kelantan</option>
																	<option value="Kuala Lumpur" <?php echo ($selectedbillingState == 'Kuala Lumpur') ? 'selected' : ''; ?>>Kuala Lumpur</option>
																	<option value="Melaka" <?php echo ($selectedbillingState == 'Melaka') ? 'selected' : ''; ?>>
																		Melaka</option>
																	<option value="Negeri Sembilan" <?php echo ($selectedbillingState == 'Negeri Sembilan') ? 'selected' : ''; ?>>Negeri Sembilan</option>
																	<option value="Pahang" <?php echo ($selectedbillingState == 'Pahang') ? 'selected' : ''; ?>>
																		Pahang</option>
																	<option value="Perak" <?php echo ($selectedbillingState == 'Perak') ? 'selected' : ''; ?>>
																		Perak</option>
																	<option value="Perlis" <?php echo ($selectedbillingState == 'Perlis') ? 'selected' : ''; ?>>
																		Perlis</option>
																	<option value="Pulau Pinang" <?php echo ($selectedbillingState == 'Pulau Pinang') ? 'selected' : ''; ?>>Pulau Pinang</option>
																	<option value="Sabah" <?php echo ($selectedbillingState == 'Sabah') ? 'selected' : ''; ?>>
																		Sabah</option>
																	<option value="Sarawak" <?php echo ($selectedbillingState == 'Sarawak') ? 'selected' : ''; ?>>Sarawak</option>
																	<option value="Selangor" <?php echo ($selectedbillingState == 'Selangor') ? 'selected' : ''; ?>>Selangor</option>
																	<option value="Terengganu" <?php echo ($selectedbillingState == 'Terengganu') ? 'selected' : ''; ?>>Terengganu</option>
																</select>
															</div>
															<div class="form-group">
																<label class="info-title" for="Billing City">Billing City
																	<span>*</span></label>
																<select class="form-control unicase-form-control text-input"
																	id="billingcity" name="billingcity" required>
																	<option value="<?php echo $row['billingCity']; ?>">
																		<?php echo $row['billingCity']; ?>
																	</option>
																	<option value="">Select City</option>
																</select>
															</div>
															<div class="form-group">
																<label class="info-title" for="Billing Pincode">Billing Postcode
																	<span>*</span></label>
																<input type="text"
																	class="form-control unicase-form-control text-input"
																	id="billingpincode" name="billingpincode"
																	required="required"
																	value="<?php echo $row['billingPincode']; ?>" maxlength="5"
																	minlength="5" pattern="\d{5}"
																	title="Postcode only can be numbers">
															</div>


															<button type="submit" name="update"
																class="btn-upper btn btn-primary checkout-page-button">Update</button>
														</form>
													<?php } ?>
												</div>
												<!-- already-registered-login -->
											</div>
										</div>
										<!-- panel-body  -->
									</div><!-- row -->
								</div>
							</div><!-- /.checkout-steps -->
						</div>
						<?php include('includes/myaccount-sidebar.php'); ?>
					</div><!-- /.row -->
				</div><!-- /.checkout-box -->
				<?php include('includes/brands-slider.php'); ?>

			</div>
		</div>
		<?php include('includes/footer.php'); ?>
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