<?php
include "db_connect.php"; //  Make sure this path is correct

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            echo "<script>alert('Login successful!'); window.location.href='patient_dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
