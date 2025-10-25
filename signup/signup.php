<?php
// Include database connection with correct path
$config_path = __DIR__ . '/../config/db_connect.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("Database configuration file not found. Please check the file path.");
}

// Initialize variables
$errors = [];
$success = "";

// Check if role is set, otherwise default to patient
$role = isset($_GET['role']) ? $_GET['role'] : 'patient';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_role = $_POST['role'];

    // Common validation
    if (empty($name)) $errors[] = "Full name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($password)) $errors[] = "Password is required";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";

    // Role-specific validation and data
    if ($user_role == 'patient') {
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $age = trim($_POST['age']);
        $gender = trim($_POST['gender']);
        $blood_type = trim($_POST['blood_type']);
        $emergency_contact_name = trim($_POST['emergency_contact_name']);
        $emergency_contact_phone = trim($_POST['emergency_contact_phone']);
        $medical_history = trim($_POST['medical_history']);
        
        if (empty($age)) $errors[] = "Age is required";
        if (!is_numeric($age) || $age < 0 || $age > 120) $errors[] = "Age must be between 0 and 120";
        if (empty($gender)) $errors[] = "Gender is required";
    }
    
    if ($user_role == 'doctor') {
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $license_no = trim($_POST['license_no']);
        $specialization = trim($_POST['specialization']);
        $years_experience = trim($_POST['years_experience']);
        $clinic_room = trim($_POST['clinic_room']);
        
        if (empty($license_no)) $errors[] = "License number is required";
    }

    if ($user_role == 'secretary') {
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $department = trim($_POST['department']);
        $employment_date = trim($_POST['employment_date']);
        $assigned_doctor_id = trim($_POST['assigned_doctor_id']);
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        $stmt->close();
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_role);
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                // Insert role-specific information
                if ($user_role == 'patient') {
                    $stmt2 = $conn->prepare("INSERT INTO patients (user_id, phone, address, age, gender, blood_type, emergency_contact_name, emergency_contact_phone, medical_history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $blood_type = !empty($blood_type) ? $blood_type : NULL;
                    $stmt2->bind_param("ississsss", $user_id, $phone, $address, $age, $gender, $blood_type, $emergency_contact_name, $emergency_contact_phone, $medical_history);
                    $stmt2->execute();
                    $stmt2->close();
                }
                elseif ($user_role == 'doctor') {
                    $stmt2 = $conn->prepare("INSERT INTO doctors (user_id, phone, address, license_no, specialization, years_experience, clinic_room) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $years_experience = !empty($years_experience) ? $years_experience : NULL;
                    $stmt2->bind_param("issssis", $user_id, $phone, $address, $license_no, $specialization, $years_experience, $clinic_room);
                    $stmt2->execute();
                    $stmt2->close();
                }
                elseif ($user_role == 'secretary') {
                    $stmt2 = $conn->prepare("INSERT INTO secretaries (user_id, phone, address, department, employment_date, assigned_doctor_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $phone = !empty($phone) ? $phone : NULL;
                    $address = !empty($address) ? $address : NULL;
                    $department = !empty($department) ? $department : NULL;
                    $employment_date = !empty($employment_date) ? $employment_date : NULL;
                    $assigned_doctor_id = !empty($assigned_doctor_id) ? $assigned_doctor_id : NULL;
                    
                    $stmt2->bind_param("issssi", $user_id, $phone, $address, $department, $employment_date, $assigned_doctor_id);
                    $stmt2->execute();
                    $stmt2->close();
                }
                
                // Commit transaction
                $conn->commit();
                $success = "Account created successfully! You can now login.";
                
                // Clear form
                $name = $email = '';
                if ($user_role == 'patient') {
                    $phone = $address = $age = $gender = $blood_type = $emergency_contact_name = $emergency_contact_phone = $medical_history = '';
                }
                elseif ($user_role == 'doctor') {
                    $phone = $address = $license_no = $specialization = $years_experience = $clinic_room = '';
                }
                elseif ($user_role == 'secretary') {
                    $phone = $address = $department = $employment_date = $assigned_doctor_id = '';
                }
            } else {
                throw new Exception("Error creating user account: " . $conn->error);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CareSync</title>
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
        }
        
        .container {
            display: flex;
            min-height: 100vh;
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
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-white);
        }
        
        .form-container {
            width: 100%;
            max-width: 500px;
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
        
        .role-badge {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: capitalize;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bg-light);
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        
        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--bg-white);
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
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
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            color: var(--text-medium);
        }
        
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
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
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left-panel {
                padding: 40px 20px;
            }
            
            .right-panel {
                padding: 40px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <a href="../index.php" class="logo">
                <img src="../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                <span>CareSync</span>
            </a>
            
            <div class="left-content">
                <h1>Join CareSync Today</h1>
                <p>Create your account and experience seamless clinic management with our comprehensive platform.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Easy appointment scheduling</li>
                    <li><i class="fas fa-check-circle"></i> Digital prescription management</li>
                    <li><i class="fas fa-check-circle"></i> Secure medical records</li>
                    <li><i class="fas fa-check-circle"></i> Real-time communication</li>
                    <li><i class="fas fa-check-circle"></i> Automated billing system</li>
                </ul>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="form-container">
                <div class="form-header">
                    <div class="role-badge">Sign Up as <?php echo ucfirst($role); ?></div>
                    <h2>Create Your Account</h2>
                    <p>Fill in your details to get started</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="role" value="<?php echo $role; ?>">
                    
                    <div class="form-section">
                        <h3 class="section-title">Account Information</h3>
                        
                        <div class="form-group">
                            <label for="name" class="required">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="required">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password" class="required">Password</label>
                                <input type="password" id="password" name="password" placeholder="Create a password (min. 8 characters)" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password" class="required">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($role == 'patient'): ?>
                    <div class="form-section">
                        <h3 class="section-title">Patient Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($phone) ? $phone : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="age" class="required">Age</label>
                                <input type="number" id="age" name="age" placeholder="Enter your age" min="0" max="120" value="<?php echo isset($age) ? $age : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender" class="required">Gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($gender) && $gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($gender) && $gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($gender) && $gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="blood_type">Blood Type</label>
                                <select id="blood_type" name="blood_type">
                                    <option value="">Select Blood Type</option>
                                    <option value="A" <?php echo (isset($blood_type) && $blood_type == 'A') ? 'selected' : ''; ?>>A</option>
                                    <option value="B" <?php echo (isset($blood_type) && $blood_type == 'B') ? 'selected' : ''; ?>>B</option>
                                    <option value="AB" <?php echo (isset($blood_type) && $blood_type == 'AB') ? 'selected' : ''; ?>>AB</option>
                                    <option value="O" <?php echo (isset($blood_type) && $blood_type == 'O') ? 'selected' : ''; ?>>O</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" placeholder="Enter your address"><?php echo isset($address) ? $address : ''; ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="emergency_contact_name">Emergency Contact Name</label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name" placeholder="Emergency contact name" value="<?php echo isset($emergency_contact_name) ? $emergency_contact_name : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="Emergency contact phone" value="<?php echo isset($emergency_contact_phone) ? $emergency_contact_phone : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="medical_history">Medical History</label>
                            <textarea id="medical_history" name="medical_history" placeholder="Any relevant medical history, allergies, or conditions"><?php echo isset($medical_history) ? $medical_history : ''; ?></textarea>
                        </div>
                    </div>
                    <?php elseif ($role == 'doctor'): ?>
                    <div class="form-section">
                        <h3 class="section-title">Doctor Information</h3>
                        
                        <div class="form-group">
                            <label for="license_no" class="required">License Number</label>
                            <input type="text" id="license_no" name="license_no" placeholder="Enter your license number" value="<?php echo isset($license_no) ? $license_no : ''; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($phone) ? $phone : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="specialization">Specialization</label>
                                <input type="text" id="specialization" name="specialization" placeholder="Enter your specialization" value="<?php echo isset($specialization) ? $specialization : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="years_experience">Years of Experience</label>
                                <input type="number" id="years_experience" name="years_experience" placeholder="Years of experience" min="0" value="<?php echo isset($years_experience) ? $years_experience : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="clinic_room">Clinic Room</label>
                                <input type="text" id="clinic_room" name="clinic_room" placeholder="Clinic room number" value="<?php echo isset($clinic_room) ? $clinic_room : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" placeholder="Enter your address"><?php echo isset($address) ? $address : ''; ?></textarea>
                        </div>
                    </div>
                    <?php elseif ($role == 'secretary'): ?>
                    <div class="form-section">
                        <h3 class="section-title">Secretary Information</h3>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($phone) ? $phone : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" placeholder="Enter your department" value="<?php echo isset($department) ? $department : ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="employment_date">Employment Date</label>
                                <input type="date" id="employment_date" name="employment_date" value="<?php echo isset($employment_date) ? $employment_date : ''; ?>">
                            </div>
                            
                            
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" placeholder="Enter your address"><?php echo isset($address) ? $address : ''; ?></textarea>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="../login/login.php">Log in here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>