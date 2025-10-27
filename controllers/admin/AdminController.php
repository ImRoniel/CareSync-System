<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/AdminModel.php';

class AdminController {
    private $model;

    public function __construct($conn) {
        $this->model = new AdminModel($conn);
    }

    public function index() {
        return [
            'totalUsers'       => $this->model->getTotalUsers(),
            'totalDoctors'     => $this->model->getTotalDoctors(),
            'totalSecretaries' => $this->model->getTotalSecretaries(),
            'totalPatients'    => $this->model->getTotalPatients(),
            'appointments'     => $this->model->getAppointmentsToday()
        ];
    }


    /**
     * Get all admins
     */
    public function getAllAdmins() {
        return $this->model->getAllAdmins();
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($adminId) {
        return $this->model->getAdminById($adminId);
    }
    
    /**
     * Update admin information
     */
    public function updateAdmin($adminId, $postData) {
        // Validate required fields
        if (empty($adminId)) {
            return ['success' => false, 'message' => 'Admin ID is required'];
        }
        
        // Sanitize data
        $phone = htmlspecialchars(trim($postData['phone'] ?? ''));
        $address = htmlspecialchars(trim($postData['address'] ?? ''));
        $department = htmlspecialchars(trim($postData['department'] ?? ''));
        $employment_date = htmlspecialchars(trim($postData['employment_date'] ?? ''));
        $access_level = htmlspecialchars(trim($postData['access_level'] ?? 'admin'));
        
        // Update admin
        $result = $this->model->updateAdmin($adminId, $phone, $address, $department, $employment_date, $access_level);
        
        if ($result) {
            return ['success' => true, 'message' => 'Admin updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update admin'];
        }
    }
}
?>
