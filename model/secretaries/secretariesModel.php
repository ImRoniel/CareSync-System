<?php
class SecretaryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSecretaryByUserId($userId) {
        $sql = "SELECT 
                    u.name AS secretary_name, 
                    u.email, 
                    u.role,
                    s.phone, 
                    s.address, 
                    s.department 
                FROM secretaries s
                JOIN users u ON s.user_id = u.id
                WHERE s.user_id = ?"; // ✅ 1 placeholder for bind_param

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $userId); // ✅ matches exactly 1 placeholder
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
   
}
?>
<?php
class secretariesModelForAdmin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ✅ Get all or search by name/email
    public function getSecretaries($searchTerm = '') {
        $sql = "SELECT 
                    s.secretary_id,
                    s.user_id,
                    s.phone,
                    s.address,
                    s.department,
                    s.employment_date,
                    u.name AS secretary_name,
                    u.email
                FROM secretaries s
                JOIN users u ON s.user_id = u.id
                WHERE u.role = 'secretary'";

        if (!empty($searchTerm)) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $stmt = $this->conn->prepare($sql);
            $like = "%{$searchTerm}%";
            $stmt->bind_param("ss", $like, $like);
        } else {
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
}
?>

