<?php
include "includes/db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    $role = $_POST['role'] ?? 'patient';

    // Basic validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirmpassword)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirmpassword) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Check for duplicate email
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists. Please log in instead.'); window.location.href='../../login.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! You can now log in.'); window.location.href='../../login.php';</script>";
    } else {
        echo "<script>alert('Error: could not save user.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
