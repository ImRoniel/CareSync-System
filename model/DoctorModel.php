<?php
class DoctorModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all doctors with their user info
    public function getAllDoctors() {
        $sql = "
            SELECT d.user_id, u.name AS doctor_name, d.specialization, u.email
            FROM doctors d
            JOIN users u ON d.user_id = u.id
        ";

        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}
?>
