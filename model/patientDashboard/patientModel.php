<?php
class PatientModel {
    private $conn;

    public function __construct($mysqli) {
        // ✅ Correct assignment (no minus)
        $this->conn = $mysqli;
    }

    public function getAllPatients($search = '') {
        // ✅ Add wildcard for searching
        $search = '%' . $search . '%';

        // ✅ Fixed SQL syntax
        $sql = "SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.created_at,
                    d.name AS doctor_name
                FROM users AS u
                LEFT JOIN patients p ON u.id = p.user_id
                LEFT JOIN users d ON p.assign_doctor_id = d.id
                WHERE u.role = 'patient'
                AND (u.name LIKE ? OR u.email LIKE ? OR p.assign_doctor_id LIKE ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>


