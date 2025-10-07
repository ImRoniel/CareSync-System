<?php
$host = 'localhost';
$dbname = 'caresync_db';
$username = 'root';   // change if your MySQL user is different
$password = '';       // change if you have a password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Database connected successfully"; // optional test
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
