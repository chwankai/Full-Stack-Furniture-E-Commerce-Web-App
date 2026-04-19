<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
if (strlen($_SESSION['login']) == 0) {
	//user need to login first to see their wishlist
	echo '<script>alert("Please login first to see your wishlist.");window.location.href = "login.php";</script>';
} else {
	// Code forProduct deletion from  wishlist	
	$wid = intval($_GET['del']);
	if (isset($_GET['del'])) {
		$query = mysqli_query($con, "delete from wishlist where id='$wid'");
	}


	if (isset($_GET['action']) && $_GET['action'] == "add") {
		$id = intval($_GET['id']);
		$query = mysqli_query($con, "delete from wishlist where productId='$id'");
		if (isset($_SESSION['cart'][$id])) {
			$_SESSION['cart'][$id]['quantity']++;
		} else {
			$sql_p = "SELECT * FROM products WHERE id={$id}";
			$query_p = mysqli_query($con, $sql_p);
			if (mysqli_num_rows($query_p) != 0) {
				$row_p = mysqli_fetch_array($query_p);
				$_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['productPrice']);
				header('location:my-wishlist.php');
			} else {
				$message = "Product ID is invalid";
			}
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

		<title>My Wishlist</title>
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

		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">
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
						<li><a href="home.php">Home</a></li>
						<li class='active'>Wishlist</li>
					</ul>
				</div><!-- /.breadcrumb-inner -->
			</div><!-- /.container -->
		</div><!-- /.breadcrumb -->

		<div class="body-content outer-top-bd">
			<div class="container">
				<div class="my-wishlist-page">
					<div class="row">
						<div class="col-md-12 my-wishlist">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th colspan="4" style="padding-top:0px;padding-bottom:0px;">my wishlist</th>
										</tr>
										<tr>
											<th colspan="4" style="padding:0px;">
												<hr>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$ret = mysqli_query($con, "select products.productName as pname,products.productName as proid,products.productImage1 as pimage,products.productPrice as pprice, products.productPriceBeforeDiscount AS pricebd, wishlist.productId as pid,wishlist.id as wid from wishlist join products on products.id=wishlist.productId where wishlist.userId='" . $_SESSION['id'] . "'");
										$num = mysqli_num_rows($ret);
										if ($num > 0) {
											while ($row = mysqli_fetch_array($ret)) {

												?>

												<tr>
													<td class="col-md-2"><img
															src="admin/productimages/<?php echo htmlentities($row['pid']); ?>/<?php echo htmlentities($row['pimage']); ?>"
															alt="<?php echo htmlentities($row['pname']); ?>" width="160"
															height="160"></td>
													<td class="col-md-6">
														<div class="product-name"><a
																href="product-details.php?pid=<?php echo htmlentities($pd = $row['pid']); ?>"><?php echo htmlentities($row['pname']); ?></a>
														</div>

														<div class="price">RM
															<?php echo htmlentities($row['pprice']); ?>
															<span>RM <?php echo htmlentities($row['pricebd']); ?></span>
														</div>
													</td>
													<td class="col-md-2" style="text-align:center;">
														<a href="my-wishlist.php?page=product&action=add&id=<?php echo $row['pid']; ?>"
															class="btn-upper btn btn-primary">Add to cart</a>
													</td>
													<td class="col-md-2 close-btn" style="text-align:center;">
														<a href="my-wishlist.php?del=<?php echo htmlentities($row['wid']); ?>"
															onClick="return confirm('Are you sure you want to delete?')" class=""><i
																class="fa fa-times"></i></a>
													</td>
												</tr>
											<?php }
										} else { ?>
											<tr style="border:0px;">
												<td style="font-size: 18px; font-weight:bold; text-align:center">
													<h1>Your Wishlist is Empty.</h1>
												</td>
											</tr>
											<tr style="border:0px;">
												<td>
													<img src='assets/images/nothing-here.avif'
														style='display: block;margin-left: auto;margin-right: auto;width: 30%;'
														alt='nothing here image'>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div><!-- /.row -->
				</div><!-- /.sigin-in-->
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
