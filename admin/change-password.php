<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	date_default_timezone_set('Asia/Kuala_Lumpur'); // change according timezone
	$currentTime = date('d-m-Y h:i:s A', time());

	if (isset($_POST['submit'])) {
		$sql = mysqli_query($con, "SELECT password FROM admin where password='" . md5($_POST['password']) . "' && username='" . $_SESSION['alogin'] . "'");
		$num = mysqli_fetch_array($sql);
		if ($num > 0) {
			$newPassword = md5($_POST['newpassword']);
			if ($newPassword == $num['password']) {
				$_SESSION['msg'] = "New Password should not be the same as the Current Password !!";
			} else {
				$con = mysqli_query($con, "update admin set password='" . md5($_POST['newpassword']) . "', updationDate='$currentTime' where username='" . $_SESSION['alogin'] . "'");
				$_SESSION['msg'] = "Password Changed Successfully !!";
			}
		} else {
			$_SESSION['error'] = "Old Password not match !!";
		}
	}
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Admin | Change Password</title>
		<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link type="text/css" href="css/theme.css" rel="stylesheet">
		<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
		<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
			rel='stylesheet'>
		<link rel="shortcut icon" href="images/favicon.ico">
		<script type="text/javascript">
			function valid() {
				var currentPassword = document.chngpwd.password.value;
				var newPassword = document.chngpwd.newpassword.value;
				var confirmPassword = document.chngpwd.confirmpassword.value;
				var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

				if (currentPassword == "") {
					alert("Current Password Field is Empty !!");
					document.chngpwd.password.focus();
					return false;
				} else if (newPassword == "") {
					alert("New Password Field is Empty !!");
					document.chngpwd.newpassword.focus();
					return false;
				} else if (!passwordPattern.test(newPassword)) {
					alert("New Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.");
					document.chngpwd.newpassword.focus();
					return false;
				} else if (newPassword == currentPassword) {
					alert("New Password should not be the same as the Current Password !!");
					document.chngpwd.newpassword.focus();
					return false;
				} else if (confirmPassword == "") {
					alert("Confirm Password Field is Empty !!");
					document.chngpwd.confirmpassword.focus();
					return false;
				} else if (newPassword != confirmPassword) {
					alert("Password and Confirm Password Field do not match !!");
					document.chngpwd.confirmpassword.focus();
					return false;
				}
				return true;
			}

			function togglePasswordVisibility() {
				var passwordFields = document.querySelectorAll('.password-field');
				passwordFields.forEach(field => {
					if (field.type === 'password') {
						field.type = 'text';
					} else {
						field.type = 'password';
					}
				});
			}
		</script>
		<style>
			.profile-container {
				display: grid;
				grid-template-columns: 0.3fr 1.7fr;
				grid-template-rows: 1fr 1fr 1fr;
				grid-auto-columns: 1fr;
				gap: 0px 0px;
				grid-auto-flow: row;
				grid-template-areas:
					"profile_img profile_name"
					"profile_img profile_email"
					"profile_img profile_role";
			}

			.profile_img {
				grid-area: profile_img;
			}

			.profile_name {
				grid-area: profile_name;
			}

			.profile_email {
				grid-area: profile_email;
			}

			.profile_role {
				grid-area: profile_role;
			}
		</style>
	</head>

	<body>
		<?php include ('include/header.php'); ?>

		<div class="wrapper">
			<div class="container">
				<div class="row">
					<?php include ('include/sidebar.php'); ?>
					<div class="span9">
						<div class="content">

							<div class="module">
								<div class="module-head">
									<h3>Admin Profile</h3>
								</div>
								<div class="module-body">

									<?php
									$admin = mysqli_query($con, "SELECT * FROM ADMIN WHERE username = '" . $_SESSION['alogin'] . "';");
									$admin_info = mysqli_fetch_array($admin);
									?>

									<div class="profile-container"
										style="border:0px solid #ccc;border-radius:8px;padding:15px;">
										<div class="profile_img" style="margin-left:auto;margin-right:auto;"><img
												src="images/user.png" alt="Profile img"
												style="height:80px;width:80px;border-radius:50%;"></div>
										<div class="profile_name"
											style="color:black; font-weight: bold;font-size:25px;display:flex;justify-content:left;align-items: center;">
											<?php echo $admin_info['username']; ?></div>
										<div class="profile_email"
											style="color:grey; font-size:12px;display:flex;justify-content:left;align-items: center;">
											<?php echo $admin_info['email']; ?></div>
										<div class="profile_role"
											style="color:grey; font-size:12px;display:flex;justify-content:left;align-items: center;">
											<?php echo $admin_info['role']; ?></div>
									</div>

								</div>
							</div>

						</div>
						<!--/.content-->
						<div class="content">

							<div class="module">
								<div class="module-head">
									<h3>Change Password</h3>
								</div>
								<div class="module-body">

									<?php if (isset($_POST['submit'])) { ?>
										<?php if (isset($_SESSION['error'])) { ?>
											<div class="alert alert-danger">
												<button type="button" class="close" data-dismiss="alert">×</button>
												<strong>Error!</strong> <?php echo htmlentities($_SESSION['error']); ?>
											</div>
											<?php unset($_SESSION['error']);
										} else { ?>
											<div class="alert alert-success">
												<button type="button" class="close" data-dismiss="alert">×</button>
												<?php echo htmlentities($_SESSION['msg']); ?>
												<?php echo htmlentities($_SESSION['msg'] = ""); ?>
											</div>
										<?php } ?>
									<?php } ?>
									<br />

									<form class="form-horizontal row-fluid" name="chngpwd" method="post"
										onSubmit="return valid();">

										<div class="control-group">
											<label class="control-label" for="basicinput">Current Password</label>
											<div class="controls">
												<input type="password" placeholder="Enter your current Password"
													name="password" class="span8 tip password-field" required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">New Password</label>
											<div class="controls">

												<input type="password" placeholder="Enter your new Password"
													name="newpassword" class="span8 tip password-field" required>

											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Confirm New Password</label>
											<div class="controls">
												<input type="password" placeholder="Enter your new Password again"
													name="confirmpassword" class="span8 tip password-field" required>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<label class="checkbox">
													<input type="checkbox" onclick="togglePasswordVisibility()"> Show
													Password
												</label>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn">Submit</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--/.content-->
					</div>
					<!--/.span9-->
				</div>
			</div>
			<!--/.container-->
		</div>
		<!--/.wrapper-->

		<?php include ('include/footer.php'); ?>

		<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
		<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
	</body>
<?php } ?>
