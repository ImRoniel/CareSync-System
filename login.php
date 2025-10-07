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
