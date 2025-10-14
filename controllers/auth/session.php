<?php
// controllers/auth/session.php

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in → redirect
    header("Location: /Caresync-System/login/login.php");
    exit();
} else {
    // Optional: debug info, remove in production
    // echo 'User logged in with user_id: ' . $_SESSION['user_id'];
}
?>
