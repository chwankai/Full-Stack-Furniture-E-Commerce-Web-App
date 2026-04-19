<?php
require_once __DIR__ . '/includes/customer_session.php';
include("includes/config.php");
$_SESSION['login']=="";
date_default_timezone_set('Asia/Kuala_Lumpur');
$ldate=date( 'd-m-Y h:i:s A', time () );
mysqli_query($con,"UPDATE userlog  SET logout = '$ldate' WHERE userEmail = '".$_SESSION['login']."' ORDER BY id DESC LIMIT 1");
session_unset();
$_SESSION['errmsg']="You have successfully logout";
echo"<script>alert('You have successfully logout.');</script>";
?>
<script language="javascript">
document.location="index.php";

</script>
