<?php

    
// Include database connection
$config_path = __DIR__ . '/../config/db_connect.php';
if (file_exists($config_path)) {
    require_once $config_path;
    echo "<!-- DEBUG: Database connected successfully. -->";
} else {
    die("<div style='color:red;'>DEBUG ERROR: Database configuration file not found.</div>");
}

$token = $_GET['token'] ?? '';
$message = '';
$message_type = '';
$success = false;

echo "<!-- DEBUG: Page loaded with token: " . htmlspecialchars($token) . " -->";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!-- DEBUG: Reset Password form submitted. -->";
    
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Step 1 — Validation
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = 'error';
        echo "<!-- DEBUG: Passwords do not match. -->";
    } else {
        $newPassword = password_hash($password, PASSWORD_DEFAULT);
        echo "<!-- DEBUG: Password hash generated successfully. -->";

        // Step 2 — Validate token
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        if (!$stmt) {
            echo "<div style='color:red;'>DEBUG ERROR: Failed to prepare token check statement - {$conn->error}</div>";
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<!-- DEBUG: Token query executed. Found rows: {$result->num_rows} -->";

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = strtolower(trim($row['email']));
            echo "<!-- DEBUG: Valid token. Found email: {$email} -->";

            // Step 3 — Update password
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            if (!$update) {
                echo "<div style='color:red;'>DEBUG ERROR: Failed to prepare update - {$conn->error}</div>";
            }
            $update->bind_param("ss", $newPassword, $email);

            if ($update->execute()) {
                echo "<!-- DEBUG: Password updated successfully for user: {$email} -->";

                // Step 4 — Delete token
                $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $delete->bind_param("s", $email);
                $delete->execute();
                echo "<!-- DEBUG: Token deleted from password_resets table. -->";

                $message = "Password reset successfully! You can now log in with your new password.";
                $message_type = 'success';
                $success = true;
            } else {
                $message = "Error updating password. Please try again.";
                $message_type = 'error';
                echo "<div style='color:red;'>DEBUG ERROR: Password update failed - {$update->error}</div>";
            }
        } else {
            $message = "Invalid or expired token.";
            $message_type = 'error';
            echo "<!-- DEBUG: Token not found or expired. -->";
        }
    }

    // Step 5 — Clean expired tokens (optional maintenance)
    $conn->query("DELETE FROM password_resets WHERE expires_at < NOW()");
    echo "<!-- DEBUG: Expired tokens cleanup executed. -->";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CareSync</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E8949;
            --primary-dark: #245033;
            --primary-light: #AD5057;
            --secondary: #CFCFCF;
            --accent: #AD5057;
            --danger: #AD5057;
            --success: #2E8949;
            
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
        
        .password-strength {
            margin-top: 8px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .strength-meter {
            height: 4px;
            flex-grow: 1;
            background-color: var(--secondary);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: var(--transition);
        }
        
        .strength-weak {
            background-color: var(--danger);
            width: 33%;
        }
        
        .strength-medium {
            background-color: #ffa500;
            width: 66%;
        }
        
        .strength-strong {
            background-color: var(--success);
            width: 100%;
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
        
        .alert-success {
            background-color: rgba(46, 137, 73, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .success-container {
            text-align: center;
            padding: 20px;
        }
        
        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
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
                <h1>Create New Password</h1>
                <p>Choose a strong, secure password to protect your CareSync account.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Secure password requirements</li>
                    <li><i class="fas fa-check-circle"></i> Instant account access</li>
                    <li><i class="fas fa-check-circle"></i> Protected health information</li>
                </ul>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="form-container">
                <?php if ($success): ?>
                    <div class="success-container">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="form-header">
                            <h2>Password Reset</h2>
                            <p>Your password has been successfully reset</p>
                        </div>
                        
                        <div class="alert alert-success">
                            <?php echo $message; ?>
                        </div>
                        
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Return to Login
                        </a>
                    </div>
                <?php else: ?>
                    <div class="form-header">
                        <h2>Reset Password</h2>
                        <p>Create a new password for your account</p>
                    </div>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="resetPasswordForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label for="password" class="required">New Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your new password" required minlength="8">
                            <div class="password-strength">
                                <span>Strength:</span>
                                <div class="strength-meter">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <span id="strengthText">-</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="required">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                            <div id="passwordMatch" style="margin-top: 8px; font-size: 0.875rem;"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-key"></i>
                            Reset Password
                        </button>
                    </form>
                    
                    <div class="back-link">
                        <p>Remember your password? <a href="login.php">Back to login</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength++;
            
            // Check for lowercase letters
            if (/[a-z]/.test(password)) strength++;
            
            // Check for uppercase letters
            if (/[A-Z]/.test(password)) strength++;
            
            // Check for numbers
            if (/[0-9]/.test(password)) strength++;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update strength meter
            switch(strength) {
                case 0:
                case 1:
                    strengthFill.className = 'strength-fill strength-weak';
                    strengthText.textContent = 'Weak';
                    strengthText.style.color = 'var(--danger)';
                    break;
                case 2:
                case 3:
                    strengthFill.className = 'strength-fill strength-medium';
                    strengthText.textContent = 'Medium';
                    strengthText.style.color = '#ffa500';
                    break;
                case 4:
                case 5:
                    strengthFill.className = 'strength-fill strength-strong';
                    strengthText.textContent = 'Strong';
                    strengthText.style.color = 'var(--success)';
                    break;
            }
            
            // Check password match
            checkPasswordMatch();
        });
        
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword === '') {
                passwordMatch.textContent = '';
                passwordMatch.style.color = '';
            } else if (password === confirmPassword) {
                passwordMatch.textContent = 'Passwords match';
                passwordMatch.style.color = 'var(--success)';
            } else {
                passwordMatch.textContent = 'Passwords do not match';
                passwordMatch.style.color = 'var(--danger)';
            }
        }
        
        // Form validation and feedback
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!password || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting Password...';
            
            // Allow form to submit normally
            return true;
        });
    </script>
</body>
</html>