<?php 
include "../../config/db_connect.php";
require_once "../../controllers/admin/AdminController.php";

// ✅ 1. Validate admin ID
if (!isset($_GET['id'])) {
    die("Invalid request. No admin ID provided.");
}
$id = intval($_GET['id']);
$message = "";

// 2. Create controller and fetch admin data
$adminController = new AdminController($conn);
$admin = $adminController->getAdminById($id);

if (!$admin) {
    die("Admin not found.");
}

// 3. Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = $adminController->updateAdmin($id, $_POST);
    
    if ($result['success']) {
        header("Location: /CareSync-System/views/admin/Admin_Dashboard1.php?message=Admin updated successfully");
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
    <title>CareSync - Edit Administrator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2E8949;
            --primary-dark: #245033;
            --admin-color: #AD5057;
            --admin-light: rgba(173, 80, 87, 0.1);
            --super-admin-color: #8B4513;
            --moderator-color: #6B7280;
        }
        
        .admin-avatar {
            background: var(--admin-color) !important;
        }
        
        .status-super-admin {
            background: rgba(139, 69, 19, 0.1);
            color: var(--super-admin-color);
            border: 1px solid var(--super-admin-color);
        }
    </style>
    <!-- Include your existing edit form styles -->
</head>
<body>
    <!-- Use similar structure to edit_doctor.php but customized for admin -->
    <div class="edit-container">
        <div class="edit-header">
            <a href="/CareSync-System/views/admin/Admin_Dashboard1.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Edit Administrator</h1>
            <p>Update administrator information</p>
        </div>
        
        <div class="edit-form-container">
            <?php if (!empty($message)): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="user-avatar admin-avatar">
                <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
            </div>
            
            <div class="user-info">
                <h3>Administrator Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst($admin['role']); ?></p>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="admin_id" value="<?php echo $id; ?>">
                
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" class="form-input" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" 
                               placeholder="Enter phone number">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="department">Department</label>
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" class="form-input" id="department" name="department" 
                               value="<?php echo htmlspecialchars($admin['department'] ?? ''); ?>" 
                               placeholder="Enter department">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="employment_date">Employment Date</label>
                    <div class="input-icon">
                        <i class="fas fa-calendar"></i>
                        <input type="date" class="form-input" id="employment_date" name="employment_date" 
                               value="<?php echo htmlspecialchars($admin['employment_date'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="access_level">Access Level</label>
                    <div class="input-icon">
                        <i class="fas fa-shield-alt"></i>
                        <select class="form-select" id="access_level" name="access_level">
                            <option value="moderator" <?php echo ($admin['access_level'] ?? '') === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                            <option value="admin" <?php echo ($admin['access_level'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="super_admin" <?php echo ($admin['access_level'] ?? '') === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <div class="input-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <textarea class="form-input" id="address" name="address" 
                                  placeholder="Enter address"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="/Caresync-System/views/admin/Admin_Dashboard1.php" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" name="edit_admin" class="save-btn">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>