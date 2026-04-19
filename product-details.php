<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id']);
	$quantity = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity'] += $quantity;
	} else {
		$sql_p = "SELECT * FROM products WHERE id={$id}";
		$query_p = mysqli_query($con, $sql_p);
		if (mysqli_num_rows($query_p) != 0) {
			$row_p = mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']] = array("quantity" => $quantity, "price" => $row_p['productPrice']);
			echo "<script>alert('Product has been added to the cart')</script>";
			echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
		} else {
			$message = "Product ID is invalid";
		}
	}
}

$pid = intval($_GET['pid']);
if (isset($_GET['pid']) && $_GET['action'] == "wishlist") {
	if (strlen($_SESSION['login']) == 0) {
		header('location:login.php');
	} else {
		mysqli_query($con, "insert into wishlist(userId,productId) values('" . $_SESSION['id'] . "','$pid')");
		echo "<script>alert('Product aaded in wishlist');</script>";
		header('location:my-wishlist.php');

	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keywords" content="MediaCenter, Template, eCommerce">
	<meta name="robots" content="all">
	<title>Product Details</title>
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
				<?php
				$ret = mysqli_query($con, "select category.categoryName as catname,subCategory.subcategory as subcatname,products.productName as pname from products join category on category.id=products.category join subcategory on subcategory.id=products.subCategory where products.id='$pid'");
				while ($rw = mysqli_fetch_array($ret)) {

					?>

					<ul class="list-inline list-unstyled">
						<li><a href="index.php">Home</a></li>
						<li><?php echo htmlentities($rw['catname']); ?></a></li>
						<li><?php echo htmlentities($rw['subcatname']); ?></li>
						<li class='active'><?php echo htmlentities($rw['pname']); ?></li>
					</ul>
				<?php } ?>
			</div><!-- /.breadcrumb-inner -->
		</div><!-- /.container -->
	</div><!-- /.breadcrumb -->
	<div class="body-content outer-top-xs">
		<div class='container'>
			<div class='row single-product outer-bottom-sm '>
				<div class='col-md-3 sidebar'>
					<div class="sidebar-module-container">

						<!-- ==============================================CATEGORY============================================== -->
						<div class="sidebar-widget outer-bottom-xs wow fadeInUp">
							<h3 class="section-title">Category</h3>
							<div class="sidebar-widget-body m-t-10">
								<div class="accordion">

									<?php $sql = mysqli_query($con, "select id,categoryName  from category");
									while ($row = mysqli_fetch_array($sql)) {
										?>
										<div class="accordion-group">
											<div class="accordion-heading">
												<a href="category.php?cid=<?php echo $row['id']; ?>"
													class="accordion-toggle collapsed">
													<?php echo $row['categoryName']; ?>
												</a>
											</div>

										</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<!-- ============================================== CATEGORY : END ============================================== -->
						<!-- ============================================== HOT DEALS ============================================== -->
						<div class="sidebar-widget hot-deals wow fadeInUp">
							<h3 class="section-title">hot deals</h3>
							<div class="owl-carousel sidebar-carousel custom-carousel owl-theme outer-top-xs">

								<?php
								$ret = mysqli_query($con, "select * from products WHERE productAvailability = 'In Stock' order by rand() limit 4 ");
								while ($rws = mysqli_fetch_array($ret)) {

									?>

									<div class="item">
										<div class="products">
											<div class="hot-deal-wrapper">
												<div class="image">
													<img style="display: block;margin-left: auto;margin-right: auto;width: auto;"
														src="admin/productimages/<?php echo htmlentities($rws['id']); ?>/<?php echo htmlentities($rws['productImage1']); ?>"
														width="200" height="200" alt="">
												</div>

											</div><!-- /.hot-deal-wrapper -->

											<div class="product-info text-left m-t-20">
												<h3 class="name"><a
														href="product-details.php?pid=<?php echo htmlentities($rws['id']); ?>"><?php echo htmlentities($rws['productName']); ?></a>
												</h3>

												<div class="product-price">
													<span class="price">
														RM <?php echo htmlentities($rws['productPrice']); ?>
													</span>

													<span class="price-before-discount">RM
														<?php echo htmlentities($rws['productPriceBeforeDiscount']); ?></span>

												</div><!-- /.product-price -->

											</div><!-- /.product-info -->

											<div class="cart clearfix animate-effect">
												<div class="action">

													<div class="add-cart-button btn-group">
														<?php if ($rws['productAvailability'] == "In Stock" && $rws['Quantity'] > 0) { ?>

															<a style="border-radius:4px !important;"
																href="category.php?page=product&action=add&id=<?php echo $rws['id']; ?>">
																<button class="btn btn-primary" type="button">Add to
																	cart</button></a>
														<?php } else { ?>
															<div class="action" style="color:red">Out of Stock</div>
														<?php } ?>

													</div>

												</div><!-- /.action -->
											</div><!-- /.cart -->
										</div>
									</div>
								<?php } ?>
							</div><!-- /.sidebar-widget -->
						</div>

						<!-- ============================================== COLOR: END ============================================== -->
					</div>
				</div><!-- /.sidebar -->
				<?php
				$ret = mysqli_query($con, "select * from products where id='$pid' AND productAvailability = 'In Stock'");
				$num = mysqli_num_rows($ret);
				if ($num > 0) {
					while ($row = mysqli_fetch_array($ret)) {
						$maxvalue = $row["Quantity"];
						?>

						<div class='col-md-9'>
							<div class="row  wow fadeInUp">
								<div class="col-xs-12 col-sm-6 col-md-5 gallery-holder">
									<div class="product-item-holder size-big single-product-gallery small-gallery">

										<div id="owl-single-product">

											<div class="single-product-gallery-item" id="slide1">
												<a data-lightbox="image-1"
													data-title="<?php echo htmlentities($row['productName']); ?>"
													href="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>">
													<img class="img-responsive" alt="" src="assets/images/blank.gif"
														data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
														width="370" height="350" />
												</a>
											</div><!-- /.single-product-gallery-item -->

											<div class="single-product-gallery-item" id="slide2">
												<a data-lightbox="image-1" data-title="Gallery"
													href="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage2']); ?>">
													<img class="img-responsive" alt="" src="assets/images/blank.gif"
														data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage2']); ?>" />
												</a>
											</div><!-- /.single-product-gallery-item -->

											<div class="single-product-gallery-item" id="slide3">
												<a data-lightbox="image-1" data-title="Gallery"
													href="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage3']); ?>">
													<img class="img-responsive" alt="" src="assets/images/blank.gif"
														data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage3']); ?>" />
												</a>
											</div>

										</div><!-- /.single-product-slider -->


										<div class="single-product-gallery-thumbs gallery-thumbs">

											<div id="owl-single-product-thumbnails">
												<div class="item">
													<a class="horizontal-thumb active" data-target="#owl-single-product"
														data-slide="1" href="#slide1">
														<img class="img-responsive" alt="" src="assets/images/blank.gif"
															data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>" />
													</a>
												</div>

												<div class="item">
													<a class="horizontal-thumb" data-target="#owl-single-product" data-slide="2"
														href="#slide2">
														<img class="img-responsive" width="85" alt=""
															src="assets/images/blank.gif"
															data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage2']); ?>" />
													</a>
												</div>
												<div class="item">

													<a class="horizontal-thumb" data-target="#owl-single-product" data-slide="3"
														href="#slide3">
														<img class="img-responsive" width="85" alt=""
															src="assets/images/blank.gif"
															data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage3']); ?>"
															height="200" />
													</a>
												</div>
											</div><!-- /#owl-single-product-thumbnails -->
										</div>
									</div>
								</div>

								<div class='col-sm-6 col-md-7 product-info-block'>
									<div class="product-info">
										<h1 class="name"><?php echo htmlentities($row['productName']); ?></h1>
										<br>
										<div class="stock-container info-container m-t-10">
											<div class="row">
												<div class="col-sm-3">
													<div class="stock-box">
														<span class="label">Availability :</span>
													</div>
												</div>
												<div class="col-sm-9">
													<div class="stock-box">
														<span
															class="value"><?php echo htmlentities($row['productAvailability']); ?></span>
													</div>
												</div>
											</div><!-- /.row -->
										</div>
										<div class="stock-container info-container m-t-10">
											<div class="row">
												<div class="col-sm-3">
													<div class="stock-box">
														<span class="label">Stock :</span>
													</div>
												</div>
												<div class="col-sm-9">
													<div class="stock-box">
														<span class="value"><?php echo htmlentities($row['Quantity']); ?></span>
													</div>
												</div>
											</div><!-- /.row -->
										</div>
										<div class="stock-container info-container m-t-10">
											<div class="row">
												<div class="col-sm-3">
													<div class="stock-box">
														<span class="label">Product Brand :</span>
													</div>
												</div>
												<div class="col-sm-9">
													<div class="stock-box">
														<span
															class="value"><?php echo htmlentities($row['productCompany']); ?></span>
													</div>
												</div>
											</div><!-- /.row -->
										</div>
										<div class="price-container info-container m-t-20">
											<div class="row">
												<div class="col-sm-6">
													<div class="price-box">
														<span class="price">RM
															<?php echo htmlentities($row['productPrice']); ?></span>
														<span class="price-strike">RM
															<?php echo htmlentities($row['productPriceBeforeDiscount']); ?></span>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="favorite-button m-t-10">
														<a class="btn btn-primary" data-toggle="tooltip" data-placement="right"
															title="Wishlist"
															href="product-details.php?pid=<?php echo htmlentities($row['id']) ?>&&action=wishlist">
															<i class="fa fa-heart"></i>
														</a>
													</div>
												</div>
											</div><!-- /.row -->
										</div><!-- /.price-container -->
										<div class="quantity-container info-container">
											<div class="row">
												<div class="col-sm-2">
													<span class="label">Qty :</span>
												</div>
												<div class="col-sm-2">
													<div class="cart-quantity">
														<div class="quant-input">
															<input type="number" style="padding:0px 10px !important;"
																id="quantity-input" value="1" min="1"
																max="<?php echo $maxvalue; ?>">

															<script>
																document.getElementById('quantity-input').addEventListener('input', function () {
																	let input = document.getElementById('quantity-input');
																	let max = parseInt(input.getAttribute('max'));
																	let min = parseInt(input.getAttribute('min'));
																	let value = parseInt(input.value);

																	if (value > max) {
																		input.value = max;
																	} else if (value < min) {
																		input.value = min;
																	}
																});
															</script>
														</div>
													</div>
												</div>
												<div class="col-sm-7">
													<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
														<button class="btn btn-primary"
															onclick="addToCart(<?php echo $row['id']; ?>)"><i
																class="fa fa-shopping-cart inner-right-vs"></i> ADD TO CART</button>
													<?php } else { ?>
														<div class="action" style="color:red">Out of Stock</div>
													<?php } ?>
												</div>
											</div><!-- /.row -->
										</div><!-- /.quantity-container -->
									</div><!-- /.product-info -->
								</div><!-- /.col-sm-7 -->
							</div><!-- /.row -->

							<div class="product-tabs inner-bottom-xs  wow fadeInUp">
								<div class="row">
									<div class="col-sm-3">
										<ul id="product-tabs" class="nav nav-tabs nav-tab-cell">
											<li class="active"><a data-toggle="tab" href="#description">DESCRIPTION</a></li>
											<li><a data-toggle="tab" href="#review">REVIEW</a></li>
										</ul><!-- /.nav-tabs #product-tabs -->
									</div>
									<div class="col-sm-9">
										<div class="tab-content">
											<div id="description" class="tab-pane in active">
												<div class="product-tab">
													<p class="text"><?php echo $row['productDescription']; ?></p>
												</div>
											</div><!-- /.tab-pane -->
											<div id="review" class="tab-pane">
												<div class="product-tab">
													<div class="product-reviews">
														<h4 class="title">Customer Reviews</h4>
														<?php
														// Ensure $pid is properly defined and sanitized
														if (isset($pid)) {
															// Execute the query
															$qry = mysqli_query($con, "SELECT * FROM productreviews WHERE productId='$pid'");

															// Check if there are results
															if (mysqli_num_rows($qry) > 0) {
																// Fetch and display reviews
																while ($rvw = mysqli_fetch_assoc($qry)) {
																	?>
																	<div class="reviews" style="border: solid 1px #000; padding-left: 2%;">
																		<div class="review">
																			<div class="author m-t-15">
																				<i class="fa fa-pencil-square-o"
																					style="color:#f39c12 !important;"></i>
																				<span class="name"><?php echo htmlentities($rvw['name']); ?>
																				</span>
																				<span class="date">
																					<i class="fa fa-calendar"></i>
																					<span>
																						<?php echo htmlentities($rvw['reviewDate']); ?>
																					</span>
																				</span>
																			</div>
																			<div class="review-title">
																				<span class="summary"
																					style="font-size:18px;font-weight:bold;"><?php echo htmlentities($rvw['summary']); ?></span>

																			</div>
																			<div class="text" style="margin-bottom:20px;">
																				"<?php echo htmlentities($rvw['review']); ?>"
																			</div>
																			<div class="text"><b>Quality :</b>
																				<?php echo htmlentities($rvw['quality']); ?> Star</div>
																			<div class="text"><b>Price :</b>
																				<?php echo htmlentities($rvw['price']); ?> Star</div>
																			<div class="text"><b>Value :</b>
																				<?php echo htmlentities($rvw['value']); ?> Star</div>

																		</div>
																	</div>
																	<?php
																}
															} else {
																echo "<p>There is no reviews yet for this product.</p>";
															}
														} else {
															echo "<p>Product ID is not set.</p>";
														}
														?>
													</div><!-- /.product-reviews -->
												</div><!-- /.product-tab -->
											</div><!-- /.tab-pane -->
										</div><!-- /.tab-content -->
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div><!-- /.product-tabs -->

							<?php $cid = $row['category'];
							$subcid = $row['subCategory'];
					}
				} else { ?>
						<h4 style="font-size:30px;">Product Not Found</h4>
					<?php } ?>
					<!-- ============================================== UPSELL PRODUCTS ============================================== -->
					<section class="section featured-product wow fadeInUp">
						<h3 class="section-title">Related Products </h3>
						<div
							class="owl-carousel home-owl-carousel upsell-product custom-carousel owl-theme outer-top-xs">

							<?php
							$qry = mysqli_query($con, "select * from products where subCategory='$subcid' and category='$cid' AND productAvailability = 'In Stock'");
							while ($rw = mysqli_fetch_array($qry)) {

								?>
								<div class="item item-carousel">
									<div class="products">
										<div class="product">
											<div class="product-image">
												<div class="image">
													<a
														href="product-details.php?pid=<?php echo htmlentities($rw['id']); ?>"><img
															src="assets/images/blank.gif"
															data-echo="admin/productimages/<?php echo htmlentities($rw['id']); ?>/<?php echo htmlentities($rw['productImage1']); ?>"
															width="150" height="150" alt=""></a>
												</div><!-- /.image -->
											</div><!-- /.product-image -->
											<div class="product-info text-left">
												<h3 class="name"><a
														href="product-details.php?pid=<?php echo htmlentities($rw['id']); ?>"><?php echo htmlentities($rw['productName']); ?></a>
												</h3>
												<div class="description"></div>

												<div class="product-price">
													<span class="price">
														RM <?php echo htmlentities($rw['productPrice']); ?> </span>
													<span class="price-before-discount">RM
														<?php echo htmlentities($rw['productPriceBeforeDiscount']); ?></span>

												</div><!-- /.product-price -->

											</div><!-- /.product-info -->
											<div class="cart clearfix animate-effect">
												<div class="action">
													<ul class="list-unstyled">
														<li class="add-cart-button btn-group">
															<a href="product-details.php?page=product&action=add&id=<?php echo $rw['id']; ?>"
																class="lnk btn btn-primary">Add to cart</a>

														</li>
													</ul>
												</div><!-- /.action -->
											</div><!-- /.cart -->
										</div><!-- /.product -->

									</div><!-- /.products -->
								</div><!-- /.item -->
							<?php } ?>
						</div><!-- /.home-owl-carousel -->
					</section><!-- /.section -->

					<!-- ============================================== UPSELL PRODUCTS : END ============================================== -->

				</div><!-- /.col -->
				<div class="clearfix"></div>
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
	<script>
		function addToCart(productId) {
			var quantity = document.getElementById('quantity-input').value;
			if (quantity < 1) {
				alert('Quantity must be at least 1.');
				return;
			}

			// Redirect to the add to cart URL with quantity parameter
			window.location.href = 'product-details.php?page=product&action=add&id=' + productId + '&qty=' + quantity;
		}
	</script>

</body>

</html>
