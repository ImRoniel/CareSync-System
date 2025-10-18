<?php
include "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'patient'; // optional

    //  Basic validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    //  Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //  Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists.'); window.history.back();</script>";
        exit();
    }

    //  Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! Redirecting to login...'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Signup failed: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
