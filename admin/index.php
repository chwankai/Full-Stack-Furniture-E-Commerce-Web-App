<?php
require_once __DIR__ . '/include/admin_session.php';
error_reporting(0);
include ("include/config.php");
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $ret = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' and password='$password' ");
    $num = mysqli_fetch_array($ret);
    if ($num > 0) {
        if ($num['Status'] == 'Inactive') {
            $_SESSION['errmsg'] = "Your account is disabled. Please contact admin.";
            $extra = "index.php";
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("location:http://$host$uri/$extra");
            exit();
        } else {
            $extra = "dashboard.php";//
            session_regenerate_id(true);
            $_SESSION['alogin'] = $_POST['username'];
            $_SESSION['id'] = $num['id'];
            $_SESSION['role'] = $num['role'];
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("location:http://$host$uri/$extra");
            exit();
        }
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
        $extra = "index.php";
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
    <script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
</head>
<style>
    .radio.outer-xs a {
        color: white;
        /* Initial color */
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .radio.outer-xs a:hover {
        color: red;
        /* Color on hover */
    }
</style>

<body>
    <main class="login-container">
        <div class="login-card">
            <header class="header">
                <div class="logo">
                    <a href="index.html">
                        <img src="images/Main_logo.png" alt="Logo">
                    </a>
                </div>
                <a href="http://localhost/final-year-project/index.php" class="back-button">Client Side</a>
            </header>

            <form class="login-form" action="index.php" method="post">
                <h2>Admin Sign In</h2>
                <?php if ($_SESSION['errmsg']) { ?>
                    <div class="error-msg" style="color:red;font-weight:bold;">
                        <?php echo htmlentities($_SESSION['errmsg']); ?></div>
                <?php } ?>
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="radio outer-xs">
                    <a href="forgot-password.php">Forgot your Password?</a>
                </div>
                <br>
                <button type="submit" name="submit" class="login-button">Login</button>
            </form>

            <footer class="footer">
                <p>&copy; 2024 Online Furniture Store. All rights reserved.</p>
            </footer>
        </div>
    </main>
</body>

</html>

<?php
// Clear the error message after displaying it
unset($_SESSION['errmsg']);
?>
