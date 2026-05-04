<?php
require_once __DIR__ . '/includes/customer_session.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/google-config.php';

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (!isset($token['error'])) {
            $client->setAccessToken($token['access_token']);
            
            $google_oauth = new Google_Service_Oauth2($client);
            $userInfo = $google_oauth->userinfo->get();
            
            $email = $userInfo->email;
            $name = $userInfo->name;
            
            // Check if user exists
            $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
            $num = mysqli_fetch_array($query);
            
            if ($num > 0) {
                // User exists, log them in
                $_SESSION['login'] = $email;
                $_SESSION['id'] = $num['id'];
                $_SESSION['username'] = $num['name'];
                
                $status = 1;
                mysqli_query($con, "insert into userlog(userEmail,status) values('$email','$status')");
                
                header("location: index.php");
                exit();
            } else {
                // User does not exist, auto-register
                $password = md5(rand(1000000, 9999999)); // Random password
                
                $insert_query = "insert into users(name,email,password,shippingReceiver,billingReceiver) values('$name','$email','$password','$name','$name')";
                $query_run = mysqli_query($con, $insert_query);
                
                if ($query_run) {
                    $last_id = mysqli_insert_id($con);
                    $_SESSION['login'] = $email;
                    $_SESSION['id'] = $last_id;
                    $_SESSION['username'] = $name;
                    
                    $status = 1;
                    mysqli_query($con, "insert into userlog(userEmail,status) values('$email','$status')");
                    
                    header("location: index.php");
                    exit();
                } else {
                    $_SESSION['errmsg'] = "Something went wrong during Google registration: " . mysqli_error($con);
                    header("location: login.php");
                    exit();
                }
            }
        } else {
            $_SESSION['errmsg'] = "Failed to login with Google: " . print_r($token, true);
            header("location: login.php");
            exit();
        }
    } catch (Exception $e) {
        die('Google Auth Error: ' . $e->getMessage());
    }
} else {
    header("location: login.php");
    exit();
}
?>