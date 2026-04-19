<?php
require_once __DIR__ . '/admin_session.php';
error_reporting(0);
include('include/config.php');


   // Fetch the username from the session or databas

$adminQuery = mysqli_query($con, "SELECT username FROM admin WHERE id='" . $_SESSION['id'] . "'");
$adminResult = mysqli_fetch_assoc($adminQuery);
$name = $adminResult['username'];

?>

<!--  -->
<style>
  .white-text {
    color: white;
    transition: all 0.03s;
}
.white-text:hover {
    background-color: #CC5500 !important;
}
</style>
<!--  -->
<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style="max-height:60px !important;">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>
        <a class="brand" style="padding:5px !important; margin:10px !important;border-radius:8px;background-color:white;" href="../admin/dashboard.php" >
          <p style="color:#f57336;text-shadow:none !important;margin:0px !important;display:flex;justify-content:center;align-items: center;"><img style="height:30px;" src="images/Main_logo.png" alt="Main Logo">| ADMIN</p>
			  	</a>
        
			  	

				<div class="nav-collapse collapse navbar-inverse-collapse">
					<ul class="nav pull-right">
						<!-- <li>
              <a class="white-text" style="color:white;text-shadow:none !important;" href="http://localhost/final-year-project/index.php">
							  <p style="color:white;text-shadow:none !important;margin:0px !important;">Client Side</p> 
						  </a>
            </li> -->
						<li class="nav-user dropdown">
              <div style="max-height:30px;background-color:#ff8f53;width:300px;height:100%;color:white;text-shadow:none !important;display:flex;justify-content:flex-end;padding:15px 15px;">
                <p style="color:white;text-shadow:none !important;margin:0px !important;display:flex;justify-content:center;align-items: center;text-align:right;"><?php echo '<span style="font-weight:bold;margin-right:10px;">' .htmlentities($name). '</span>';?></p>
								<img src="images/user.png" style="margin:0px 0px !important;height:30px;width:30px;" class="nav-avatar" /> 
              </div>
						</li>
					</ul>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->
	
</div>
