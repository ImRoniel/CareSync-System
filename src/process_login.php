<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'patient') {
                header("Location: patient_dashboard.php");
            } else if ($row['role'] === 'secretary') {
                header("Location: secretary_dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
