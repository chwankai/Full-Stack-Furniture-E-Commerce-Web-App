<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');

$find_keyword = isset($_GET['key']) ? $_GET['key'] : (isset($_POST['product']) ? "{$_POST['product']}" : '');

$find = '%' . $find_keyword . '%';

// Capture the 'sort' parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Set the $sortkeyword based on the captured parameter
switch ($sort) {
	case 'latest':
		$sortkeyword = "ORDER BY id DESC";
		$sortcsskeyword1 = "background-color: orange;color: white;";
		break;
	case 'price_asc':
		$sortkeyword = "ORDER BY productPrice ASC";
		$sortcsskeyword2 = "background-color: orange;color: white;";
		break;
	case 'price_desc':
		$sortkeyword = "ORDER BY productPrice DESC";
		$sortcsskeyword3 = "background-color: orange;color: white;";
		break;
	case 'name_asc':
		$sortkeyword = "ORDER BY productName ASC";
		$sortcsskeyword4 = "background-color: orange;color: white;";
		break;
	case 'name_desc':
		$sortkeyword = "ORDER BY productName DESC";
		$sortcsskeyword5 = "background-color: orange;color: white;";
		break;
	default:
		$sortkeyword = "ORDER BY id DESC";
}

if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id']);
	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity']++;
		echo "<script>alert('Product has been added to the cart.')</script>";
		echo "<script type='text/javascript'> document.location ='search-result.php?key=" . urlencode($find_keyword) . "&sort=" . urlencode($sort) . "'; </script>";
	} else {
		$sql_p = "SELECT * FROM products WHERE id={$id}";
		$query_p = mysqli_query($con, $sql_p);
		if (mysqli_num_rows($query_p) != 0) {
			$row_p = mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['productPrice']);
			echo "<script>alert('Product has been added to the cart.')</script>";
			echo "<script type='text/javascript'> document.location ='search-result.php?key=" . urlencode($find_keyword) . "&sort=" . urlencode($sort) . "'; </script>";

		} else {
			$message = "Product ID is invalid";
		}
	}
}

