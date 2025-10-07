<?php
session_start();
include('db_connect.php'); // Your connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            // Create a session for this user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];  // doctor, patient, secretary

            // Redirect based on role
            if ($user['role'] == 'doctor') {
                header("Location: signup/doctor_dashboard.php");
            } elseif ($user['role'] == 'patient') {
                header("Location: signup/patient_dashboard.php");
            } elseif ($user['role'] == 'secretary') {
                header("Location: signup/secretary_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="assets/css/login.css">
  <title>CareSync Login</title>
  <style>
    
  </style>
</head>
<body>
  <div class="login-container">
    <!-- CareSync Logo -->
    <img src="assets/images/3.png" alt="CareSync Logo">

    <h2 style="color: #626262;">Login</h2>

    <form action="process_login.php" method="POST">
      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="btn">LOGIN</button>
    </form>

    <div class="signup-link">
       I don’t have an account? <a href="index.php">Sign Up</a>
    </div>
  </div>
</body>
</html>
