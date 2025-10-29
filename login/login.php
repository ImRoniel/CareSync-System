<?php

// login.php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();        
}

// Include DB connection
$config_path = __DIR__ . '/../config/db_connect.php';
if (!file_exists($config_path)) {
    die("Database configuration file not found.");
}
require_once $config_path;

require_once __DIR__ . '/../controllers/auth/AuthController.php';
$authController = new AuthController($conn);

// Initialize variables
$errors = [];
$email = '';
$password = '';

// If user is already logged in, redirect them to their dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'doctor':
            header("Location: ../views/doctor/Doctor_Dashboard1.php");
            exit();
        case 'patient':
            header("Location: ../views/patient/Patient_DashBoard1.php");
            exit();
        case 'secretary':
            header("Location: ../views/secretary/Secretary_Dashboard1.php");
            exit();
        case 'admin':
            header("Location: ../views/admin/Admin_Dashboard1.php");
            exit();
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {

        //  Static admin login (bypass database)
        if ($email === 'admin@gmail.com' && $password === 'admin') {
            $_SESSION['user_id'] = 0; // no DB record, just static admin
            $_SESSION['user_name'] = 'Administrator';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'admin';
            $_SESSION['logged_in'] = true;

            header("Location: ../views/admin/Admin_Dashboard1.php");
            exit();
        }

        //  Otherwise, check in database
        $check_table = $conn->query("SHOW TABLES LIKE 'users'");
        if ($check_table->num_rows == 0) {
            $errors[] = "Users table doesn't exist.";
        } else {
            // Prepare query to find user by email
            $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            if (!$stmt) {
                $errors[] = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $user_name, $user_email, $hashed_password, $user_role);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        // Successful login
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $user_name;
                        $_SESSION['user_email'] = $user_email;
                        $_SESSION['user_role'] = $user_role;
                        $_SESSION['logged_in'] = true;
                    
                    // For admin users, get additional admin data
                        if ($user_role === 'admin') {
                            $admin_data = $authController->getAdminData($user_id);
                            if ($admin_data) {
                                $_SESSION['admin_data'] = $admin_data;
                            }
                        }
                        // Redirect based on role
                        switch ($user_role) {
                            case 'doctor':
                                header("Location: ../views/doctor/Doctor_Dashboard1.php");
                                exit();
                            case 'patient':
                                header("Location: ../views/patient/Patient_Dashboard1.php");
                                exit();
                            case 'secretary':
                                header("Location: ../views/secretary/Secretary_Dashboard1.php");
                                exit();
                            case 'admin':
                                header("Location: ../views/admin/Admin_Dashboard1.php");
                                exit();
                            default:
                                header("Location: ../views/admin/default.php");
                                exit();
                        }
                    } else {
                        $errors[] = "Invalid email or password.";
                    }
                } else {
                    $errors[] = "Invalid email or password.";
                }
                $stmt->close();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CareSync</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E8949;
            --primary-dark: #245033;
            --primary-light: #AD5057;
            --secondary: #CFCFCF;
            --accent: #AD5057;
            --danger: #AD5057;
            
            --text-dark: #111814;
            --text-medium: #2E603D;
            --text-light: #CFCFCF;
            
            --bg-white: #FFFFFF;
            --bg-light: #f8f9fa;
            --bg-gray: #CFCFCF;
            
            --border-light: #CFCFCF;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }
        
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .left-panel:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" preserveAspectRatio="none"><path fill="%23ffffff" fill-opacity="0.03" d="M0,0 L1000,1000 L0,1000 Z"></path></svg>');
            background-size: cover;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.75rem;
            color: white;
            text-decoration: none;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .logo-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }
        
        .left-content {
            position: relative;
            z-index: 1;
            max-width: 500px;
        }
        
        .left-content h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .left-content p {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .features-list {
            list-style: none;
            margin-top: 40px;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 1rem;
        }
        
        .features-list i {
            color: white;
            font-size: 1.25rem;
        }
        
        .right-panel {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-white);
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .form-header h2 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .form-header p {
            color: var(--text-medium);
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .required:after {
            content: '*';
            color: var(--danger);
            margin-left: 4px;
        }
        
        input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--bg-white);
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            gap: 10px;
            font-size: 1rem;
            width: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn:disabled {
            background-color: var(--secondary);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.875rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input {
            width: auto;
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 30px;
            color: var(--text-medium);
        }
        
        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .role-selection {
            margin-bottom: 24px;
        }
        
        .role-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        
        .role-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            background: white;
            color: var(--text-medium);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            font-weight: 500;
            text-decoration: none;
            display: block;
        }
        
        .role-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .alert {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
        }
        
        .alert-error {
            background-color: rgba(173, 80, 87, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
        }
        
        .alert-info {
            background-color: rgba(46, 137, 73, 0.1);
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .test-credentials {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: var(--radius-md);
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 100%;
                min-height: auto;
            }
            
            .left-panel {
                padding: 40px 20px;
            }
            
            .right-panel {
                padding: 40px 20px;
            }
            
            .left-content h1 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .role-buttons {
                flex-direction: column;
            }
            
            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <a href="../index.php" class="logo">
                <div class="logo-image">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <span>CareSync</span>
            </a>
            
            <div class="left-content">
                <h1>Welcome Back</h1>
                <p>Sign in to your CareSync account to manage your clinic operations efficiently.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Access your dashboard</li>
                    <li><i class="fas fa-check-circle"></i> Manage appointments</li>
                    <li><i class="fas fa-check-circle"></i> View patient records</li>
                    <li><i class="fas fa-check-circle"></i> Handle prescriptions</li>
                    <li><i class="fas fa-check-circle"></i> Track billing information</li>
                </ul>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                
               
                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="required">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="../login/forgot_password.php" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
                
                <div class="signup-link">
                    <p>Don't have an account? <a href="../signup/signup.php">Sign up here</a></p>
                </div>
                
                <div class="role-selection">
                    <p style="text-align: center; margin-bottom: 16px; color: var(--text-medium);">Or sign up as:</p>
                    <div class="role-buttons">
                        <a href="../signup/signup.php?role=patient" class="role-btn">Patient</a>
                        <!-- <a href="../signup/signup.php?role=doctor" class="role-btn">Doctor</a>
                        <a href="../signup/signup.php?role=secretary" class="role-btn">Secretary</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation and feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            
            // Allow form to submit normally
            return true;
        });

        // Debug: Check if form is working
        console.log('Login form loaded successfully');
        
        // Auto-fill test credentials for development
        // document.getElementById('email').value = 'roniel@gmail.com';
        // document.getElementById('password').value = 'password123';
    </script>
</body>
</html>