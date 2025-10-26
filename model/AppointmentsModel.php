<?php
class appointmentsModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    //FOR APPOINTMETN COUNTIGN 
    public function getTodayAppointmentsCount() {
        $query = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE DATE(appointment_date) = CURDATE()";
        $result = $this->conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_appointments'];
        }
        return 0;
    }

    public function getAppointmentsToday() {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'] ?? 0;
    }
     
    public function getAllDoctors() {
        $sql = "SELECT d.doctor_id, u.name, d.specialization, d.clinic_room 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                WHERE d.status = 'active' AND u.is_active = 1";
        
        // Use prepared statement to avoid any issues
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            error_log("Execute failed: " . $this->conn->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Book a new appointment
     */
    public function bookAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reason) {
        // Generate queue number for the day
        $queueNumber = $this->generateQueueNumber($doctorId, $appointmentDate);
        
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, queue_number, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("iisssi", $patientId, $doctorId, $appointmentDate, $appointmentTime, $reason, $queueNumber);
        return $stmt->execute();
    }
    
    /**
     * Generate queue number for the doctor on specific date
     */
    private function generateQueueNumber($doctorId, $date) {
        $sql = "SELECT COUNT(*) as count FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND status != 'cancelled'";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 1;
        }
        
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] ?? 0) + 1;
    }
    
    /**
     * Check if patient already has appointment at same time
     */
    public function checkPatientAvailability($patientId, $date, $time) {
        $sql = "SELECT COUNT(*) as count FROM appointments 
                WHERE patient_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("iss", $patientId, $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] ?? 0) == 0;
    }
    
    /**
     * Check doctor availability
     */
    public function checkDoctorAvailability($doctorId, $date, $time) {
        $sql = "SELECT COUNT(*) as count FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("iss", $doctorId, $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] ?? 0) == 0;
    }
    
    /**
     * Get patient details by user_id
     */
    public function getPatientByUserId($userId) {
        $sql = "SELECT p.patient_id, p.user_id, u.name, p.phone, p.age, p.gender 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>