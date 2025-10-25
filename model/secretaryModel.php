<?php
class SecretaryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSecretaryByUserId($user_id) {
        $sql = "
            SELECT 
                u.id, u.name, u.email, u.role,
                s.secretary_id, s.phone, s.address, s.department, s.employment_date, s.assigned_doctor_id
            FROM users u
            JOIN secretaries s ON u.id = s.user_id
            WHERE u.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc(); // Return one patient record
    }
}
?>
