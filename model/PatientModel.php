<?php
class PatientModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    //show the patient data 
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

        return $result->fetch_assoc(); 
    }

    public function updatePatientProfile($user_id, $name, $email, $phone, $address) {
        // Update users table
        $sql1 = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->bind_param("ssi", $name, $email, $user_id);
        $stmt1->execute();

        // Update patients table
        $sql2 = "UPDATE patients SET phone = ?, address = ? WHERE user_id = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("ssi", $phone, $address, $user_id);
        return $stmt2->execute();
    }

}   
?>