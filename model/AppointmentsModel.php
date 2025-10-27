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
    public function getTodayAppointmentsCountPatient(){

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

    
    // count the certain appointment for the login patient only MODEL
    public function getUpcomingAppointmentsCount($patientId) {
        error_log("DEBUG: Model called with patient_id: " . $patientId);
        
        $sql = "SELECT COUNT(*) as upcoming_count 
                FROM appointments 
                WHERE patient_id = ? 
                AND (appointment_date > CURDATE() 
                    OR (appointment_date = CURDATE() AND appointment_time > CURTIME()))
                AND status IN ('pending', 'approved')";
        
        error_log("DEBUG: SQL Query: " . $sql);
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return 0;
        }

        $stmt->bind_param("i", $patientId);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return 0;
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        error_log("DEBUG: Query result: " . print_r($row, true));
        error_log("DEBUG: Final count: " . ($row['upcoming_count'] ?? 0));
        
        return $row['upcoming_count'] ?? 0;
    }

    //appointment statistic for a specififc user
    public function getAppointmentStats($patientId) {
        $sql = "SELECT 
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
                FROM appointments 
                WHERE patient_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['pending_count' => 0, 'approved_count' => 0, 'completed_count' => 0, 'cancelled_count' => 0];
        }
        
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    //a method for querying the total appointment in a table
    public function getTotalAppointments() {
        $sql = "SELECT COUNT(*) FROM appointments";
        $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

   public function getAllAppointments() {
        $query = "
            SELECT 
                a.appointment_id,
                p.patient_id,
                CONCAT(u_p.name) as patient_name,
                d.doctor_id,
                CONCAT(u_d.name) as doctor_name,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.reason,
                a.queue_number,
                a.created_at
            FROM appointments a
            LEFT JOIN patients p ON p.patient_id = a.patient_id
            LEFT JOIN users u_p ON u_p.id = p.user_id
            LEFT JOIN doctors d ON d.doctor_id = a.doctor_id
            LEFT JOIN users u_d ON u_d.id = d.user_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
        
        $result = $this->conn->query($query);
        
        $appointments = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
        }
        
        return $appointments;
    }
    
    /**
     * Update appointment status
     */
    public function updateAppointmentStatus($appointmentId, $status) {
        $validStatuses = ['pending', 'approved', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $stmt = $this->conn->prepare("UPDATE appointments SET status = ?, updated_at = NOW() WHERE appointment_id = ?");
        $stmt->bind_param("si", $status, $appointmentId);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Confirm/Approve appointment
     */
    public function confirmAppointment($appointmentId) {
        return $this->updateAppointmentStatus($appointmentId, 'approved');
    }
    
    /**
     * Complete appointment
     */
    public function completeAppointment($appointmentId) {
        return $this->updateAppointmentStatus($appointmentId, 'completed');
    }
    
    public function rescheduleAppointment($appointmentId, $newDate, $newTime) {
    $stmt = $this->conn->prepare("
        UPDATE appointments 
        SET appointment_date = ?, appointment_time = ?, status = 'pending', updated_at = NOW() 
        WHERE appointment_id = ?
    ");
    $stmt->bind_param("ssi", $newDate, $newTime, $appointmentId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    }
    $stmt->close();
    return false;
}
    
    /**
     * Get appointment by ID
     */
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


        /**
     * Cancel an appointment
     */
    public function cancelAppointment($appointmentId) {
        $stmt = $this->conn->prepare("
            UPDATE appointments 
            SET status = 'cancelled', updated_at = NOW() 
            WHERE appointment_id = ?
        ");
        $stmt->bind_param("i", $appointmentId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }


    

}
?>