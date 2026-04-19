<?php //send reset password via gmail 

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli = require __DIR__ . "/includes/config.php";
$sql = "SELECT name FROM users WHERE email = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $stmt->close();}

$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

if ($stmt = $mysqli->prepare($sql)) {

    // Bind parameters
    $stmt->bind_param("sss", $token_hash, $expiry, $email);

    // Execute the statement
    $stmt->execute();

    // Check if the execution was successful
    if ($mysqli->affected_rows) {

        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@example.com", "Online Furniture Store");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                }
                .container {
                    width: 80%;
                    margin: auto;
                    padding: 20px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #ff6600;
                }
                p {
                    line-height: 1.6;
                }
                a {
                    display: inline-block;
                    padding: 10px 20px;
                    color: #fff;
                    background-color: #ff6600;
                    text-decoration:none;
                    border-radius: 4px;
                }
                a:hover {
                    background-color: #cc5200;
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo img {
                    max-width: 200px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <img src="https://i.ibb.co/2Ymx8Lc/Main-logo.png" alt="Online Furniture Store Logo">
                </div>
                <h1>Password Reset Request</h1>
                <p>Dear $name,</p>
                <p>Click the button below to reset your password. If you did not request a password reset, please ignore this email.</p>
                <p><a href="http://localhost/final-year-project/reset-forgot-password.php?token=$token">Reset Password</a></p>
                <p>If you have any questions or concerns, feel free to contact us.</p>
                <p>Best regards,<br>Online Furniture Store</p>
                
            </div>
        </body>
        </html>
        
        END;

        try {

            $mail->send();

        } catch (Exception $e) {

            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

        }

    }

    echo '<script>
    alert("An email has been sent to your email address. Please click on the link in the email to reset your password.");
    window.location.href = "login.php";
    </script>';

    // Close the prepared statement
    $stmt->close();

} else {
    // Preparation failed, display the error
    echo "Prepare failed: " . $mysqli->error;
}

// Close the database connection
$mysqli->close();
?>

<script>
    // Hide the loader modal popup when the processing is complete
    $('#loaderModal').modal('hide');
</script>