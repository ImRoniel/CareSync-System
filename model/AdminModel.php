<?php

//class for all function in admin dashboard
class AdminModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
//get total users from the database tabled users
    public function getTotalUsers() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        return $result->fetch_assoc()['total'] ?? 0;
    }
//get total doctors from the database tabled doctors
    public function getTotalDoctors() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'doctor'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getTotalSecretaries() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'secretary'");
        return $result->fetch_assoc()['total'] ?? 0;
    }   

    public function getTotalPatients() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'patient'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getAppointmentsToday() {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'] ?? 0;
    }


    
    /**
     * Get admin by user ID
     */
    public function getAdminByUserId($userId) {
        $sql = "SELECT admins.*, users.name, users.email, users.role 
                FROM admins 
                JOIN users ON admins.user_id = users.id 
                WHERE admins.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        return $admin;
    }
    
    /**
     * Get all admins with user information
     */
    public function getAllAdmins() {
        $sql = "SELECT 
                    admins.*, 
                    users.id as user_id,
                    users.name, 
                    users.email, 
                    users.role, 
                    users.created_at
                FROM admins 
                JOIN users ON admins.user_id = users.id 
                ORDER BY users.created_at DESC";
        
        $result = $this->conn->query($sql);
        $admins = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $admins[] = $row;
            }
        }
        
        return $admins;
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($adminId) {
        $sql = "SELECT 
                    admins.*, 
                    users.id as user_id,
                    users.name, 
                    users.email, 
                    users.role
                FROM admins 
                JOIN users ON admins.user_id = users.id 
                WHERE admins.admin_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        return $admin;
    }
    
    /**
     * Update admin information
     */
    public function updateAdmin($adminId, $phone, $address, $department, $employment_date, $access_level) {
        $sql = "UPDATE admins SET 
                phone = ?, 
                address = ?, 
                department = ?, 
                employment_date = ?, 
                access_level = ? 
                WHERE admin_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $phone, $address, $department, $employment_date, $access_level, $adminId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    
}
?>
