<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include ('includes/config.php');
if (isset($_GET['action']) && $_GET['action'] == "add") {
	$id = intval($_GET['id']);
	if (isset($_SESSION['cart'][$id])) {
		$_SESSION['cart'][$id]['quantity']++;
	} else {
		$sql_p = "SELECT * FROM products WHERE id={$id}";
		$query_p = mysqli_query($con, $sql_p);
		if (mysqli_num_rows($query_p) != 0) {
			$row_p = mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['productPrice']);

		} else {
			$message = "Product ID is invalid";
		}
	}
	echo "<script>alert('Product has been added to the cart.')</script>";
	echo "<script type='text/javascript'> document.location = document.referrer; </script>";
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

	<title>Online Furniture Store</title>

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
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/images/favicon.ico">

</head>

<body class="cnt-home">

	<!-- ============================================== HEADER ============================================== -->
	<header class="header-style-1">
		<?php include ('includes/top-header.php'); ?>
		<?php include ('includes/main-header.php'); ?>
		<?php include ('includes/menu-bar.php'); ?>
	</header>

	<!-- ============================================== HEADER : END ============================================== -->
	<div class="body-content outer-top-xs" id="top-banner-and-menu">
		<div class="container">
			<div class="furniture-container homepage-container">
				<div class="row">

					<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
						<!-- ================================== TOP NAVIGATION ================================== -->
						<?php include ('includes/side-menu.php'); ?>
						<!-- ================================== TOP NAVIGATION : END ================================== -->
					</div><!-- /.sidemenu-holder -->

					<div class="col-xs-12 col-sm-12 col-md-9 homebanner-holder">
						<!-- ========================================== SECTION – HERO ========================================= -->

						<div id="hero" class="homepage-slider3">
							<div id="owl-main" class="owl-carousel owl-inner-nav owl-ui-sm">
								<div class="full-width-slider">
									<div class="item" style="background-image: url(assets/images/sliders/slider1.jpg);">
										<!-- /.container-fluid -->
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->
								<div class="full-width-slider">
									<a href="http://localhost/final-year-project/search-result.php?key=modern">
										<div class="item"
											style="background-image: url(assets/images/sliders/slider2.jpg);">
											<!-- /.container-fluid -->
										</div><!-- /.item -->
									</a>
								</div><!-- /.full-width-slider -->
								<div class="full-width-slider">
									<div class="item" style="background-image: url(assets/images/sliders/slider4.jpg);">
										<!-- /.container-fluid -->
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

								<div class="full-width-slider">
									<div class="item full-width-slider"
										style="background-image: url(assets/images/sliders/slider5.jpg);">
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

								<div class="full-width-slider">
									<div class="item full-width-slider"
										style="background-image: url(assets/images/sliders/slider6.jpg);">
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

								<div class="full-width-slider">
									<div class="item full-width-slider"
										style="background-image: url(assets/images/sliders/slider7.jpg);">
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

								<div class="full-width-slider">
									<div class="item full-width-slider"
										style="background-image: url(assets/images/sliders/slider8.jpg);">
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

								<div class="full-width-slider">
									<div class="item full-width-slider"
										style="background-image: url(assets/images/sliders/slider9.jpg);">
									</div><!-- /.item -->
								</div><!-- /.full-width-slider -->

							</div><!-- /.owl-carousel -->
						</div>

						<!-- ========================================= SECTION – HERO : END ========================================= -->
						<!-- ============================================== INFO BOXES ============================================== -->
						<div class="info-boxes wow fadeInUp">
							<div class="info-boxes-inner">
								<div class="row">
									<div class="col-md-6 col-sm-4 col-lg-4">
										<div class="info-box">
											<div class="row">
												<div class="col-xs-2">
													<i class="icon fa fa-dollar"></i>
												</div>
												<div class="col-xs-10">
													<h4 class="info-box-heading green">Guarantee</h4>
												</div>
											</div>
											<h6 class="text">1 Year Guarantee.</h6>
										</div>
									</div><!-- .col -->

									<div class="hidden-md col-sm-4 col-lg-4">
										<div class="info-box">
											<div class="row">
												<div class="col-xs-2">
													<i class="icon fa fa-truck"></i>
												</div>
												<div class="col-xs-10">
													<h4 class="info-box-heading orange" style="padding-left:10px;">FREE
														SHIPPING</h4>
												</div>
											</div>
											<h6 class="text">FOR ORDER OVER RM1000.00.</h6>
										</div>
									</div><!-- .col -->

									<div class="col-md-6 col-sm-4 col-lg-4">
										<div class="info-box">
											<div class="row">
												<div class="col-xs-2">
													<i class="icon fa fa-gift"></i>
												</div>
												<div class="col-xs-10">
													<h4 class="info-box-heading red">SPECIAL SALE</h4>
												</div>
											</div>
											<h6 class="text">SELECTED ITEM AT 20% OFF.</h6>
										</div>
									</div><!-- .col -->
								</div><!-- /.row -->
							</div><!-- /.info-boxes-inner -->

						</div><!-- /.info-boxes -->
						<!-- ============================================== INFO BOXES : END ============================================== -->
					</div><!-- /.homebanner-holder -->

				</div><!-- /.row -->
				<!-- ============================================== SCROLL TABS ============================================== -->
				<div id="product-tabs-slider" class="scroll-tabs inner-bottom-vs  wow fadeInUp">
					<br><br>
					<div class="more-info-tab clearfix">
						<h3 class="new-product-title pull-left">New Arrival</h3>
						<ul class="nav nav-tabs nav-tab-line pull-right" id="new-products-1">
						</ul><!-- /.nav-tabs -->
					</div>

					<div class="tab-content outer-top-xs">
						<div class="tab-pane in active" id="all">
							<div class="product-slider">
								<div class="owl-carousel home-owl-carousel custom-carousel owl-theme" data-item="4">
									<?php
									$ret = mysqli_query($con, "SELECT products.*, category.categoryName, subcategory.subCategory
									FROM products
									JOIN category ON products.category = category.id
									JOIN subcategory ON products.subCategory = subcategory.id
									WHERE products.productAvailability = 'In Stock'
									  AND category.Status = 'Active'
									  AND subcategory.SubStatus = 'Active'
									ORDER BY products.id DESC;");
									while ($row = mysqli_fetch_array($ret)) {
									
										?>

										<div class="item item-carousel">
											<div class="products">

												<div class="product">
													<div class="product-image">
														<div class="image">
															<a
																href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>">
																<img src="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																	data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																	width="262px" height="262px" alt=""></a>
														</div><!-- /.image -->


													</div><!-- /.product-image -->


													<div class="product-info text-left">
														<h3 class="name"><a
																href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><?php echo htmlentities($row['productName']); ?></a>
														</h3>
														<div class="description"></div>

														<div class="product-price">
															<span class="price">
																RM <?php echo htmlentities($row['productPrice']); ?> </span>
															<span class="price-before-discount">RM
																<?php echo htmlentities($row['productPriceBeforeDiscount']); ?>
															</span>

														</div><!-- /.product-price -->

													</div><!-- /.product-info -->
													<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
														<div class="action"><a
																href="index.php?page=product&action=add&id=<?php echo $row['id']; ?>"
																class="lnk btn btn-primary">Add to Cart</a></div>
													<?php } else { ?>
														<div class="action" style="color:red">Out of Stock</div>
													<?php } ?>
												</div><!-- /.product -->

											</div><!-- /.products -->
										</div><!-- /.item -->
									<?php } ?>

								</div><!-- /.home-owl-carousel -->
							</div><!-- /.product-slider -->
						</div>
						<!-- ============================================== TABS ============================================== -->
						<div class="sections prod-slider-small outer-top-small">
							<div class="row">
								<div class="col-md-6">
									<section class="section">
										<?php
										$retname = mysqli_query($con, 'SELECT subcategory.subcategory as name,subcategory.id FROM subcategory 
								INNER JOIN category ON subcategory.categoryid = category.id 
								WHERE category.Status = "Active" 
								AND subcategory.SubStatus = "Active"
										order by id DESC
								LIMIT 1;');
										$retname_info = mysqli_fetch_array($retname);
										?>
										<h3 class="section-title"><?php echo $retname_info['name'] ?></h3>
										<div class="owl-carousel homepage-owl-carousel custom-carousel outer-top-xs owl-theme"
											data-item="2">

											<?php
											$ret = mysqli_query($con, "SELECT * FROM products
									WHERE products.productAvailability = 'In Stock'
									AND products.subCategory = (SELECT subcategory.id FROM subcategory 
														  INNER JOIN category ON subcategory.categoryid = category.id 
														  WHERE category.Status = 'Active' 
														  AND subcategory.SubStatus = 'Active'
																  ORDER BY id DESC
														  LIMIT 1);");
											while ($row = mysqli_fetch_array($ret)) {
												?>



												<div class="item item-carousel">
													<div class="products">

														<div class="product">
															<div class="product-image">
																<div class="image">
																	<a
																		href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><img
																			src="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			width="262px" height="262px"></a>
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

																</div>

															</div>
															<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
																<div class="action"><a
																		href="index.php?page=product&action=add&id=<?php echo $row['id']; ?>"
																		class="lnk btn btn-primary">Add to Cart</a></div>
															<?php } else { ?>
																<div class="action" style="color:red">Out of Stock</div>
															<?php } ?>
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									</section>
								</div>
								<div class="col-md-6">
									<section class="section">
										<?php
										$retname = mysqli_query($con, 'SELECT subcategory.subcategory as name,subcategory.id FROM subcategory 
								INNER JOIN category ON subcategory.categoryid = category.id 
								WHERE category.Status = "Active" 
								AND subcategory.SubStatus = "Active"
										order by id asc
								LIMIT 1;');
										$retname_info = mysqli_fetch_array($retname);
										?>
										<h3 class="section-title"><?php echo $retname_info['name'] ?></h3>
										<div class="owl-carousel homepage-owl-carousel custom-carousel outer-top-xs owl-theme"
											data-item="2">
											<?php
											$ret = mysqli_query($con, "SELECT * FROM products
									WHERE products.productAvailability = 'In Stock'
									AND products.subCategory = (SELECT subcategory.id FROM subcategory 
														  INNER JOIN category ON subcategory.categoryid = category.id 
														  WHERE category.Status = 'Active' 
														  AND subcategory.SubStatus = 'Active'
																  ORDER BY id ASC
														  LIMIT 1);");
											while ($row = mysqli_fetch_array($ret)) {
												?>
												<div class="item item-carousel">
													<div class="products">

														<div class="product">
															<div class="product-image">
																<div class="image">
																	<a
																		href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><img
																			src="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			width="262" height="262"></a>
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

																</div>

															</div>
															<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
																<div class="action"><a
																		href="index.php?page=product&action=add&id=<?php echo $row['id']; ?>"
																		class="lnk btn btn-primary">Add to Cart</a></div>
															<?php } else { ?>
																<div class="action" style="color:red">Out of Stock</div>
															<?php } ?>
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
									</section>
								</div>
							</div>
						</div>
						<!-- ============================================== TABS : END ============================================== -->

						<section class="section featured-product inner-xs wow fadeInUp"
							style="padding-bottom:0px !important;">
							<?php
							$retname = mysqli_query($con, 'SELECT category.categoryName as name,category.id FROM category 
					WHERE category.Status = "Active" 
					order by name desc
					LIMIT 1;');
							$retname_info = mysqli_fetch_array($retname);
							?>
							<h3 class="section-title"><?php echo $retname_info['name'] ?></h3>
							<div class="owl-carousel best-seller custom-carousel owl-theme outer-top-xs">
								<?php
								$ret = mysqli_query($con, "SELECT * FROM products
						WHERE products.productAvailability = 'In Stock'
						AND products.category = (SELECT category.id FROM category 
						INNER JOIN subcategory ON category.id = subcategory.categoryid
						WHERE category.Status = 'Active' 
						AND subcategory.SubStatus = 'Active'
						ORDER BY categoryName desc
						LIMIT 1);");
								while ($row = mysqli_fetch_array($ret)) {
							
									?>
									<div class="item">
										<div class="products">
											<div class="product">
												<div class="product-micro">
													<div class="row product-micro-row">
														<div class="col col-xs-6">
															<div class="product-image">
																<div class="image">
																	<a href="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																		data-lightbox="image-1"
																		data-title="<?php echo htmlentities($row['productName']); ?>">
																		<img data-echo="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
																			width="170" height="174" alt="">
																		<div class="zoom-overlay"></div>
																	</a>
																</div><!-- /.image -->

															</div><!-- /.product-image -->
														</div><!-- /.col -->
														<div class="col col-xs-6">
															<div class="product-info">
																<h3 class="name"><a
																		href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>"><?php echo htmlentities($row['productName']); ?></a>
																</h3>
																<div class="product-price">
																	<span class="price">
																		RM <?php echo htmlentities($row['productPrice']); ?>
																	</span>

																</div><!-- /.product-price -->
																<?php if ($row['productAvailability'] == 'In Stock' && $row['Quantity'] > 0) { ?>
																	<div class="action"><a
																			href="index.php?page=product&action=add&id=<?php echo $row['id']; ?>"
																			class="lnk btn btn-primary">Add to Cart</a></div>
																<?php } else { ?>
																	<div class="action" style="color:red">Out of Stock</div>
																<?php } ?>
															</div>
														</div><!-- /.col -->
													</div><!-- /.product-micro-row -->
												</div><!-- /.product-micro -->
											</div>
										</div>
									</div><?php } ?>
							</div>
						</section>
						<div style="text-align:center;color:black;text-decoration:underline;">
							<h1>Check us out on X!</h1>
						</div>
						<div style="display:flex;gap:40px;align-items:center;margin-bottom:30px;">
							<div>
								<blockquote class="twitter-tweet">
									<p lang="en" dir="ltr">Seeking top-notch furniture? Look no further! Explore our
										exquisite collection for the perfect blend of style and comfort. Your dream
										space awaits! 💫 <a
											href="https://twitter.com/hashtag/Furniture?src=hash&amp;ref_src=twsrc%5Etfw">#Furniture</a>
										<a
											href="https://twitter.com/hashtag/HomeDecor?src=hash&amp;ref_src=twsrc%5Etfw">#HomeDecor</a>
										<a
											href="https://twitter.com/hashtag/InteriorDesign?src=hash&amp;ref_src=twsrc%5Etfw">#InteriorDesign</a>
										<a href="https://t.co/L49fLq7bcv">pic.twitter.com/L49fLq7bcv</a></p>&mdash;
									Online Furniture Store (@ofurstore) <a
										href="https://twitter.com/ofurstore/status/1798380107398332711?ref_src=twsrc%5Etfw">June
										5, 2024</a>
								</blockquote>
								<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
							</div>
							<div>
								<blockquote class="twitter-tweet">
									<p lang="en" dir="ltr">Dive into our curated selection of stunning furniture pieces.
										From sleek sofas to chic tables, we&#39;ve got everything to make your home
										shine! ✨ <a
											href="https://twitter.com/hashtag/FurnitureGoals?src=hash&amp;ref_src=twsrc%5Etfw">#FurnitureGoals</a>
										<a
											href="https://twitter.com/hashtag/HomeDecor?src=hash&amp;ref_src=twsrc%5Etfw">#HomeDecor</a>
										<a
											href="https://twitter.com/hashtag/InteriorInspo?src=hash&amp;ref_src=twsrc%5Etfw">#InteriorInspo</a>
										<a href="https://t.co/2oAfSvQQdW">pic.twitter.com/2oAfSvQQdW</a></p>&mdash;
									Online Furniture Store (@ofurstore) <a
										href="https://twitter.com/ofurstore/status/1798382985487798706?ref_src=twsrc%5Etfw">June
										5, 2024</a>
								</blockquote>
								<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
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
