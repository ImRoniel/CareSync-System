<?php


// Include database connection
$config_path = __DIR__ . '/../config/db_connect.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("Database configuration file not found. Please check the file path."); //debugger 
}

// dynamic null variable that is storing a text 
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if the email exists in users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Store token
        $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $email, $token, $expires);
        $insert->execute();

        // Show reset link (for testing)
        $message = "A password reset link has been generated. Please check your email or use the link below:";
        $message_type = 'info';
        $reset_link = "reset_password.php?token=$token";
    } else {
        $message = "No account found with that email.";
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CareSync</title>
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
        
        .back-link {
            text-align: center;
            margin-top: 30px;
            color: var(--text-medium);
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
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
        
        .reset-link {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: var(--radius-md);
            border: 1px solid #dee2e6;
            word-break: break-all;
        }
        
        .reset-link a {
            color: var(--primary);
            font-weight: 500;
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
                <h1>Reset Your Password</h1>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Secure password reset</li>
                    <li><i class="fas fa-check-circle"></i> Quick and easy process</li>
                    <li><i class="fas fa-check-circle"></i> Back to your account in minutes</li>
                </ul>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <h2>Forgot Password</h2>
                    <p>Enter your email to reset your password</p>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                    
                    <?php if (isset($reset_link)): ?>
                        <div class="reset-link">
                            <p><strong>For testing purposes:</strong></p>
                            <a href="<?php echo $reset_link; ?>"><?php echo $reset_link; ?></a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <form method="POST" action="" id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Link
                    </button>
                </form>
                
                <div class="back-link">
                    <p>Remember your password? <a href="login.php">Back to login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation and feedback
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            // Allow form to submit normally
            return true;
        });
    </script>
</body>
</html>