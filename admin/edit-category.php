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
		$Status = $_POST['Status'];
		$description = $_POST['description'];
		$id = intval($_GET['id']);

		// Check if the category name already exists
		$checkQuery = "SELECT id FROM category WHERE categoryName = '$category' AND id != $id";
		$checkResult = mysqli_query($con, $checkQuery);
		if (mysqli_num_rows($checkResult) > 0) {
			$_SESSION['error'] = "Category name already exists. Update failed.";
		} else {
			// Check if the category name contains only alphabets
			if (!preg_match('/^[a-zA-Z ]+$/', $category)) {
				$_SESSION['error'] = "Category name can only contain alphabets. Update failed.";
			} else {
				// Update the category if it doesn't already exist
				$sql = mysqli_query($con, "UPDATE category SET categoryName='$category', categoryDescription='$description', updationDate='$currentTime',Status='$Status' WHERE id='$id'");
				$_SESSION['msg'] = "Category Updated !!";


			}
		}
	}

	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Admin | Category</title>
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
									<h3>Edit Category</h3>
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
										$query = mysqli_query($con, "select * from category where id='$id'");
										while ($row = mysqli_fetch_array($query)) {
											?>
											<div class="control-group">
												<label class="control-label" for="basicinput">Category Name</label>
												<div class="controls">
													<input type="text" placeholder="Enter category Name" name="category"
														value="<?php echo htmlentities($row['categoryName']); ?>"
														class="span8 tip" required>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="basicinput">Category Status</label>
												<div class="controls">
													<select name="Status" class="span8 tip" required>
														<?php echo htmlentities($row['Status']); ?>
														<option value="Active" <?php if ($row['Status'] == 'Active')
															echo 'selected'; ?>>Active</option>
														<option value="Inactive" <?php if ($row['Status'] == 'Inactive')
															echo 'selected'; ?>>Inactive</option>
													</select>
												</div>
											</div>


											<div class="control-group">
												<label class="control-label" for="basicinput">Description</label>
												<div class="controls">
													<textarea class="span8" name="description"
														rows="5"><?php echo htmlentities($row['categoryDescription']); ?></textarea>
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
