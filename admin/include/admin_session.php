<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('ofs_admin_session');
    session_start();
}
?>
