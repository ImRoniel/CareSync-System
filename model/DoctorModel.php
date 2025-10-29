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
                u.id, 
                u.name, 
                u.email, 
                u.role,
                d.doctor_id, 
                d.phone, 
                d.address, 
                d.license_no, 
                d.specialization, 
                d.years_experience, 
                d.assigned_secretary_id,
                su.name AS secretary_name
            FROM users u    
            JOIN doctors d 
                ON u.id = d.user_id
            LEFT JOIN secretaries s 
                ON d.assigned_secretary_id = s.secretary_id
            LEFT JOIN users su 
                ON s.user_id = su.id
            WHERE u.id = ?;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc(); // Return one patient record
    } 

    public function updateDoctorProfile($user_id, $name, $email, $phone, $address, $department) {
        $sql = "
            UPDATE users 
            SET name = ?, email = ?
            WHERE id = ?;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();

        // update secretary table
        $sql2 = "
            UPDATE doctors
            SET phone = ?, address = ?, department = ?
            WHERE user_id = ?;
        ";

        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("sssi", $phone, $address, $department, $user_id);
        return $stmt2->execute(); // returns true or false
        }

    //a method from appointment
    //purpose: get all doctor that is active
    public function getAllDoctorsActive(){
         $sql = "SELECT doctor_id, name, specialization, email, phone FROM doctors WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //a method from appoimtmet but for admin use only 
    // purrpose: could access doctor 
     public function getAllDoctorsForAdmin() {
        $sql = "SELECT doctor_id, name, specialization, email, phone, status FROM doctors";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
