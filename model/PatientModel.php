<?php
class PatientModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getPatientByUserId($user_id) {
        $sql = "
            SELECT 
                u.id, u.name, u.email, u.role,
                p.patient_id, p.phone, p.address, p.age, p.gender, p.blood_type
            FROM users u
            JOIN patients p ON u.id = p.user_id
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