<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'User not logged in, redirecting...';
    header("Location: /Caresync-System/login/login.php");
    exit();
} else {
    echo 'User logged in with user_id: ' . $_SESSION['user_id'];
}
?>
