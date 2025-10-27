<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all users (with optional search filter)
    public function getAllUsers() {
        $sql = "SELECT id, name, email, role, created_at FROM users";
        $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

      public function deleteUserById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // public function searchSecretary(){
    //     $sql = "SELECT * FROM ";
    // }


    /**
     * Get all users with their role-specific information
     */
    public function getAllUsersWithDetails() {
        $sql = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.role, 
                    u.is_active, 
                    u.created_at,
                    COALESCE(d.phone, p.phone, s.phone, a.phone) as phone,
                    COALESCE(d.specialization, a.department, s.department) as department,
                    COALESCE(a.access_level, 'user') as access_level
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id AND u.role = 'doctor'
                LEFT JOIN patients p ON u.id = p.user_id AND u.role = 'patient'
                LEFT JOIN secretaries s ON u.id = s.user_id AND u.role = 'secretary'
                LEFT JOIN admins a ON u.id = a.user_id AND u.role = 'admin'
                ORDER BY u.created_at DESC";
        
        $result = $this->conn->query($sql);
        $users = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }


    /**
     * Get user by ID with complete information including role-specific data
     */
    public function getUserById($userId) {
        // If it's the static admin (user_id = 0)
        if ($userId == 0) {
            return [
                'id' => 0,
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'phone' => 'N/A',
                'department' => 'System Administration',
                'access_level' => 'super_admin'
            ];
        }

        // For database users
        $sql = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.role,
                    COALESCE(d.phone, p.phone, s.phone, a.phone) as phone,
                    COALESCE(d.specialization, a.department, s.department) as department,
                    COALESCE(a.access_level, 'user') as access_level
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id AND u.role = 'doctor'
                LEFT JOIN patients p ON u.id = p.user_id AND u.role = 'patient'
                LEFT JOIN secretaries s ON u.id = s.user_id AND u.role = 'secretary'
                LEFT JOIN admins a ON u.id = a.user_id AND u.role = 'admin'
                WHERE u.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user;
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $sql = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.role,
                    COALESCE(d.phone, p.phone, s.phone, a.phone) as phone,
                    COALESCE(d.specialization, a.department, s.department) as department,
                    COALESCE(a.access_level, 'user') as access_level
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id AND u.role = 'doctor'
                LEFT JOIN patients p ON u.id = p.user_id AND u.role = 'patient'
                LEFT JOIN secretaries s ON u.id = s.user_id AND u.role = 'secretary'
                LEFT JOIN admins a ON u.id = a.user_id AND u.role = 'admin'
                WHERE u.email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user;
    }
            
}
?>
