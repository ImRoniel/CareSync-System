<?php
class DoctorModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchDoctors($search = '') {
        // Add wildcard
        $search = "%" . $search . "%";

        $sql = "SELECT u.id AS user_id, u.name AS doctor_name, u.email, d.specialization 
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id
                WHERE u.role = 'doctor'
                AND (u.name LIKE ? OR u.email LIKE ? OR d.specialization LIKE ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();

        // ✅ This returns a mysqli_result object
        return $stmt->get_result();
    }
}
?>
