<?php
class PatientModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getAllPatient() {
        $sql = "SELECT 
                p.patient_id,
                pu.name AS name,
                pu.email AS email,
                du.name AS doctor_name,
                MAX(a.appointment_date) AS created_at
            FROM patients p
            INNER JOIN users pu ON p.user_id = pu.id
            LEFT JOIN appointments a ON p.patient_id = a.patient_id
            LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
            LEFT JOIN users du ON d.user_id = du.id
            GROUP BY p.patient_id, pu.name, pu.email, du.name;";
            $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

    public function getAppointmentById($appointmentId) {
        $stmt = $this->conn->prepare("
            SELECT 
                a.*,
                p.patient_id,
                CONCAT(u_p.name) as patient_name,
                d.doctor_id,
                CONCAT(u_d.name) as doctor_name
            FROM appointments a
            LEFT JOIN patients p ON p.patient_id = a.patient_id
            LEFT JOIN users u_p ON u_p.id = p.user_id
            LEFT JOIN doctors d ON d.doctor_id = a.doctor_id
            LEFT JOIN users u_d ON u_d.id = d.user_id
            WHERE a.appointment_id = ?
        ");
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();
        $stmt->close();
        
        return $appointment;
    }

    //show the patient data 
    public function getPatientByUserId($user_id) {
        $sql = "
            SELECT patients.*, users.name, users.email, users.role 
                FROM patients 
                JOIN users ON patients.user_id = users.id 
                WHERE patient_id = ?
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

    /**
     * Get patient by ID with user information
     */
    public function getPatientById($patientId) {
        $sql = "SELECT patients.*, users.name, users.email, users.role 
                FROM patients 
                JOIN users ON patients.user_id = users.id 
                WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        $stmt->close();

        //  empty array if no result found
        return $patient ?? [];
    }


    /**
     * Update patient information
     */
    public function updatePatient($patientId, $phone, $address, $age, $gender, $bloodType, $emergencyContactName, $emergencyContactPhone, $medicalHistory) {
        $sql = "UPDATE patients SET 
                phone = ?, 
                address = ?, 
                age = ?, 
                gender = ?, 
                blood_type = ?, 
                emergency_contact_name = ?, 
                emergency_contact_phone = ?, 
                medical_history = ? 
                WHERE patient_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssisssssi", $phone, $address, $age, $gender, $bloodType, $emergencyContactName, $emergencyContactPhone, $medicalHistory, $patientId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }


    public function getPatientByUserId2($user_id) {
        $sql = "
            SELECT 
                u.id, u.name, u.email, u.role,
                p.patient_id, p.phone, p.address, p.age, p.gender, p.blood_type, p.emergency_contact_name, p.emergency_contact_phone, p.medical_history
            FROM users u 
            JOIN patients p ON u.id = p.user_id
            WHERE u.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }



}   
?>