
<?php
include(__DIR__ . '/../includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $license = $_POST['license_no'];
    $spec = $_POST['specialization'];

    try {
        $pdo->beginTransaction();

        // Step 1: insert into users
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'doctor')");
        $stmt->execute([$name, $email, $password]);
        $user_id = $pdo->lastInsertId();

        // Step 2: insert into patients
        $stmt2 = $pdo->prepare("INSERT INTO doctors (user_id, license_no, specialization) VALUES (?, ?, ?)");
        $stmt2->execute([$user_id, $license_no, $specialization]);

        $pdo->commit();

        header('Location:login.php?success=registered');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../assets/css/signup.css">
  <meta charset="UTF-8">
  <title>CareSync | Doctor Signup</title>
</head>
<body>
  <div class="signup-container">
    <img src="../assets/images/3.png" alt="CareSync Logo">
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

    <p class="login-link">Already have an account? <a href="../login.php">Login</a></p>
  </div>
</body>
</html>
