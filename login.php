<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
// php Code  for user Registration
if (isset($_POST['submit'])) {
	$name = $_POST['fullname'];
	$email = $_POST['emailid'];
	$contactno = $_POST['contactno'];
	$password = md5($_POST['spassword']);
	$birthday = $_POST['birthday'];
	$query = mysqli_query($con, "insert into users(name,email,contactno,password,birthday,shippingReceiver,billingReceiver,shippingPhone,billingPhone) values('$name','$email','$contactno','$password','$birthday','$name','$name','$contactno','$contactno')");
	if ($query) {
		echo "<script>alert('Register successfully!');</script>";
	} else {
		echo "<script>alert('Not register something went worng');</script>";
	}
}
// Code for User login
if (isset($_POST['login'])) {
	$email = $_POST['email'];
	$password = md5($_POST['password']);
	$query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' and password='$password'");
	/*using mysqli_query function to select user data and compare the user entered detail when they signup ,$con = check database connection*/
	$num = mysqli_fetch_array($query);
	if ($num > 0) {
		$extra = "index.php";//after login it will load this page 
		session_regenerate_id(true);
		$_SESSION['login'] = $_POST['email'];
		$_SESSION['id'] = $num['id'];
		$_SESSION['username'] = $num['name'];
		$status = 1;
		$log = mysqli_query($con, "insert into userlog(userEmail,status) values('" . $_SESSION['login'] . "','$status')");
		$host = $_SERVER['HTTP_HOST'];
		$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("location:http://$host$uri/$extra");
		exit();
	} else {
		$extra = "login.php";
		$email = $_POST['email'];
		$status = 0;
		$log = mysqli_query($con, "insert into userlog(userEmail,status) values('$email','$status')");
		$host = $_SERVER['HTTP_HOST'];
		$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("location:http://$host$uri/$extra");
		$_SESSION['errmsg'] = "Invalid email id or Password";
		exit();
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

	<title>Online Furniture Store | Login | Signup</title>

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

	<!-- Icons/Glyphs -->
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/password-validation-message.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
		integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMFzG5a/DA5gW/9RTLC47a/2kIRH2W2L9l/qj3x" crossorigin="anonymous">
	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">

	<script type="text/javascript">
		function validatePassword() {
			var password = document.register.spassword.value;
			var confirmPassword = document.register.confirmpassword.value;

			// Check if passwords match
			if (password !== confirmPassword) {
				alert("Password and Confirm Password Field do not match!!");
				document.register.confirmpassword.focus();
				return false;
			}

			// Check password length
			if (password.length < 8) {
				alert("Password must be at least 8 characters long!!");
				document.register.spassword.focus();
				return false;
			}

			// Check password complexity
			var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
			if (!passwordRegex.test(password)) {
				alert("Password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character!!");
				document.register.spassword.focus();
				return false;
			}

			return true;
		}	
	</script>
	<script>
		function userAvailability() {
			$("#loaderIcon").show();
			jQuery.ajax({
				url: "check_availability.php",
				data: 'email=' + $("#email").val(),
				type: "POST",
				success: function (data) {
					$("#user-availability-status1").html(data);
					$("#loaderIcon").hide();
				},
				error: function () { }
			});
		}
		//view password for sign up
		document.addEventListener("DOMContentLoaded", function () {
			document.getElementById('togglePassword').addEventListener('click', function () {
				var passwordInput = document.getElementById('spassword');
				if (passwordInput.type == 'password') {
					passwordInput.type = 'text';
					this.textContent = 'Hide Password';
				} else {
					passwordInput.type = 'password';
					this.textContent = 'View Password';
				}
			});
		});
		//view password for login
		document.addEventListener("DOMContentLoaded", function () {
			document.getElementById('togglePasswordlogin').addEventListener('click', function () {
				var passwordInput = document.getElementById('password');
				if (passwordInput.type == 'password') {
					passwordInput.type = 'text';
					this.textContent = 'Hide Password';
				} else {
					passwordInput.type = 'password';
					this.textContent = 'View Password';
				}
			});
		});
	</script>



</head>

<body class="cnt-home">

	<!-- ============================================== HEADER ============================================== -->
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
					<li class='active'>Authentication</li>
				</ul>
			</div><!-- /.breadcrumb-inner -->
		</div><!-- /.container -->
	</div><!-- /.breadcrumb -->

	<div class="body-content outer-top-bd">
		<div class="container">
			<div class="sign-in-page inner-bottom-sm">
				<div class="row">
					<!-- Sign-in -->
					<div class="col-md-6 col-sm-6 sign-in">
						<h4 class="">Sign in</h4>
						<p class="">Hello, Welcome to your account.</p>
						<form class="register-form outer-top-xs" method="post">
							<span style="color:red;">
								<?php
								echo htmlentities($_SESSION['errmsg']);
								?>
								<?php
								echo htmlentities($_SESSION['errmsg'] = "");
								?>
							</span>
							<div class="form-group">
								<label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
								<input type="email" name="email" class="form-control unicase-form-control text-input"
									id="exampleInputEmail1">
							</div>
							<div class="form-group">
								<label class="info-title" for="exampleInputPassword1">Password <span>*</span></label>
								<input type="password" name="password"
									class="form-control unicase-form-control text-input" id="password">
								<button type="button" id="togglePasswordlogin"
									style="background-color: orange; color: #fff;margin-top:10px ;padding: 5px 12px; border: none; border-radius: 5px; cursor: pointer; font-size:12px;">Show
									Password</button>
							</div>
							<div class="radio outer-xs">
								<a href="forgot-password.php" class="forgot-password pull-right">Forgot your
									Password?</a>
							</div>
							<button type="submit" class="btn-upper btn btn-primary checkout-page-button"
								name="login">Login</button>
						</form>
					</div>
					<!-- Sign-in -->

					<!-- create a new account -->
					<div class="col-md-6 col-sm-6 create-new-account">
						<h4 class="checkout-subtitle">Create a new account</h4>
						<p class="text title-tag-line">Create your own Shopping account.</p>
						<form class="register-form outer-top-xs" role="form" method="post" name="register"
							onSubmit="return validatePassword();">
							<div class="form-group">
								<label class="info-title" for="fullname">Name <span>*</span></label>
								<input type="text" class="form-control unicase-form-control text-input" id="fullname"
									name="fullname" required="required" pattern="[a-zA-Z][a-zA-Z0-9\s]*"
									title="Name must start with letters (letters & numbers only)">
							</div>

							<div class="form-group">
								<label class="info-title" for="exampleInputEmail2">Email Address <span>*</span></label>
								<input type="email" class="form-control unicase-form-control text-input" id="email"
									onBlur="userAvailability()" name="emailid" required>
								<span id="user-availability-status1" style="font-size:12px;"></span>
							</div>

							<div class="form-group">
								<label class="info-title" for="contactno">Contact No. <span>*</span></label>
								<input type="text" class="form-control unicase-form-control text-input" id="contactno"
									name="contactno" minlength="10" maxlength="13" required value="+60"
									pattern="\+?[\d\s()-]*$">
							</div>
							<div class="form-group">
								<label class="info-title" for="birthday">Day of Birth<span>*</span></label>
								<input type="date" class="form-control unicase-form-control text-input" id="birthday"
									name="birthday">
								<span id="birthday-error"></span>
							</div>
							<div id="message">
								<h3>Password must contain the following:</h3>
								<p id="letter" class="invalid">At least <b>lowercase</b> letter</p>
								<p id="capital" class="invalid">At least <b>capital (uppercase)</b> letter</p>
								<p id="number" class="invalid">At least <b>number</b></p>
								<p id="length" class="invalid">Minimum <b>8 characters</b></p>
								<p id="special" class="invalid">At least <b>one special character</b></p>
							</div>
							<div class="form-group">
								<label class="info-title" for="password">Password. <span>*</span></label>
								<div class="password-container">
									<input type="password" class="form-control unicase-form-control text-input"
										id="spassword" name="spassword" required>
									<button type="button" id="togglePassword"
										style="background-color: orange; color: #fff;margin-top:10px ;padding: 5px 12px; border: none; border-radius: 5px; cursor: pointer;">Show
										Password</button>
								</div>
							</div>

							<div class="form-group">
								<label class="info-title" for="confirmpassword">Confirm Password. <span>*</span></label>
								<input type="password" class="form-control unicase-form-control text-input"
									id="confirmpassword" name="confirmpassword" required>
								<button type="button" class="view-password-btn" onclick="togglePasswordCnfpass()"
									style="background-color: orange; color: #fff;margin-top:10px ;padding: 5px 12px;  border: none; border-radius: 5px; cursor: pointer;">Show
									Password</button>
							</div>

							<button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button"
								id="submit">Sign Up</button>
						</form>
						<span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To : </span>
						<div class="checkbox">
							<label class="checkbox">
								- Speed your way through the checkout.
							</label>
							<label class="checkbox">
								- Track your orders easily.
							</label>
							<label class="checkbox">
								- Keep a record of all your purchases.
							</label>
						</div>
					</div>
					<!-- create a new account -->
				</div><!-- /.row -->
			</div>
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
	<script src="assets/js/password-validation-signup.js"></script>
	<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
	<script>//birthday validation
		const today = new Date();
		const minBirthday = new Date(today.getFullYear()).toISOString().split('T')[0];

		document.getElementById('birthday').setAttribute('max', today.toISOString().split('T')[0]);
		document.getElementById('birthday').setAttribute('min', minBirthday);

		const birthdayInput = document.getElementById('birthday');
		const birthdayError = document.getElementById('birthday-error');

		birthdayInput.addEventListener('input', () => {
			const inputDate = new Date(birthdayInput.value);
			if (inputDate.getFullYear() > 2006) {
				birthdayError.textContent = 'You must be at least 18 years old to register.';
				birthdayError.style.color = 'red';
				birthdayInput.setCustomValidity('Please Enter a valid Day of Birth.');
			} else if (inputDate.getFullYear() < 1900) {
				birthdayError.textContent = 'Invalid Day of Birth.';
				birthdayError.style.color = 'red';
				birthdayInput.setCustomValidity('Please Enter a valid Day of Birth.');
			} else {
				birthdayError.textContent = 'You are available to register';
				birthdayError.style.color = 'green';
				birthdayInput.setCustomValidity('');
			}
		});
	</script>
	<script>
		//phone number validation
		const phoneInput = document.getElementById('contactno');

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

		// Add an event listener for form submission
		const form = document.querySelector('form');
		form.addEventListener('submit', function (event) {
			// Check if the phone number is valid
			if (phoneInput.validity.customError) {
				// Phone number is invalid
				event.preventDefault();
			}
		});
	</script>
	<script>
		//view confirm password
		function togglePasswordCnfpass() {
			var passwordField = document.getElementById("confirmpassword");
			var passwordButton = document.querySelector(".view-password-btn");
			if (passwordField.type === "password") {
				passwordField.type = "text";
				passwordButton.textContent = "Hide Password";
			} else {
				passwordField.type = "password";
				passwordButton.textContent = "Show Password";
			}
		}
	</script>

</body>

</html>
