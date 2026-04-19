<?php
	require_once __DIR__ . '/admin_session.php';
?>
<div class="span3">
					<div class="sidebar">


<ul class="widget widget-menu unstyled">
<li><a href="dashboard.php"><i class="menu-icon icon-tasks"></i> Dashboard </a></li>
							<li>
								<a class="collapsed" data-toggle="collapse" href="#togglePages">
									<i class="menu-icon icon-cog"></i>
									<i class="icon-chevron-down pull-right"></i><i class="icon-chevron-up pull-right"></i>
									Order Management
								</a>
								<ul id="togglePages" class="collapse unstyled">
									<li>
										<a href="todays-orders.php">
											<b>
											<i class="icon-tasks"></i>
											All Orders
											</b>
											
  <?php
//   $f1="00:00:00";
// $from=date('Y-m-d')." ".$f1;
// $t1="23:59:59";
// $to=date('Y-m-d')." ".$t1;
 $result = mysqli_query($con,"SELECT * FROM orders where orderStatus IS NOT NULL");
$num_rows1 = mysqli_num_rows($result);
{
?>
											<b class="label green pull-right"><?php echo htmlentities($num_rows1); ?></b>
											<?php } ?>
										</a>
									</li>
									<li>
										<a href="new-orders.php">
											<i class="icon-inbox"></i>
											New Orders
										<?php	
	$status='Order Placed';									 
$ret = mysqli_query($con,"SELECT * FROM orders where orderStatus='$status'");
$num = mysqli_num_rows($ret);
{?><b class="label orange pull-right"><?php echo htmlentities($num); ?></b>
<?php } ?>
										</a>
									</li>
									<li>
										<a href="pending-orders.php">
											<i class="icon-inbox"></i>
											In Process Orders
										<?php	
	$status='In Process';									 
$ret = mysqli_query($con,"SELECT * FROM orders where orderStatus='$status'");
$num = mysqli_num_rows($ret);
{?><b class="label orange pull-right"><?php echo htmlentities($num); ?></b>
<?php } ?>
										</a>
									</li>
									<li>
										<a href="delivered-orders.php">
											<i class="icon-inbox"></i>
											Delivered Orders
								<?php	
	$status='Delivered';									 
$rt = mysqli_query($con,"SELECT * FROM orders where orderStatus='$status'");
$num1 = mysqli_num_rows($rt);
{?><b class="label orange pull-right"><?php echo htmlentities($num1); ?></b>
<?php } ?>

										</a>
									</li>
									<li>
										<a href="cancelled-orders.php">
											<i class="icon-inbox"></i>
											Cancelled Orders
								<?php	
	$status='Cancelled';									 
$rt = mysqli_query($con,"SELECT * FROM orders where orderStatus='$status'");
$num1 = mysqli_num_rows($rt);
{?><b class="label orange pull-right"><?php echo htmlentities($num1); ?></b>
<?php } ?>

										</a>
									</li>
								</ul>
							</li>
							
							<li>
								<a href="manage-users.php">
									<i class="menu-icon icon-group"></i>
									Manage Users
								</a>
							</li>
						</ul>


						<ul class="widget widget-menu unstyled">
                                <li><a href="category.php"><i class="menu-icon icon-tasks"></i>Create Category </a></li>
                                <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Category </a></li>
                                <li><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Insert Product </a></li>
                                <li><a href="manage-products.php"><i class="menu-icon icon-table"></i>Manage Products </a></li>
						</ul><!--/.widget-nav-->

						
							<ul class="widget widget-menu unstyled">
							<li><a href="cust-review.php"><i class="menu-icon icon-tasks"></i>Customer Reviews</a></li>
							<li><a href="sales_report.php" target="_blank"><i class="menu-icon icon-tasks"></i>Sales Report </a></li>
							<li><a href="user-logs.php"><i class="menu-icon icon-tasks"></i>User Login Logs</a></li>
							</ul>
						
						
						<ul class="widget widget-menu unstyled">
							<?php
								$admin = mysqli_query($con,"SELECT * FROM ADMIN WHERE id = '" . $_SESSION['id'] . "'"); 
								$admin_info = mysqli_fetch_array($admin);
								if($admin_info['role'] == 'SuperAdmin'){ ?>
									<li><a href="manage-admin.php"><i class="menu-icon icon-group"></i>Manage Admin </a></li>
							<?php }?>
						<li>
							<a href="change-password.php">
								<i class="menu-icon icon-cog"></i>Profile and Settings
							</a>
						</li>
						<li>
							<a href="http://localhost/final-year-project/index.php" target="_blank">
							<script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
							<i class="menu-icon fa-solid fa-arrow-up-right-from-square"></i> Client Portal
							</a>
						</li>
						<li>
							<a href="logout.php">
								<i class="menu-icon icon-signout"></i>Logout
							</a>
						</li>
					</ul>

				</div><!--/.sidebar-->
			</div><!--/.span3-->
