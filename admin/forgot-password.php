<?php
require_once __DIR__ . '/include/admin_session.php';
error_reporting(0);
include ('include/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer autoload file
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ`-=[];.,/*!@#$%^&*)(|';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
if (isset($_POST['save'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = generateRandomPassword();
    $hashedPassword = md5($password);
    $ret = mysqli_query($con, "SELECT * FROM  admin WHERE  email='$email' AND username='$username'");
    $num = mysqli_fetch_array($ret);
    if ($num > 0) {

        if ($num['Status'] == 'Inactive') {
            $_SESSION['errmsg'] = "Your account is inactive. Please contact the administrator.";
            $extra = "forgot-password.php";
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("location:http://$host$uri/$extra");
            exit();
        } else {
            $query = mysqli_query($con, "UPDATE admin SET password='$hashedPassword' WHERE email='$email' OR username='$username'");
            $mail = new PHPMailer(TRUE);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = 'TRUE';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Username = "ofurstore@gmail.com";// gmail
            $mail->Password = "weqr glie hrju pxtp";//gmail app password
            $mail->Port = 587;
            $mail->setFrom("ofurstore@gmail.com", "Online Furniture Store | Admin");
            $mail->addAddress($email);// send to the recipient

            $mail->isHTML(TRUE);

            $mail->Subject = 'Password Reset for Admin Account';
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
                    <p>Hi there,</p>
                    <p>Your password has been reset. Below are your new account details:</p>
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
                echo "<script>alert('The new password successful send to your email.Please check your email.');</script>";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        }
    } else {
        $_SESSION['errmsg'] = "Username Or Email not found";
        $extra = "forgot-password.php";
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFS | Admin Login Portal</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="css/loader.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
</head>

<body>
    <main class="login-container">
        <div class="login-card">
            <header class="header">
                <div class="logo">
                    <a href="index.html">
                        <img src="images/Main_logo.png" alt="Logo">
                    </a>
                </div>
                <a href="http://localhost/shopping/final-year-project-1/" class="back-button">Client Side</a>
            </header>

            <form class="login-form" action="forgot-password.php" method="post" style="margin-bottom:0px;">
                <h2>Admin | Forgot Password</h2>
                <?php if ($_SESSION['errmsg']) { ?>
                    <div class="error-msg" style="color:red;font-weight:bold;">
                        <?php echo htmlentities($_SESSION['errmsg']); ?></div>
                <?php } ?>
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required
                        style="background-color: rgba(255, 255, 255, 0.15) !important;border-radius: 5px !important;border:none !important;font-size:16px !important;color:white!important;padding:10px!important;outline:none!important;">
                </div>
                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email" required
                        style="background-color: rgba(255, 255, 255, 0.15) !important;border-radius: 5px !important;border:none !important;font-size:16px !important;color:white !important;padding:10px!important;outline:none!important;">
                </div>
                <br>
                <button type="submit" name="save" class="login-button" id="submitBtn">Submit</button>
                <button type="button" name="return" class="login-button" onclick="goToIndex()"
                    style="margin-top:10px">Back</button>
            </form>

            <footer class="footer">
                <p>&copy; 2024 Online Furniture Store. All rights reserved.</p>
            </footer>
        </div>
        <div id="loaderModal" style="display:none;" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="loader"></div>
                        <p style="text-align:center;">Please wait while we are processing your request...</p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</body>

</html>
<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>
<script>
    function goToIndex() {
        window.location.href = 'index.php';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('submitBtn').addEventListener('click', function (event) {
            // Check if both username and email are valid
            if (validateForm()) {
                // Show the loader modal popup
                $('#loaderModal').modal('show');
            } else {
                // If validation fails, prevent the default form submission behavior
                event.preventDefault();
            }
        });
    });

    function validateForm() {
        var username = document.getElementById('username').value.trim();
        var email = document.getElementById('email').value.trim();

        // Regular expression pattern to validate email format
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Perform validation checks
        if (username === '' || email === '') {
            // If any field is empty, show an alert and return false
            alert('Please fill in both username and email fields.');
            return false;
        } else if (!emailPattern.test(email)) {
            // If email format is invalid, show an alert and return false
            alert('Please enter a valid email address.');
            return false;
        }

        // If all checks pass, return true
        return true;
    }
</script>
<?php
// Clear the error message after displaying it
unset($_SESSION['errmsg']);
?>
