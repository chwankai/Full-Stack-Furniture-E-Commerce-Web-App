<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('ofs_customer_session');
    session_start();
}
?>
