<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: login.php?success=logged_out");
exit;
?>