<?php
// admin/logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['admin_user']);
header("Location: login.php");
exit;
