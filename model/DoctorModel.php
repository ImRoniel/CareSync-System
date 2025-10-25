<?php
class DoctorModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all doctors with their user info
    public function getAllDoctors() {
        $sql = "SELECT * 
                FROM users
                CROSS JOIN doctors
                ON users.id = doctors.user_id";
            $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

    public function getDoctorByUserId($user_id) {
        $sql = "
            SELECT 
                u.id, u.name, u.email, u.role,
                d.doctor_id, d.phone, d.address, d.license_no, d.specialization, d.years_experience
            FROM users u
            JOIN doctors d ON u.id = d.user_id
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
