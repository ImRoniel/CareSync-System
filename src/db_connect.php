<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caresync_db"; // ✅ Make sure this matches your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
