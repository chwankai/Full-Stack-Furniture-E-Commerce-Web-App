<?php
require_once __DIR__ . '/include/admin_session.php';
$_SESSION['alogin'] == "";
session_unset();
//session_destroy();
$_SESSION['errmsg'] = "You have successfully logout";
?>
<script language="javascript">
    document.location = "index.php";
</script>
