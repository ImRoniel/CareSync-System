<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="assets/css/signup.css">
  <meta charset="UTF-8">
  <title>CareSync | Doctor Signup</title>
</head>
<body>
  <div class="signup-container">
    <img src="assets/images/logo.png" alt="CareSync Logo">
    <h2>Doctor Registration</h2>

    <form action="controllers/auth/register_user.php" method="POST">
      <input type="hidden" name="role" value="doctor">

      <div class="input-group">
        <label>Full Name</label>
        <input type="text" name="fullname" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="input-group">
        <label>Specialization</label>
        <input type="text" name="specialization" placeholder="e.g., Pediatrics, Surgery">
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="input-group">
        <label>Confirm Password</label>
        <input type="password" name="confirmpassword" required>
      </div>

      <button type="submit" class="btn">Sign Up</button>
    </form>

    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
