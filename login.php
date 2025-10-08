<?php
session_start();
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Step 1: Get user info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Step 2: Store session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            // Step 3: Redirect based on role
            switch ($user['role']) {
                case 'patient':
                    header('Location: dashboards/patient_dashboard.php');
                    break;
                case 'doctor':
                    header('Location: dashboards/doctor_dashboard.php');
                    break;
                case 'secretary':
                    header('Location: dashboards/secretary_dashboard.php');
                    break;
                default:
                    header('Location: login.php?error=invalid_role');
                    break;
            }
            exit;
        } else {
            // Invalid credentials
            header('Location: login.php?error=invalid_credentials');
            exit;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
  if (isset($_GET['success']) && $_GET['success'] === 'logged_out'): ?>
    <p style="color: green;">You have been logged out successfully.</p>
<?php endif; 
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