// COde for Wishlist
if (isset($_GET['pid']) && $_GET['action'] == "wishlist") {
	if (strlen($_SESSION['login']) == 0) {
		header('location:login.php');
	} else {
		mysqli_query($con, "insert into wishlist(userId,productId) values('" . $_SESSION['id'] . "','" . $_GET['pid'] . "')");
		echo "<script>alert('Product added in wishlist.');</script>";
		echo "<script type='text/javascript'> document.location ='my-wishlist.php'; </script>";

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

	<title>Product Category</title>

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
	<link rel="stylesheet" href="assets/css/sort.css">
	<link rel="stylesheet" href="assets/css/config.css">

	<!-- Icons/Glyphs -->
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">

	<!-- Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">

	<!-- HTML5 elements and media queries Support for IE8 : HTML5 shim and Respond.js -->
	<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

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
	</div><!-- /.breadcrumb -->
	<div class="body-content outer-top-xs">
		<div class='container'>
			<div class='row outer-bottom-sm'>
				<div class='col-md-3 sidebar'>
					<!-- ================================== TOP NAVIGATION ================================== -->
					<div class="side-menu animate-dropdown outer-bottom-xs">
						<div class="side-menu animate-dropdown outer-bottom-xs">
							<div class="head"><i class="icon fa fa-align-justify fa-fw"></i>Sub Categories</div>
							<nav class="yamm megamenu-horizontal" role="navigation">

								<ul class="nav">
									<li class="dropdown menu-item">
										<?php $sql = mysqli_query($con, "select id,subcategory from subcategory where SubStatus = 'Active'");

										while ($row = mysqli_fetch_array($sql)) {
											?>
											<a href="sub-category.php?scid=<?php echo $row['id']; ?>"
												class="dropdown-toggle"><i class="icon fa fa-solid fa-couch"></i>
												<?php echo $row['subcategory']; ?></a>
										<?php } ?>
									</li>
								</ul>
							</nav>
						</div>
					</div><!-- /.side-menu -->
					<!-- ================================== TOP NAVIGATION : END ================================== -->
					<div class="sidebar-module-container">
						<h3 class="section-title">shop by</h3>
						<div class="sidebar-filter">
							<!-- ============================================== SIDEBAR CATEGORY ============================================== -->
							<div class="sidebar-widget wow fadeInUp outer-bottom-xs ">
								<div class="widget-header m-t-20">
									<h4 class="widget-title">Category</h4>
								</div>
								<div class="sidebar-widget-body m-t-10">
									<?php $sql = mysqli_query($con, "select id,categoryName from category where Status = 'Active'");
									while ($row = mysqli_fetch_array($sql)) {
										?>
										<div class="accordion">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a href="category.php?cid=<?php echo $row['id']; ?>"
														class="accordion-toggle collapsed">
														<?php echo $row['categoryName']; ?>
													</a>
												</div>
											</div>
										</div>
									<?php } ?>
								</div><!-- /.sidebar-widget-body -->
							</div><!-- /.sidebar-widget -->
							<!-- ============================================== COLOR: END ============================================== -->
						</div><!-- /.sidebar-filter -->
					</div><!-- /.sidebar-module-container -->
				</div><!-- /.sidebar -->
				<div class='col-md-9'>
					<!-- ========================================== SECTION – HERO ========================================= -->

					<div id="category" class="category-carousel hidden-xs">
						<div class="item">
							<div class="image">
								<img src="assets/images/banners/cat-banner-3.jpg" alt="" class="img-responsive">
							</div>
							<div class="container-fluid">
								<div class="caption vertical-top text-left">
									<div class="big-text">
										<br />
									</div>

									<div class="excerpt hidden-sm hidden-md"
										style="font-family:'Trebuchet MS';color:#d53c2a;width:650px;text-align:center;">
										"<?php echo $find_keyword; ?>"
									</div>
								</div><!-- /.caption -->
							</div><!-- /.container-fluid -->
						</div>
					</div>
					<div class="sort">
						<div>Sort by: </div>
						<div class="sort_button_div">
							<button class="sort_button" style="<?php echo $sortcsskeyword1; ?>"
								onclick="window.location.href='?key=<?php echo $find_keyword; ?>&sort=latest'">Latest</button>
							<button class="sort_button" style="<?php echo $sortcsskeyword2; ?>"
								onclick="window.location.href='?key=<?php echo $find_keyword; ?>&sort=price_asc'">Lowest
								to Highest Price</button>
							<button class="sort_button" style="<?php echo $sortcsskeyword3; ?>"
								onclick="window.location.href='?key=<?php echo $find_keyword; ?>&sort=price_desc'">Highest
								to Lowest Price</button>
							<button class="sort_button" style="<?php echo $sortcsskeyword4; ?>"
								onclick="window.location.href='?key=<?php echo $find_keyword; ?>&sort=name_asc'">Name
								from A->Z</button>
							<button class="sort_button" style="<?php echo $sortcsskeyword5; ?>"
								onclick="window.location.href='?key=<?php echo $find_keyword; ?>&sort=name_desc'">Name
								from Z->A</button>
						</div>
					</div>
					<div class="search-result-container">
						<div id="myTabContent" class="tab-content">
							<div class="tab-pane active " id="grid-container">
								<div class="category-product  inner-top-vs">
									<div class="row">
										<?php
										$ret = mysqli_query($con, "select * from products where productName like '$find' AND productAvailability = 'In Stock' $sortkeyword");
										$num = mysqli_num_rows($ret);
										if ($num > 0) {
											while ($row = mysqli_fetch_array($ret)) { ?>
												<div class="col-sm-6 col-md-4 wow fadeInUp">
													<div class="products">
														<div class="product">
															<div class="product-image">
																<div class="image">
																	<a
																		href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><img
																			src="assets/images/blank.gif"
																			data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			alt="" width="262px" height="262px"></a>
																</div><!-- /.image -->
															</div><!-- /.product-image -->


															<div class="product-info text-left">
																<h3 class="name"><a
																		href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><?php echo htmlentities($row['productName']); ?></a>
																</h3>
																<div class="description"></div>

																<div class="product-price">
																	<span class="price">
																		RM <?php echo htmlentities($row['productPrice']); ?>
																	</span>
																	<span class="price-before-discount">RM
																		<?php echo htmlentities($row['productPriceBeforeDiscount']); ?></span>

																</div><!-- /.product-price -->

															</div><!-- /.product-info -->
															<div class="cart clearfix animate-effect">
																<div class="action">
																	<ul class="list-unstyled">
																		<li class="add-cart-button btn-group">
																			<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
																				<a
																					href="search-result.php?page=product&action=add&id=<?php echo $row['id']; ?>&key=<?php echo $find_keyword; ?>&sort=<?php echo $sort; ?>">
																					<button class="btn btn-primary" type="button"><i
																							class="fa fa-shopping-cart"></i> Add to
																						cart</button></a>
																			<?php } else { ?>
																				<div class="action" style="color:red">Out of Stock
																				</div>
																			<?php } ?>

																		</li>

																		<li class="lnk wishlist">
																			<a class="add-to-cart"
																				href="search-result.php?pid=<?php echo htmlentities($row['id']) ?>&&action=wishlist"
																				title="Wishlist">
																				<i class="icon fa fa-heart"></i>
																			</a>
																		</li>
																	</ul>
																</div><!-- /.action -->
															</div><!-- /.cart -->
														</div>
													</div>
												</div>
											<?php }
										} else { ?>

											<div class="col-sm-6 col-md-4 wow fadeInUp">
												<h3>No Product Found</h3>
											</div>

										<?php } ?>
									</div><!-- /.row -->
								</div><!-- /.category-product -->
							</div><!-- /.tab-pane -->
						</div><!-- /.search-result-container -->
					</div><!-- /.col -->
				</div>
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
