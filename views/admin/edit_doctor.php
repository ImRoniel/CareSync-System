<?php 
include "../../config/db_connect.php";

// ✅ 1. Validate user ID
if (!isset($_GET['id'])) {
    die("Invalid request. No user ID provided.");
}
$id = intval($_GET['id']);
$message = "";

// ✅ 2. Fetch user data
$sql = "SELECT id, name, email, specializatoin FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

if (!$doctor) {
    die("User not found.");
}

// ✅ 3. Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Update user info in users table
    $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $role, $id);

    if ($stmt->execute()) {
        // ✅ 4. Role synchronization logic
        if ($role === 'Doctor') {
            // If user is a doctor, ensure they exist in doctors table
            $check = $conn->prepare("SELECT * FROM doctors WHERE user_id = ?");
            $check->bind_param("i", $id);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows === 0) {
                $insert = $conn->prepare("INSERT INTO doctors (user_id, specialization) VALUES (?, 'General')");
                $insert->bind_param("i", $id);
                $insert->execute();
                $insert->close();
            }
            $check->close();

        } elseif ($role === 'Secretary') {
            // If user becomes a secretary, remove from doctors table if exists
            $delete = $conn->prepare("DELETE FROM doctors WHERE user_id = ?");
            $delete->bind_param("i", $id);
            $delete->execute();
            $delete->close();
        } elseif ($role === 'Patient') {
            // If user becomes a patient, also remove from doctors table
            $delete = $conn->prepare("DELETE FROM doctors WHERE user_id = ?");
            $delete->bind_param("i", $id);
            $delete->execute();
            $delete->close();
        }

        // ✅ 5. Redirect back to dashboard
        header("Location: /Caresync-System/dashboard/admin_dashboard.php?message=User updated successfully");
        exit;
    } else {
        $message = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Edit User</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2E8949;
            --primary-dark: #245033;
            --primary-light: #dbeafe;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --light: #f8fafc;
            --dark: #1e293b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .edit-container {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        .edit-header {
            background: var(--primary);
            color: white;
            padding: 32px 30px 28px;
            text-align: center;
            position: relative;
        }
        
        .edit-header h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .edit-header p {
            opacity: 0.9;
            font-size: 15px;
            font-weight: 400;
        }
        
        .back-btn {
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-50%) scale(1.08);
        }
        
        .edit-form-container {
            padding: 32px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
            letter-spacing: 0.3px;
        }
        
        .form-input, .form-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--light);
            color: var(--dark);
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            font-size: 16px;
        }
        
        .input-icon .form-input, .input-icon .form-select {
            padding-left: 48px;
        }
        
        .form-actions {
            display: flex;
            gap: 14px;
            margin-top: 32px;
        }
        
        .cancel-btn, .save-btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            flex: 1;
            font-size: 15px;
            letter-spacing: 0.3px;
        }
        
        .cancel-btn {
            background: var(--light);
            color: var(--secondary);
            border: 2px solid var(--border);
        }
        
        .cancel-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .save-btn {
            background: var(--primary);
            color: white;
            border: 2px solid var(--primary);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }
        
        .save-btn:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        }
        
        .message {
            margin: 20px 0;
            padding: 14px 18px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid;
        }
        
        .success {
            background: #ecfdf5;
            color: #065f46;
            border-left-color: #10b981;
        }
        
        .error {
            background: #fef2f2;
            color: #991b1b;
            border-left-color: #ef4444;
        }
        
        .role-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
            background: var(--primary-light);
            color: var(--primary-dark);
        }
        
        .form-select option {
            padding: 12px;
            font-size: 15px;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 600;
            margin: 0 auto 20px;
            border: 4px solid white;
            box-shadow: var(--shadow);
        }
        
        @media (max-width: 576px) {
            body {
                padding: 16px;
            }
            
            .edit-container {
                border-radius: 10px;
            }
            
            .edit-header {
                padding: 24px 20px;
            }
            
            .edit-header h1 {
                font-size: 22px;
            }
            
            .edit-form-container {
                padding: 24px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .back-btn {
                left: 16px;
                width: 36px;
                height: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <a href="/CareSync-System/dashboard/admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Edit User</h1>
            <p>Update user information</p>
        </div>
        
        <div class="edit-form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <i class="fas <?php echo strpos($message, 'Error') !== false ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-input" id="name" name="name" 
                               value="<?php echo htmlspecialchars($user['name']); ?>" required
                               placeholder="Enter full name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-input" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required
                               placeholder="Enter email address">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="role">User Role</label>
                    <div class="input-icon">
                        <i class="fas fa-user-tag"></i>
                        <select class="form-select" id="role" name="role" required>
                            <option value="patient" <?php echo $user['role'] == 'patient' ? 'selected' : ''; ?>>
                                Patient
                            </option>
                            <option value="doctor" <?php echo $user['role'] == 'doctor' ? 'selected' : ''; ?>>
                                Doctor
                            </option>
                            <option value="secretary" <?php echo $user['role'] == 'secretary' ? 'selected' : ''; ?>>
                                Secretary
                            </option>
                        </select>
                    </div>
                    <div class="role-indicator">
                        <i class="fas fa-info-circle"></i>
                        Current role: <?php echo ucfirst($user['role']); ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="/CareSync-System/dashboard/admin_dashboard.php" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Enhanced animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-input, .form-select');
            const saveBtn = document.querySelector('.save-btn');
            
            formElements.forEach(element => {
                element.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                element.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            // Add loading state to save button
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveBtn.disabled = true;
            });
        });
    </script>
</body>
</html>