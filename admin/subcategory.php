<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {


	if (isset($_POST['submit'])) {
		$category = $_POST['category'];
		$subcat = $_POST['subcategory'];
		$checkQuery = "SELECT id FROM subcategory WHERE subcategory = '$subcat'";
		$checkResult = mysqli_query($con, $checkQuery);
		if (mysqli_num_rows($checkResult) > 0) {
			$_SESSION['error'] = "Category name already exists. Created failed.";
		} else {
			// Check if the category name contains only alphabets
			if (!preg_match('/^[a-zA-Z ]+$/', $subcat)) {
				$_SESSION['error'] = "Subcategory name can only contain alphabets. Created failed.";
			} else {
				$sql = mysqli_query($con, "insert into subcategory(categoryid,subcategory) values('$category','$subcat')");
				$_SESSION['msg'] = "SubCategory Created !!";
			}

		}
	}

	function hasProducts($subcategoryId)
	{
		global $con; // Use the global database connection
		$checkProducts = "SELECT id FROM products WHERE subCategory = $subcategoryId";
		$result = mysqli_query($con, $checkProducts);
		return mysqli_num_rows($result) > 0; // Return true if there are products, false otherwise
	}

	if (isset($_GET['del'])) {
		$subcategoryId = intval($_GET['id']);
		if (hasProducts($subcategoryId)) {
			$_SESSION['delmsg'] = "Subcategory cannot be deleted. Delete all products under this subcategory first.";
		} else {


			mysqli_query($con, "delete from subcategory where id = '" . $_GET['id'] . "'");
			$_SESSION['delmsg'] = "SubCategory deleted !!";
		}
	}
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Admin | SubCategory</title>
		<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link type="text/css" href="css/theme.css" rel="stylesheet">
		<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
			rel='stylesheet'>
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
									<h3>Add New Sub Category</h3>
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


									<?php if (isset($_GET['del'])) { ?>
										<div class="alert alert-error">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong>Oh snap!</strong>
											<?php echo htmlentities($_SESSION['delmsg']); ?>		<?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
										</div>
									<?php } ?>

									<br />

									<form class="form-horizontal row-fluid" name="subcategory" method="post">

										<div class="control-group">
											<label class="control-label" for="basicinput">Category</label>
											<div class="controls">
												<select name="category" class="span8 tip" required>
													<option value="">Select Category</option>
													<?php $query = mysqli_query($con, "select * from category WHERE Status='Active'");
													while ($row = mysqli_fetch_array($query)) { ?>

														<option value="<?php echo $row['id']; ?>">
															<?php echo $row['categoryName']; ?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">SubCategory Name</label>
											<div class="controls">
												<input type="text" placeholder="Enter SubCategory Name" name="subcategory"
													class="span8 tip" required>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn">Create</button>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="module">
								<div class="module-head">
									<h3>Sub Category</h3>
								</div>
								<div class="module-body table">
									<table cellpadding="0" cellspacing="0" border="0"
										class="datatable-1 table table-bordered table-striped	 display" width="100%">
										<thead>
											<tr>
												<th>ID#</th>
												<th>Category</th>
												<th>Description</th>
												<th>Creation date</th>
												<th>Last Updated</th>
												<th>Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>

											<?php $query = mysqli_query($con, "select subcategory.id,category.categoryName,subcategory.subcategory,subcategory.creationDate,subcategory.updationDate,IF(category.Status='Inactive', 'Inactive', IF(category.Status='Active', subcategory.SubStatus, 'Active')) AS SubStatus
											  from subcategory join category on category.id=subcategory.categoryid");
											$cnt = 1;
											while ($row = mysqli_fetch_array($query)) {
												?>
												<tr>
													<td><?php echo htmlentities($row['id']); ?></td>
													<td><?php echo htmlentities($row['categoryName']); ?></td>
													<td><?php echo htmlentities($row['subcategory']); ?></td>
													<td> <?php echo htmlentities($row['creationDate']); ?></td>
													<td><?php echo htmlentities($row['updationDate']); ?></td>
													<td><?php echo htmlentities($row['SubStatus']); ?></td>
													<td>
														<a href="edit-subcategory.php?id=<?php echo $row['id'] ?>"><i
																class="icon-edit"></i></a>
														<a href="subcategory.php?id=<?php echo $row['id'] ?>&del=delete"
															onClick="return confirm('Are you sure you want to delete?')"><i
																class="icon-remove-sign"></i></a>
													</td>
												</tr>
												<?php $cnt = $cnt + 1;
											} ?>

									</table>
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
				// $('.datatable-1').dataTable();
				$('.datatable-1').dataTable({
					"aaSorting": [[0, "desc"]], // Change the 0 to the column index you want to sort by default
				});
				$('.dataTables_paginate').addClass("btn-group datatable-pagination");
				$('.dataTables_paginate > a').wrapInner('<span />');
				$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
				$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
			});
		</script>
	</body>
<?php } ?>
