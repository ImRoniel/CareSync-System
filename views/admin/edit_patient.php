<?php 
include "../../config/db_connect.php";
require_once "../../controllers/admin/patientController.php";

// 1. Validate patient ID
if (!isset($_GET['id'])) {
    die("Invalid request. No patient ID provided.");
}
$id = intval($_GET['id']);
$message = "";

// 2. Create controller and fetch patient data
$patientController = new PatientController($conn);
$patient = $patientController->getPatientById2($id);

if (!$patient) {
    die("Patient not found.");
}

// 3. Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = $patientController->updatePatient($id, $_POST);
    
    if ($result['success']) {
        header("Location: /CareSync-System/views/admin/Admin_Dashboard1.php?message=Patient updated successfully");
        exit;
    } else {
        $message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Edit Patient</title>
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
            max-width: 600px;
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
            text-decoration: none;
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
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--light);
            color: var(--dark);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
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
            box-shadow: 0 2px 8px rgba(46, 137, 73, 0.2);
        }
        
        .save-btn:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46, 137, 73, 0.3);
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
        
        .user-info {
            background: var(--light);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            border-left: 4px solid var(--primary);
        }
        
        .user-info h3 {
            margin-bottom: 8px;
            color: var(--primary-dark);
            font-size: 16px;
        }
        
        .user-info p {
            margin-bottom: 4px;
            font-size: 14px;
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
            
            .form-row {
                grid-template-columns: 1fr;
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
            <a href="/CareSync-System/views/admin/Admin_Dashboard1.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Edit Patient</h1>
            <p>Update patient information</p>
        </div>
        
        <div class="edit-form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <i class="fas <?php echo strpos($message, 'Error') !== false ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="user-avatar">
                <?php echo strtoupper(substr($patient['name'], 0, 1)); ?>
            </div>
            
            <div class="user-info">
                <h3>User Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst($patient['role']); ?></p>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="patient_id" value="<?php echo $id; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" class="form-input" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($patient['phone'] ?? ''); ?>" 
                                   placeholder="Enter phone number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="age">Age</label>
                        <div class="input-icon">
                            <i class="fas fa-birthday-cake"></i>
                            <input type="number" class="form-input" id="age" name="age" 
                                   value="<?php echo htmlspecialchars($patient['age'] ?? ''); ?>" 
                                   placeholder="Enter age" min="0" max="120" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <div class="input-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" class="form-input" id="address" name="address" 
                               value="<?php echo htmlspecialchars($patient['address'] ?? ''); ?>" 
                               placeholder="Enter address">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <div class="input-icon">
                            <i class="fas fa-venus-mars"></i>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo ($patient['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($patient['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($patient['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="blood_type">Blood Type</label>
                        <div class="input-icon">
                            <i class="fas fa-tint"></i>
                            <select class="form-select" id="blood_type" name="blood_type">
                                <option value="">Select Blood Type</option>
                                <option value="A" <?php echo ($patient['blood_type'] ?? '') === 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo ($patient['blood_type'] ?? '') === 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="AB" <?php echo ($patient['blood_type'] ?? '') === 'AB' ? 'selected' : ''; ?>>AB</option>
                                <option value="O" <?php echo ($patient['blood_type'] ?? '') === 'O' ? 'selected' : ''; ?>>O</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="emergency_contact_name">Emergency Contact Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-input" id="emergency_contact_name" name="emergency_contact_name" 
                                   value="<?php echo htmlspecialchars($patient['emergency_contact_name'] ?? ''); ?>" 
                                   placeholder="Enter emergency contact name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="emergency_contact_phone">Emergency Contact Phone</label>
                        <div class="input-icon">
                            <i class="fas fa-phone-alt"></i>
                            <input type="text" class="form-input" id="emergency_contact_phone" name="emergency_contact_phone" 
                                   value="<?php echo htmlspecialchars($patient['emergency_contact_phone'] ?? ''); ?>" 
                                   placeholder="Enter emergency contact phone">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="medical_history">Medical History</label>
                    <textarea class="form-textarea" id="medical_history" name="medical_history" 
                              placeholder="Enter medical history"><?php echo htmlspecialchars($patient['medical_history'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="/CareSync-System/views/admin/Admin_Dashboard1.php" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" name="edit_patient" class="save-btn">
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
            const formElements = document.querySelectorAll('.form-input, .form-select, .form-textarea');
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