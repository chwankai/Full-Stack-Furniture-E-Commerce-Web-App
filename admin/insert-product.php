<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	if (isset($_POST['submit'])) {
		$category = $_POST['category'];
		$subcat = $_POST['subcategory'];
		$productname = $_POST['productName'];
		$productcompany = $_POST['productCompany'];
		$productprice = $_POST['productprice'];
		$productpricebd = $_POST['productpricebd'];
		$productdescription = $_POST['productDescription'];
		$productavailability = $_POST['productAvailability'];
		$quantity = $_POST['Quantity'];
		
		$allowed_types = array('image/jpg', 'image/jpeg', 'image/png');

		// File names
		$productimage1 = isset($_POST["storedimage1"]) ? $_POST["storedimage1"] : $_FILES["productimage1"]["name"];
		$productimage2 = isset($_POST["storedimage2"]) ? $_POST["storedimage2"] : $_FILES["productimage2"]["name"];
		$productimage3 = isset($_POST["storedimage3"]) ? $_POST["storedimage3"] : $_FILES["productimage3"]["name"];

		//validation file type
		$check_image1 = isset($_FILES["productimage1"]) && in_array($_FILES["productimage1"]["type"], $allowed_types);
        $check_image2 = isset($_FILES["productimage2"]) && in_array($_FILES["productimage2"]["type"], $allowed_types);
        $check_image3 = isset($_FILES["productimage3"]) && in_array($_FILES["productimage3"]["type"], $allowed_types);
		if (!$check_image1 || !$check_image2 || !$check_image3) {
            $_SESSION['error'] = "Only jpg/jpeg and png files are allowed!";
        }else{
		// check if product name already exists
		$checkQuery = mysqli_query($con, "SELECT id FROM products WHERE productName = '$productname'");
		if (mysqli_num_rows($checkQuery) > 0) {
			$_SESSION['error'] = "Product name already exists. Please choose a different name.";
		} else {
			// Validate product name
			if (!preg_match('/^[a-zA-Z ]+$/', $productname)) {
				$_SESSION['error'] = "Product name can only contain alphabets and spaces.";
			} else {
				// Validate prices
				if (
					!is_numeric($productprice) || $productprice < 0 ||
					!is_numeric($productpricebd) || $productpricebd < 0 ||
					!is_numeric($quantity) || $quantity < 0
				) {
					$_SESSION['error'] = "Product price, price before discount, and quantity must be non-negative numeric values.";
				} else {
					// Get the next product ID
					$query = mysqli_query($con, "SELECT MAX(id) AS pid FROM products");
					$result = mysqli_fetch_array($query);
					$productid = $result['pid'] + 1;
					$dir = "./productimages/$productid";

					if (!is_dir($dir)) {
						mkdir($dir);
					}

					if (!empty($_FILES["productimage1"]["name"])) {
						move_uploaded_file($_FILES["productimage1"]["tmp_name"], "$dir/" . $_FILES["productimage1"]["name"]);
					}
					if (!empty($_FILES["productimage2"]["name"])) {
						move_uploaded_file($_FILES["productimage2"]["tmp_name"], "$dir/" . $_FILES["productimage2"]["name"]);
					}
					if (!empty($_FILES["productimage3"]["name"])) {
						move_uploaded_file($_FILES["productimage3"]["tmp_name"], "$dir/" . $_FILES["productimage3"]["name"]);
					}

					// Insert product details into the database
					$sql = mysqli_query($con, "INSERT INTO products(category, subCategory, productName, productCompany, productPrice, productDescription, productAvailability,Quantity, productImage1, productImage2, productImage3, productPriceBeforeDiscount) VALUES('$category', '$subcat', '$productname', '$productcompany', '$productprice', '$productdescription',  '$productavailability', '$quantity','$productimage1', '$productimage2', '$productimage3', '$productpricebd')");
					$_SESSION['msg'] = "Product Inserted Successfully !!";
				}
			}
		}
	}
	}
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Admin | Insert Product</title>
		<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link type="text/css" href="css/theme.css" rel="stylesheet">
		<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
			rel='stylesheet'>
		<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

		<script>
			function getSubcat(val) {
				$.ajax({
					type: "POST",
					url: "get_subcat.php",
					data: 'cat_id=' + val,
					success: function (data) {
						$("#subcategory").html(data);
					}
				});
			}

			function selectCountry(val) {
				$("#search-box").val(val);
				$("#suggesstion-box").hide();
			}

			function validateFileType(input) {
				var fileName = input.value;
				var idxDot = fileName.lastIndexOf(".") + 1;
				var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
				if (extFile == "jpg" || extFile == "jpeg" || extFile == "png") {
					// Valid file extension
				} else {
					alert("Only jpg/jpeg and png files are allowed!");
					input.value = ""; // Clear the input
				}
			}
			// Store selected file names to hidden inputs
			function handleFileSelect(input) {
				var inputId = input.id;
				var storedInputId = "stored" + inputId;
				var storedInput = document.getElementById(storedInputId);
				storedInput.value = input.files[0].name;
				validateFileType(input);
			}

			// Restore file names on page load
			document.addEventListener("DOMContentLoaded", function () {
				var inputs = document.querySelectorAll('input[type="file"]');
				inputs.forEach(function (input) {
					handleFileSelect(this);
				});
			});

			// Form validation
			function validateForm1() {
				var productname = document.forms["insertproduct"]["productName"].value;
				var productprice = document.forms["insertproduct"]["productprice"].value;
				var productpricebd = document.forms["insertproduct"]["productpricebd"].value;
				var productimage1 = document.forms["insertproduct"]["storedimage1"].value;
				var productimage2 = document.forms["insertproduct"]["storedimage2"].value;
				var productimage3 = document.forms["insertproduct"]["storedimage3"].value;

				var errorMessages = [];

				if (!/^[a-zA-Z ]+$/.test(productname)) {
					errorMessages.push("Product name can only contain alphabets and spaces.");
				}
				if (!isNumeric(productprice) || productprice < 0) {
					errorMessages.push("Product price must be a non-negative numeric value.");
				}
				if (!isNumeric(productpricebd) || productpricebd < 0) {
					errorMessages.push("Product price before discount must be a non-negative numeric value.");
				}
				if (!isNumeric(quantity) || quantity < 0) {
					errorMessages.push("Quantity must be a non-negative numeric value.");
				}
				if (productimage1 === '') {
					errorMessages.push("Product Image1 is required.");
				}
				if (productimage2 === '') {
					errorMessages.push("Product Image2 is required.");
				}
				if (productimage3 === '') {
					errorMessages.push("Product Image3 is required.");
				}

				if (errorMessages.length > 0) {
					alert("Please fix the following errors:\n" + errorMessages.join("\n"));
					return false;
				}
				return true;
			}

			function isNumeric(value) {
				return !isNaN(value) && isFinite(value);
			}
		</script>
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
									<h3>Insert Product</h3>
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
										<?php } ?>
									<?php } ?>

									<?php if (isset($_GET['del'])) { ?>
										<div class="alert alert-error">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong>Oh snap!</strong>
											<?php echo htmlentities($_SESSION['delmsg']); ?>		<?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
										</div>
									<?php } ?>

									<br />

									<form class="form-horizontal row-fluid" name="insertproduct" method="post"
										enctype="multipart/form-data" onsubmit="return validateForm1()">

										<div class="control-group">
											<label class="control-label" for="basicinput">Category</label>
											<div class="controls">
												<select name="category" class="span8 tip" onChange="getSubcat(this.value);"
													required>
													<option value="">Select Category</option>
													<?php $query = mysqli_query($con, "select * from category");
													while ($row = mysqli_fetch_array($query)) { ?>
														<option value="<?php echo $row['id']; ?>">
															<?php echo $row['categoryName']; ?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Sub Category</label>
											<div class="controls">
												<select name="subcategory" id="subcategory" class="span8 tip" required>
													<option value="">Select Subcategory</option>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Product Name</label>
											<div class="controls">
												<input type="text" name="productName" placeholder="Enter Product Name"
													class="span8 tip"
													value="<?php echo isset($productname) ? $productname : ''; ?>" required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Product Company</label>
											<div class="controls">
												<input type="text" name="productCompany"
													placeholder="Enter Product Company Name" class="span8 tip"
													value="<?php echo isset($productcompany) ? $productcompany : ''; ?>"
													required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Product Price</label>
											<div class="controls">
												<input type="text" name="productprice" placeholder="Enter Product Price"
													class="span8 tip"
													value="<?php echo isset($productprice) ? $productprice : ''; ?>"
													required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Product Price Before
												Discount</label>
											<div class="controls">
												<input type="text" name="productpricebd"
													placeholder="Enter Product Price Before Discount" class="span8 tip"
													value="<?php echo isset($productpricebd) ? $productpricebd : ''; ?>"
													required>
											</div>
										</div>
										<div class="control-group">
											<label class="control-label" for="basicinput">Quantity</label>
											<div class="controls">
												<input type="text" name="Quantity" placeholder="Enter Product Quantity"
													class="span8 tip"
													value="<?php echo isset($quantity) ? $quantity : ''; ?>" required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Product Description</label>
											<div class="controls">
												<textarea name="productDescription" placeholder="Enter Product Description"
													rows="6"
													class="span8 tip"><?php echo isset($productdescription) ? $productdescription : ''; ?></textarea>
											</div>
										</div>


										<div class="control-group">
											<label class="control-label" for="basicinput">Product Availability</label>
											<div class="controls">
												<select name="productAvailability" id="productAvailability"
													class="span8 tip" required>
													<option selected value="In Stock" <?php if (isset($productavailability) && $productavailability == 'In Stock')
														echo 'selected'; ?>>Active
													</option>
													<option value="Out of Stock" <?php if (isset($productavailability) && $productavailability == 'Out of Stock')
														echo 'selected'; ?>>Inactive
													</option>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="productimage1">Product Image1</label>
											<div class="controls">
												<input type="file" name="productimage1" id="productimage1" class="span8 tip"
													required onchange="handleFileSelect(this)">
												<input type="hidden" name="storedimage1" id="storedproductimage1"
													value="<?php echo isset($productimage1) ? $productimage1 : ''; ?>">
												<?php if (isset($productimage1)) {
													echo '<p>Previously uploaded file: ' . $productimage1 . '</p>';
												} ?>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="productimage2">Product Image2</label>
											<div class="controls">
												<input type="file" name="productimage2" id="productimage2" class="span8 tip"
													required onchange="handleFileSelect(this)">
												<input type="hidden" name="storedimage2" id="storedproductimage2"
													value="<?php echo isset($productimage2) ? $productimage2 : ''; ?>">
												<?php if (isset($productimage2)) {
													echo '<p>Previously uploaded file: ' . $productimage2 . '</p>';
												} ?>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="productimage3">Product Image3</label>
											<div class="controls">
												<input type="file" name="productimage3" id="productimage3" class="span8 tip"
													onchange="handleFileSelect(this)">
												<input type="hidden" name="storedimage3" id="storedproductimage3"
													value="<?php echo isset($productimage3) ? $productimage3 : ''; ?>">
												<?php if (isset($productimage3)) {
													echo '<p>Previously uploaded file: ' . $productimage3 . '</p>';
												} ?>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn">Insert</button>
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

</html>
