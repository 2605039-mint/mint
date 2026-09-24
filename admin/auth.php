<?php
// admin/auth.php - Admin Authentication Guard
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_admin_auth() {
    if (!isset($_SESSION['admin_user'])) {
        header("Location: login.php");
        exit;
    }
}
