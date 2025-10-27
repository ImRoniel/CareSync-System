<?php
require_once __DIR__ . '/../../model/userModel.php';
require_once __DIR__ . '/../../model/AdminModel.php';

class AuthController {
    private $userModel;
    private $adminModel;
    
    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
        $this->adminModel = new AdminModel($conn);
    }
    
    /**
     * Get current logged-in user information
     */
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Handle static admin
        if ($userId == 0 || (isset($_SESSION['is_static_admin']) && $_SESSION['is_static_admin'])) {
            return [
                'id' => 0,
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'phone' => 'N/A',
                'department' => 'System Administration',
                'access_level' => 'super_admin',
                'is_static_admin' => true
            ];
        }
        
        // Get user from database
        $user = $this->userModel->getUserById($userId);
        
        // If user is admin, get admin-specific data
        if ($user && $user['role'] === 'admin') {
            $admin_data = $this->getAdminData($userId);
            if ($admin_data) {
                $user = array_merge($user, $admin_data);
            }
        }
        
        return $user;
    }
    
    /**
     * Get admin-specific data
     */
    public function getAdminData($userId) {
        return $this->adminModel->getAdminByUserId($userId);
    }
    
    /**
     * Validate user session and redirect if not logged in
     */
    public function validateSession() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: /CareSync-System/login/login.php");
            exit();
        }
        
        // Update session with fresh user data
        $currentUser = $this->getCurrentUser();
        if ($currentUser) {
            $_SESSION['user_name'] = $currentUser['name'];
            $_SESSION['user_email'] = $currentUser['email'];
            $_SESSION['user_role'] = $currentUser['role'];
            
            // Store admin data in session if applicable
            if ($currentUser['role'] === 'admin' && !isset($_SESSION['is_static_admin'])) {
                $_SESSION['admin_data'] = $this->getAdminData($currentUser['id']);
            }
        }
    }
    
    /**
     * Get user display name with role
     */
    public function getUserDisplayName() {
        $user = $this->getCurrentUser();
        if (!$user) return 'Guest';
        
        return $user['name'];
    }
    
    /**
     * Get user role display text
     */
    public function getUserRoleDisplay() {
        $user = $this->getCurrentUser();
        if (!$user) return 'Guest';
        
        switch($user['role']) {
            case 'admin':
                $access_level = $user['access_level'] ?? 'admin';
                if ($user['is_static_admin'] ?? false) {
                    return 'System Administrator (Super Admin)';
                }
                return 'Administrator' . ($access_level === 'super_admin' ? ' (Super Admin)' : '');
            case 'doctor':
                return 'Doctor Account';
            case 'secretary':
                return 'Secretary Account';
            case 'patient':
                return 'Patient Account';
            default:
                return 'User Account';
        }
    }
    
    /**
     * Get user department/role-specific info
     */
    public function getUserDepartment() {
        $user = $this->getCurrentUser();
        if (!$user) return '';
        
        if ($user['role'] === 'admin') {
            return $user['department'] ?? 'System Administration';
        }
        
        return $user['department'] ?? $user['specialization'] ?? 'No Department';
    }
    
    /**
     * Check if current user is static admin
     */
    public function isStaticAdmin() {
        return ($_SESSION['user_id'] == 0 || ($_SESSION['is_static_admin'] ?? false));
    }
}
?>