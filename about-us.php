<?php
error_reporting(0);
include ('includes/config.php');

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

  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/orange.css">
  <link rel="stylesheet" href="assets/css/owl.carousel.css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css">
  <link href="assets/css/lightbox.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/rateit.css">
  <link rel="stylesheet" href="assets/css/config.css">

  <link rel="stylesheet" href="assets/css/font-awesome.min.css">
  <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>

  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

  <!-- Favicon -->
  <link rel="shortcut icon" href="assets/images/favicon.ico">

  <style>
    .navbar-nav {
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-nav .nav-link {
      color: #fff;
      font-size: 16px;
      font-weight: 500;
      padding: 0 10px;
    }

    .navbar-nav .nav-link:hover {
      color: #ffcc00;
    }

    .navbar-brand {
      font-size: 24px;
      font-weight: 700;
      padding: 0;
    }

    .navbar-toggler {
      border: none;
      padding: 0;
      margin-right: 10px;
    }

    @media (max-width: 991px) {
      .navbar-nav {
        flex-direction: column;
      }

      .navbar-nav .nav-link {
        padding: 10px 0;
      }
    }

    .member {
      background-color: white;
      /* light gray background */
      padding: 10px;
      border-radius: 10px;
      /* rounded corners */
      width: auto;
      font-size: 26px;
      color: #333;
      margin-top: 10px;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    .info {
      font-size: 16px;
      padding: 10px;
      margin-bottom: 10px;
      font-family: 'Montserrat', sans-serif;

    }

    p {
      font-size: 12px;
    }

    .margin {
      margin-bottom: 45px;
      font-family: sans-serif;
      color: `#FF8A08;
      font-weight: bold;
      font-size: 40px;
    }

    .bg-1 {
      background-color: #f9f9f9;
      /* Background color */
      color: black;
      /* Text color */
      width: 100%;
      /* Full width */
      height: auto;
      /* Auto height */
      padding: 20px 0;
      /* Top and bottom padding */
    }

    .bg-2 {
      background-color: orange;
      /* Dark Blue */
      color: #ffffff;
    }

    .bg-3 {
      background-color: #ffffff;
      /* White */
      color: #555555;
    }

    .container-fluid {
      padding-top: 50px;
      padding-bottom: 50px;
    }

    .profile-picture {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      margin: 20px auto;
      border: 1px solid #ddd;
      padding: 5px;
    }

    .mission {
      margin-bottom: 20px;
      text-align: justify;
      /* Justify the text */
      font-size: 18px;
    }

    .mission-box {
      padding: 20px width: 1000px;
      text-align: justify;
      padding: 20px;
      border-radius: 10px;
      /* rounded corners */
      width: 1200px;
      margin: 0 auto;
      /* Center the box horizontally */
    }

    .mission-box .btn {
      display: block;
      margin: 20px auto;
      /* Center the button horizontally and add some top margin */
      background-color: #f8f9fa;
      /* Default background color */
      color: #000;
      /* Default text color */
      border: 1px solid #ccc;
      /* Default border color */
      padding: 10px 20px;
      /* Padding for the button */
      text-decoration: none;
      /* Remove underline */
      transition: background-color 0.3s ease;
      /* Smooth transition for background color */
    }

    .mission-box .btn:hover {
      background-color: #FF8A08;
      /* Hover background color */
      color: #fff;
      /* Hover text color */
      border: 1px solid #FF7D29;
      /* Hover border color */
    }

    .info-section {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .info-section p {
      font-size: 14px;
      line-height: 1.5;
      font-size: 16px;
      padding: 10px;
      font-family: 'Montserrat', sans-serif;

    }

    .info-section .detail {
      font-weight: bold;
      color: #333;
    }

    .title-info {
      border: solid 2px orange;
      background-color: orange;
      color: white;
      border-radius: 30px;
      padding: 20px;
      margin: 20px;
      letter-spacing: 1px;
    }

    .title-info:hover {
      background-color: #FB8B24;
    }
  </style>
  <title>About Us</title>

  <!-- ================================== TOP NAVIGATION ================================== -->

</head>

<body>

  <body class="cnt-home">
    <!-- ============================================== HEADER ============================================== -->
    <header class="header-style-1">
      <?php include ('includes/top-header.php'); ?>
      <?php include ('includes/main-header.php'); ?>
      <?php include ('includes/menu-bar.php'); ?>
    </header>

    <!-- About 1 - Bootstrap Brain Component -->

    <section class="banner">
      <div class="container-fluid bg-1 text-center">
        <h1 class="margin">ABOUT US</h1>
        <img src="assets/images/logo/main-logo.png" style="display:inline" alt="main-logo" width="550" height="200">
        <h4 style="padding:15px;" class="slogan">We offer a wide range of high-quality furniture for your home and
          office. Shop with us and transform your living spaces into beautiful and comfortable environments.</h3>
          <div class="info-section">
            <p>Our location:</p>
            <p class="detail">Jalan Ayer Keroh Lama, 75450 Bukit Beruang, Melaka, Malaysia</p>

            <p>Contact us:</p>
            <p class="detail">1-300-80-0668</p>
            <p class="detail">+606-2523253/3254</p>

            <p>Email us:</p>
            <p class="detail">ofur@gmail.com</p>
          </div>

          <!-- Second Container -->
          <div class="container-fluid bg-2 text-center">
            <h3 class="margin" style="display: flex;justify-content: center;align-items: center;">OUR MISSION</h3>
            <div class="mission-box">
              <p class="mission">At <b>Online Furniture Store</b>, our mission is to transform every house into a home
                by providing high-quality, stylish,
                and affordable furniture that caters to the unique tastes and needs of our diverse customer base. We
                strive to offer an exceptional online shopping experience with seamless navigation, comprehensive
                product information, and unparalleled customer service.
                Our commitment is to sustainability and innovation, ensuring that our products not only enhance living
                spaces but also contribute to a better, greener future.
                We aim to inspire creativity and comfort, making home furnishing a joyous and fulfilling experience for
                everyone.</p><br>
              <a href="index.php" class="btn btn-default btn-lg">
                Start shopping now
              </a>
            </div>

          </div>

          <!-- Third Container (Grid) -->
          <div class="container-fluid bg-3 text-center">
            <h1 class="margin">About Our Members</h1><br>
            <div class="row">
              <div class="col-sm-4">
                <div class="title-info">
                  <img src="assets/images/user.png" alt="Image" class="profile-picture">
                  <h2>See Chwan Kai</h2>
                  <p class="info"><b>Student ID : </b>1211207735 </p>
                  <p class="info"><b>Email Address : </b>1211207735@student.mmu.edu.my</p>
                  <p class="info"><b>Programme :</b><br> Diploma In Information Technology</p>
                </div>

              </div>
              <div class="col-sm-4">
                <div class="title-info">
                  <img src="assets/images/user.png" alt="Image" class="profile-picture">
                  <h2>Tee Kian Hao</h2>
                  <p class="info"><b>Student ID : </b>1211208688 </p>
                  <p class="info"><b>Email Address : </b>1211208688@student.mmu.edu.my</p>
                  <p class="info"><b>Programme :</b><br> Diploma In Information Technology</p>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="title-info">
                  <img src="assets/images/user.png" alt="Image" class="profile-picture">
                  <h2>Teo Jing An</h2>
                  <p class="info"><b>Student ID : </b>1211208694 </p>
                  <p class="info"><b>Email Address : </b>1211208694@student.mmu.edu.my</p>
                  <p class="info"><b>Programme :</b><br> Diploma In Information Technology</p>
                </div>
              </div>
            </div>
          </div>

    </section>
    <FOOTER>
      <?php include ('includes/footer.php'); ?>
    </FOOTER>
  </body>

</html>
