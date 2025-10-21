

<?php 
// controllers/auth/session.php

// Start session only if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is not logged in → redirect to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /Caresync-System/login/login.php");
    exit();
}

// (Optional) Role-based protection
// Example: block non-admins from admin area
$currentFile = basename($_SERVER['PHP_SELF']);
if (strpos($currentFile, 'Admin') !== false && ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: /Caresync-System/login/login.php");
    exit();
}

?>