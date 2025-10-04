<?php
session_start();

// Check if user is logged in and role is secretary
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'secretary') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secretary Dashboard | CareSync</title>
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

    .dashboard {
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      text-align: center;
      width: 450px;
    }

    h1 {
      color: #01796F;
    }

    .info {
      color: #555;
      margin: 20px 0;
    }

    .btn {
      background: #207c33;
      color: #fff;
      border: none;
      padding: 12px 25px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #154d1e;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <img src="images/3.png" alt="CareSync Logo" width="80">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?>!</h1>
    <p class="info">You’re logged in as a <strong>Secretary</strong>.</p>

    <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>
</body>
</html>
