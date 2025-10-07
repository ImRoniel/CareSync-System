<?php
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $assigned_doctor_id = !empty($_POST['assigned_doctor_id']) ? $_POST['assigned_doctor_id'] : null;

    try {
        $pdo->beginTransaction();

        // Step 1: Insert into users (role = 'secretary')
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'secretary')");
        $stmt->execute([$name, $email, $password]);
        $user_id = $pdo->lastInsertId();

        // Step 2: Insert into secretaries table
        $stmt2 = $pdo->prepare("INSERT INTO secretaries (user_id, assigned_doctor_id) VALUES (?, ?)");
        $stmt2->execute([$user_id, $assigned_doctor_id]);

        $pdo->commit();

        header('Location: login.php?success=registered');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="assets/css/signup.css">
  <meta charset="UTF-8">
  <title>CareSync | Secretary Signup</title>
</head>
<body>
  <div class="signup-container">
    <img src="assets/images/logo.png" alt="CareSync Logo">
    <h2>Secretary Registration</h2>

    <form action="controllers/auth/register_user.php" method="POST">
      <input type="hidden" name="role" value="secretary">

      <div class="input-group">
        <label>Full Name</label>
        <input type="text" name="fullname" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="input-group">
        <label>Clinic Code</label>
        <input type="text" name="clinic_code" placeholder="Enter assigned clinic code">
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
