<?php
require_once __DIR__ . '/includes/customer_session.php';

if(isset($_POST['submit'])) {
    include ('includes\config.php');
    
    
    if (isset($_POST['cpass']) && isset($_POST['newpass']) && isset($_POST['cnfpass'])) {

        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $cp = validate($_POST['cpass']);
        $np = validate($_POST['newpass']);
        $cnfp = validate($_POST['cnfpass']);

        
            // hashing the password
            $op = md5($cp);
            $np = md5($np);
            $id = $_SESSION['id'];

            $stmt = $con->prepare("SELECT password FROM users WHERE id=? AND password=?");
            $stmt->bind_param("is", $id, $op);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {//compare the password with the database and which enter by user
                $stmt = $con->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $np, $id);
                $stmt->execute();
                session_destroy();
                
                echo '<script>
                  alert("Your password has been changed successfully.");
                  window.location.href = "login.php";
                  </script>';
               
                exit();
            } else {
                echo '<script>
                  alert("Incorrect current password,PLEASE TRY AGAIN.");
                  window.location.href = "my-account.php";
                  </script>';
                exit();
            }
        }
    }       
  
