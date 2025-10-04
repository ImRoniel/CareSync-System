<?php
session_start();
include "db_connect.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmpassword = $_POST['confirmpassword'];
  $role = $_POST['role'];

  if ($password !== $confirmpassword) {
    echo "<script>alert('Passwords do not match!');</script>";
  } else {
    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
      echo "<script>alert('Account created successfully!'); window.location.href='login.html';</script>";
    } else {
      echo "<script>alert('Error: Could not create account.');</script>";
    }

    $stmt->close();
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CareSync Signup</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .signup-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      width: 400px;
      text-align: center;
    }

    .signup-container img {
      width: 80px;
      margin-bottom: 20px;
    }

    h2 {
      margin-bottom: 20px;
      color: #626262;
    }

    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }

    .input-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #333;
    }

    .input-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #d9d9d9;
      border-radius: 6px;
      font-size: 14px;
    }

    .role-selection {
      margin: 15px 0;
      text-align: left;
    }

    .role-selection label {
      margin-right: 15px;
      font-size: 14px;
      color: #333;
    }

    .btn {
      background: #207c33;
      color: #fff;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #154d1e;
    }

    .login-link {
      margin-top: 15px;
      font-size: 14px;
      color: #333;
    }

    .login-link a {
      color: #207c33;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="signup-container">
    <img src="images/3.png" alt="CareSync Logo">
    <h2>Create Account</h2>

    <form method="POST" action="login.html" method="POST">
      <div class="input-group">
        <label for="fullname">Full Name</label>
        <input type="text" name="fullname" id="fullname" required>
      </div>

      <div class="input-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div class="input-group">
        <label for="confirmpassword">Confirm Password</label>
        <input type="password" name="confirmpassword" id="confirmpassword" required>
      </div>

      <div class="role-selection">
        <label><input type="radio" name="role" value="patient" checked> Patient</label>
        <label><input type="radio" name="role" value="secretary"> Secretary</label>
      </div>

      <button type="submit" class="btn">SIGN UP</button>
    </form>

    <div class="login-link">
      have an account? <a href="login.html">Login</a> 
    </div>
  </div>
</body>
</html>
