<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CareSync Login</title>
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

    .login-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      width: 400px;
      text-align: center;
    }

    .login-container img {
      width: 80px;
      margin-bottom: 20px;
    }

    h2 {
      margin-bottom: 20px;
      color: #01796F; /* Pine Green */
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
      background: #2E603D; /* Darker Pine Green */
    }

    .signup-link {
      margin-top: 15px;
      font-size: 14px;
      color: #333;
    }

    .signup-link a {
      color: #111814;
      text-decoration: none;
      font-weight: bold;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- CareSync Logo -->
    <img src="images/3.png" alt="CareSync Logo">

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
       don’t have an account? <a href="signup.php">Sign Up</a>
    </div>
  </div>
</body>
</html>
