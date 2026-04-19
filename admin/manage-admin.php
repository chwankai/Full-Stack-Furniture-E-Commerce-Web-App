<?php
require_once __DIR__ . '/include/admin_session.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('include/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ`-=[];.,/*!@#$%^&*)(|';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
function validateEmailDomain($email)
{
    $allowedDomains = ['gmail.com', 'student.mmu.edu.my', 'yahoo.com', 'mmu.edu.my', 'outlook.com', 'icloud.com'];
    $emailParts = explode('@', $email);
    if (count($emailParts) === 2) {
        $domain = $emailParts[1];
        if (in_array($domain, $allowedDomains)) {
            return true;
        }
    }
    return false;
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$currentTime = date('d-m-Y h:i:s A', time());

if (isset($_GET['del']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($con, "DELETE FROM admin WHERE id='$id'");
    if ($result) {
        $_SESSION['delmsg'] = "Admin deleted !!";
    } else {
        $_SESSION['delmsg'] = "Error deleting admin.";
    }
}

if (isset($_POST['savebtn']) && $_SESSION['role'] !== 'SuperAdmin') {
    echo "<script>alert('You are not authorized to access this function!!!!!');</script>";
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'SuperAdmin') {
    if (isset($_POST['savebtn'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = generateRandomPassword();
        $hashedPassword = md5($password);

        $result = mysqli_query($con, "SELECT `username`, `email` FROM admin WHERE email='$email' OR username='$username'");

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Username or email already exists. Please try again.');</script>";
        } else {
            $query = mysqli_query($con, "INSERT INTO admin(username, email, password) VALUES ('$username', '$email', '$hashedPassword')");
            if ($query) {
                $mail = new PHPMailer(TRUE);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = TRUE;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->Port = 587;
                $mail->setFrom("ofurstore@gmail.com", "Online Furniture Store | Admin");
                $mail->addAddress($email);
                $mail->isHTML(TRUE);
                $mail->Subject = 'Welcome to the Admin Family!';
                $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Welcome Aboard!</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f2f2f2;
                            padding: 20px;
                        }
                        .header{
                            text-align: center;
                            padding: 10px 0;
                        }
                        .header img{
                            max-width: 300px;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            background-color: #fff;
                            padding: 30px;
                            border-radius: 10px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }
                        h1 {
                            color: #ff6600;
                            text-align: center;
                        }
                        p {
                            color: #333;
                            margin-bottom: 20px;
                        }
                        .credentials {
                            background-color: #f9f9f9;
                            padding: 15px;
                            border-radius: 5px;
                            margin-bottom: 20px;
                        }
                        .footer {
                            background-color: #ff6600;
                            color: #fff;
                            text-align: center;
                            padding: 10px 0;
                            border-radius: 0 0 10px 10px;
                        }
                    </style>
                </head>
                <body>
                    
                    <div class='header'>
                    <a href='http://localhost/final-year-project/admin/index.php'>
                        <img src='https://i.ibb.co/2Ymx8Lc/Main-logo.png' alt='Main-logo' border='0'>
                    </a>
                    </div>
                    <div class='container'>
                        <p>Welcome Aboard!</p>
                        <p>Hi there,</p>
                        <p>We're excited to have you as part of our admin team. Below are your account details:</p>
                        <div class='credentials'>
                            <p><strong>Username:</strong> " . $username . " </p>
                            <p><strong>Password:</strong>  " . $password . "</p>
                        </div>
                        <p>For security reasons, we recommend that you change your password as soon as you log in for the first time.</p>
                        <p>Best regards,</p>
                        <p>Online Furniture Store</p>
                    </div>
                    <div class='footer'>
                        &copy; 2024 Online Furniture Store. All rights reserved.
                    </div>
                </body>
                </html>
                ";

                try {
                    $mail->send(); ?>
                    <script>
                        // Hide the loader modal popup when the processing is complete
                        $('#loaderModal').modal('hide');
                    </script>
                    <?php
                    echo "<script>alert('Register successfully.');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Mail could not be sent.');</script>";
                }
            } else {
                echo "<script>alert('Not registered, something went wrong.');</script>";
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin | Manage Admins</title>
        <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
        <link type="text/css" href="css/theme.css" rel="stylesheet">
        <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
        <link type="text/css" href="css/loader.css" rel="stylesheet">
        <link rel="shortcut icon" href="images/favicon.ico">
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
            rel='stylesheet'>
        <style>
            .popup {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
            }

            .popup-content {
                background-color: #fff;
                width: 300px;
                margin: 100px auto;
                padding: 20px;
                border-radius: 5px;
            }

            .add_btn:hover {
                background-color: #CC5500 !important;
            }
        </style>
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
                                    <h3>Manage Admins</h3>
                                </div>
                                <div class="module-body table">
                                    <?php if (isset($_SESSION['delmsg'])) { ?>
                                        <div class="alert alert-error">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                            <?php unset($_SESSION['delmsg']); ?>
                                        </div>
                                    <?php } ?>
                                    <div style="">
                                        <button onclick="showPopup()" class="add_btn"
                                            style="display:flex;color:white;margin-left:auto;margin-right:15px;padding:5px 10px;border-radius:8px;border:0px;background-color:#ff6a00c2;transition:all 0.3s;">Add
                                            New Admin</button>
                                    </div>
                                    <br />
                                    <table cellpadding="0" cellspacing="0" border="0" style="font-size:13px !important;"
                                        class="datatable-1 table table-bordered table-striped display" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Admin ID#</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th>Email</th>
                                                <th>Reg. Date</th>
                                                <th>Status</th>
                                                <th style="text-align:center;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = mysqli_query($con, "SELECT * FROM admin");
                                            $cnt = 1;
                                            while ($row = mysqli_fetch_array($query)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlentities($row['id']); ?></td>
                                                    <td><?php echo htmlentities($row['username']); ?></td>
                                                    <td><?php echo htmlentities($row['role']); ?></td>
                                                    <td><?php echo htmlentities($row['email']); ?></td>
                                                    <td><?php echo htmlentities($row['creationDate']); ?></td>
                                                    <td><?php echo htmlentities($row['Status']); ?></td>
                                                    <td style="text-align:center;">
                                                        <?php if ($row['role'] != "SuperAdmin") { ?>
                                                            <a href="edit-admin.php?id=<?php echo $row['id'] ?>"><i
                                                                    class="icon-edit"></i></a>
                                                            <!-- <a href="manage-admin.php?id=<?php echo $row['id'] ?>&del=delete"
                                                                onClick="return confirm('This account will be delete permanently. Are you sure you want to delete?')"><i
                                                                    class="icon-remove-sign"></i></a> -->
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php $cnt = $cnt + 1;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!--/.content-->
                        <div class="popup" id="registrationPopup">
                            <div class="popup-content">
                                <span onclick="hidePopup()" style="float: right; cursor: pointer;">&times;</span>
                                <h2>Register New Admin</h2>
                                <form action="manage-admin.php" method="POST" onsubmit="return validateForm()">
                                    <label for="username">Username:</label><br>
                                    <input type="text" id="username" name="username" required><br>
                                    <label for="email">Email:</label><br>
                                    <input type="email" id="email" name="email" maxlength="50" autocomplete="off"
                                        required><br>
                                    <input type="submit" value="Register" name="savebtn">
                                </form>
                            </div>
                        </div>

                        <div id="loaderModal" class="modal fade" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div id="loader"></div>
                                        <p style="text-align:center;">Please wait while we are processing your request...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--/.span9-->
                </div><!--/.row-->
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

            function validateEmail(email) {


                const allowedDomains = ['gmail.com', 'student.mmu.edu.my', 'yahoo.com', 'soffice.mmu.edu.my', 'outlook.com', 'icloud.com'];
                const emailParts = email.split('@');
                if (emailParts.length === 2) {
                    const domain = emailParts[1];
                    if (allowedDomains.includes(domain)) {
                        return true;
                    }
                }

                return false;
            }

            function showPopup() {
                document.getElementById('registrationPopup').style.display = 'block';
            }

            function hidePopup() {
                document.getElementById('registrationPopup').style.display = 'none';
            }

            function validateForm() {
                const email = document.getElementById('email').value;
                hidePopup();
                $('#loaderModal').modal('show');
                if (!validateEmail(email)) {
                    $('#loaderModal').modal('hide');
                    alert('Email must be from @gmail.com, @student.mmu.edu.my, @yahoo.com, @mmu.edu.my, @outlook.com, or @icloud.com domains.');
                    location.reload();
                    return false;
                }
                return true;
            }
        </script>
    </body>

    </html>
<?php } ?>
