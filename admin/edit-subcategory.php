<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	date_default_timezone_set('Asia/Kuala_Lumpur');// change according timezone
	$currentTime = date('d-m-Y h:i:s A', time());


	if (isset($_POST['submit'])) {
		$category = $_POST['category'];
		$status = $_POST['SubStatus'];
		$subcat = $_POST['subcategory'];
		$id = intval($_GET['id']);

		// Check if the category is inactive
		$categoryQuery = mysqli_query($con, "SELECT Status FROM category WHERE id='$category'");
		$categoryResult = mysqli_fetch_array($categoryQuery);
		$categoryStatus = $categoryResult['Status'];

		if ($categoryStatus == 'Inactive' && $status == 'Active') {
			$_SESSION['error'] = "Cannot set subcategory to active because the category is inactive.";
		} else {
			// Fetch the current subcategory name
			$currentSubcatQuery = mysqli_query($con, "SELECT subcategory FROM subcategory WHERE id='$id'");
			$currentSubcatResult = mysqli_fetch_array($currentSubcatQuery);
			$currentSubcat = $currentSubcatResult['subcategory'];

			if ($subcat != $currentSubcat) {
				$checkQuery = "SELECT * FROM subcategory WHERE subcategory='$subcat' AND id != $id";
				$checkResult = mysqli_query($con, $checkQuery);
				if (mysqli_num_rows($checkResult) > 0) {
					$_SESSION['error'] = "Subcategory name already exists. Update failed.";
				} else {
					if (!preg_match('/^[a-zA-Z ]+$/', $subcat)) {
						$_SESSION['error'] = "Subcategory name can only contain alphabets. Update failed.";
					} else {
						$sql = mysqli_query($con, "UPDATE subcategory SET categoryid='$category', subcategory='$subcat', updationDate='$currentTime', SubStatus='$status' WHERE id='$id'");
						$_SESSION['msg'] = "Sub-Category Updated !!";
					}
				}
			} else {
				$sql = mysqli_query($con, "UPDATE subcategory SET categoryid='$category', updationDate='$currentTime', SubStatus='$status' WHERE id='$id'");
				$_SESSION['msg'] = "Sub-Category Updated !!";
			}
		}
	}
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Admin | Edit SubCategory</title>
		<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link type="text/css" href="css/theme.css" rel="stylesheet">
		<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
		<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
			rel='stylesheet'>
		<link rel="shortcut icon" href="images/favicon.ico">
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
									<h3>Edit SubCategory</h3>
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
												<strong>Well done!</strong> <?php echo htmlentities($_SESSION['msg']); ?>
												<?php echo htmlentities($_SESSION['msg'] = ""); ?>
											</div>
										<?php }
									} ?>

									<br />

									<form class="form-horizontal row-fluid" name="Category" method="post">
										<?php
										$id = intval($_GET['id']);
										$query = mysqli_query($con, "select category.id,category.categoryName,subcategory.subcategory,subcategory.SubStatus from subcategory join category on category.id=subcategory.categoryid where subcategory.id='$id'");
										while ($row = mysqli_fetch_array($query)) {
											?>

											<div class="control-group">
												<label class="control-label" for="basicinput">Category</label>
												<div class="controls">
													<select name="category" class="span8 tip" required>
														<option value="<?php echo htmlentities($row['id']); ?>">
															<?php echo htmlentities($catname = $row['categoryName']); ?></option>
														<?php $ret = mysqli_query($con, "select * from category");
														while ($result = mysqli_fetch_array($ret)) {
															echo $cat = $result['categoryName'];
															if ($catname == $cat) {
																continue;
															} else {
																?>
																<option value="<?php echo $result['id']; ?>">
																	<?php echo $result['categoryName']; ?></option>
															<?php }
														} ?>
													</select>
												</div>
											</div>

											<div class="control-group">
												<label class="control-label" for="basicinput">SubCategory Name</label>
												<div class="controls">
													<input type="text" placeholder="Enter category Name" name="subcategory"
														value="<?php echo htmlentities($row['subcategory']); ?>"
														class="span8 tip" required>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="basicinput">SubCategory Status</label>
												<div class="controls">
													<select name="SubStatus" class="span8 tip" required>
														<?php echo htmlentities($row['SubStatus']); ?>
														<option value="Active" <?php if ($row['SubStatus'] == 'Active')
															echo 'selected'; ?>>Active</option>
														<option value="Inactive" <?php if ($row['SubStatus'] == 'Inactive')
															echo 'selected'; ?>>Inactive</option>
													</select>
												</div>
											</div>

										<?php } ?>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn">Update</button>
											</div>
										</div>
									</form>
								</div>
							</div>

						</div><!--/.content-->
					</div><!--/.span9-->
				</div>
			</div><!--/.container-->
		</div><!--/.wrapper-->

		<?php include ('include/footer.php'); ?>

		<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
		<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
		<script src="scripts/datatables/jquery.dataTables.js"></script>
		<script>
			$(document).ready(function () {
				$('.datatable-1').dataTable();
				$('.dataTables_paginate').addClass("btn-group datatable-pagination");
				$('.dataTables_paginate > a').wrapInner('<span />');
				$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
				$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
			});
		</script>
	</body>
<?php } ?>
