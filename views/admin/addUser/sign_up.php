<?php
$config_path = __DIR__ . '/../../../config/db_connect.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    // Debug information
    echo "Trying to include: " . $config_path . "<br>";
    echo "Current directory: " . __DIR__ . "<br>";
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
    if ($user_role == 'admin') {
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $employment_date = trim($_POST['employment_date'] ?? '');
        $access_level = trim($_POST['access_level'] ?? 'admin');
    }
    elseif ($user_role == 'patient') {
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $blood_type = trim($_POST['blood_type'] ?? '');
        $emergency_contact_name = trim($_POST['emergency_contact_name'] ?? '');
        $emergency_contact_phone = trim($_POST['emergency_contact_phone'] ?? '');
        $medical_history = trim($_POST['medical_history'] ?? '');
        
        if (empty($age)) $errors[] = "Age is required";
        if (!is_numeric($age) || $age < 0 || $age > 120) $errors[] = "Age must be between 0 and 120";
        if (empty($gender)) $errors[] = "Gender is required";
    }
    elseif ($user_role == 'doctor') {
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $license_no = trim($_POST['license_no'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $years_experience = trim($_POST['years_experience'] ?? '');
        $clinic_room = trim($_POST['clinic_room'] ?? '');
        
        if (empty($license_no)) $errors[] = "License number is required";
    }
    elseif ($user_role == 'secretary') {
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $employment_date = trim($_POST['employment_date'] ?? '');
        $assigned_doctor_id = trim($_POST['assigned_doctor_id'] ?? '');
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
                if ($user_role == 'admin') {
                    $stmt2 = $conn->prepare("INSERT INTO admins (user_id, phone, address, department, employment_date, access_level) VALUES (?, ?, ?, ?, ?, ?)");
                    $phone = !empty($phone) ? $phone : NULL;
                    $address = !empty($address) ? $address : NULL;
                    $department = !empty($department) ? $department : NULL;
                    $employment_date = !empty($employment_date) ? $employment_date : NULL;
                    $access_level = !empty($access_level) ? $access_level : 'admin';
                    
                    $stmt2->bind_param("isssss", $user_id, $phone, $address, $department, $employment_date, $access_level);
                    $stmt2->execute();
                    $stmt2->close();
                }
                elseif ($user_role == 'patient') {
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
                $success = "User created successfully!";
                
                // Redirect to dashboard after 2 seconds
                header("Refresh: 2; url=../Admin_Dashboard1.php");
                
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
    <title>Add New User - CareSync</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .back-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .role-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .form-container {
            padding: 40px;
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
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
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
        
        .info-box {
            background-color: rgba(46, 137, 73, 0.1);
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid var(--primary);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="role_selection.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            <h1>Add New User</h1>
            <div class="role-badge"><?php echo ucfirst($role); ?></div>
        </div>
        
        <div class="form-container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <p><i class="fas fa-check-circle"></i> <?php echo $success; ?> Redirecting to dashboard...</p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="role" value="<?php echo $role; ?>">
                
                <div class="form-section">
                    <h3 class="section-title">Account Information</h3>
                    
                    <div class="form-group">
                        <label for="name" class="required">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter full name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="required">Password</label>
                            <input type="password" id="password" name="password" placeholder="Create password (min. 8 characters)" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="required">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>
                </div>
                
                <?php if ($role == 'admin'): ?>
                <div class="form-section">
                    <h3 class="section-title">Administrator Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter phone number" value="<?php echo isset($phone) ? $phone : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" id="department" name="department" placeholder="Enter department" value="<?php echo isset($department) ? $department : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employment_date">Employment Date</label>
                            <input type="date" id="employment_date" name="employment_date" value="<?php echo isset($employment_date) ? $employment_date : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="access_level">Access Level</label>
                            <select id="access_level" name="access_level">
                                <option value="admin" <?php echo (isset($access_level) && $access_level == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="super_admin" <?php echo (isset($access_level) && $access_level == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                                <option value="moderator" <?php echo (isset($access_level) && $access_level == 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Enter address"><?php echo isset($address) ? $address : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <strong>Administrator Access:</strong> This user will have full system access and management privileges.
                            <br><small>Super Admin has the highest level of access, followed by Admin, then Moderator.</small>
                        </div>
                    </div>
                </div>
                <?php elseif ($role == 'patient'): ?>
                <!-- Patient form section remains the same -->
                <div class="form-section">
                    <h3 class="section-title">Patient Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter phone number" value="<?php echo isset($phone) ? $phone : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="age" class="required">Age</label>
                            <input type="number" id="age" name="age" placeholder="Enter age" min="0" max="120" value="<?php echo isset($age) ? $age : ''; ?>" required>
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
                        <textarea id="address" name="address" placeholder="Enter address"><?php echo isset($address) ? $address : ''; ?></textarea>
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
                <!-- Doctor form section remains the same -->
                <?php elseif ($role == 'secretary'): ?>
                <!-- Secretary form section remains the same -->
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add User
                </button>
            </form>
        </div>
    </div>
</body>
</html>