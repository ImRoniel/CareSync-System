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


    public function getDoctorAppointments($doctorId) {
        $sql = "SELECT 
                    a.appointment_id,
                    a.patient_id,
                    a.doctor_id,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    a.reason,
                    a.queue_number,
                    p.patient_id,
                    u.name as patient_name,
                    d.doctor_id,
                    du.name as doctor_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.patient_id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users du ON d.user_id = du.id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            
            $stmt->close();
            return $appointments;
        } else {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
    }

    // Optional: If you still want today's appointments separately
    public function getTodaysDoctorAppointments($doctorId) {
        $today = date('Y-m-d');
        $sql = "SELECT ... (same as above) ...
                WHERE a.doctor_id = ? AND a.appointment_date = ?
                ORDER BY a.appointment_time ASC";
        
        return $this->conn->fetchAll($sql, [$doctorId, $today]);
    }

    public function updateAppointmentStatusDoctor($appointmentId, $status) {
        $sql = "UPDATE appointments SET status = ?, updated_at = NOW() WHERE appointment_id = ?";
        return $this->conn->execute($sql, [$status, $appointmentId]);
    }

    public function updatePatientAppointment($patientId){
        
    }

    public function checkInAppointment($appointment_id, $secretary_id) {
        try {
            // First, get the secretary's assigned doctor
            $secretaryQuery = "SELECT assigned_doctor_id FROM secretaries WHERE secretary_id = ?";
            $stmt = $this->conn->prepare($secretaryQuery);
            $stmt->bind_param("i", $secretary_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $secretary = $result->fetch_assoc();
            $stmt->close();
            
            if (!$secretary || !$secretary['assigned_doctor_id']) {
                return ['success' => false, 'message' => 'Secretary not assigned to any doctor'];
            }
            
            $assigned_doctor_id = $secretary['assigned_doctor_id'];
            
            // Verify the appointment belongs to the secretary's assigned doctor
            // REMOVED the status check to see the appointment regardless of status
            $verifyQuery = "
                SELECT appointment_id, status 
                FROM appointments 
                WHERE appointment_id = ? 
                AND doctor_id = ?
            ";
            
            $stmt = $this->conn->prepare($verifyQuery);
            $stmt->bind_param("ii", $appointment_id, $assigned_doctor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $appointment = $result->fetch_assoc();
            $stmt->close();
            
            if (!$appointment) {
                return ['success' => false, 'message' => 'Appointment not found or not assigned to your doctor'];
            }

            // Check the current status and handle accordingly
            $current_status = $appointment['status'];
            
            if ($current_status === 'cancelled') {
                // If cancelled, change to approved
                $new_status = 'approved';
                $message = 'Cancelled appointment has been approved';
            } elseif ($current_status === 'pending') {
                // If pending, change to approved
                $new_status = 'approved';
                $message = 'Appointment checked in successfully';
            } elseif ($current_status === 'approved') {
                return ['success' => false, 'message' => 'Appointment is already approved'];
            } elseif ($current_status === 'completed') {
                return ['success' => false, 'message' => 'Appointment is already completed'];
            } else {
                return ['success' => false, 'message' => 'Unknown appointment status: ' . $current_status];
            }

            // Update appointment status
            $updateQuery = "
                UPDATE appointments 
                SET status = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE appointment_id = ?
            ";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("si", $new_status, $appointment_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                return ['success' => true, 'message' => $message];
            } else {
                return ['success' => false, 'message' => 'Failed to update appointment'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function rejectAppointment($appointment_id, $secretary_id) {
        try {
            // First, get the secretary's assigned doctor
            $secretaryQuery = "SELECT assigned_doctor_id FROM secretaries WHERE secretary_id = ?";
            $stmt = $this->conn->prepare($secretaryQuery);
            $stmt->bind_param("i", $secretary_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $secretary = $result->fetch_assoc();
            $stmt->close();
            
            if (!$secretary || !$secretary['assigned_doctor_id']) {
                return ['success' => false, 'message' => 'Secretary not assigned to any doctor'];
            }
            
            $assigned_doctor_id = $secretary['assigned_doctor_id'];
            
            // Verify the appointment belongs to the secretary's assigned doctor
            // REMOVED the status check
            $verifyQuery = "
                SELECT appointment_id, status 
                FROM appointments 
                WHERE appointment_id = ? 
                AND doctor_id = ?
            ";
            
            $stmt = $this->conn->prepare($verifyQuery);
            $stmt->bind_param("ii", $appointment_id, $assigned_doctor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $appointment = $result->fetch_assoc();
            $stmt->close();
            
            if (!$appointment) {
                return ['success' => false, 'message' => 'Appointment not found or not assigned to your doctor'];
            }

            // Check the current status
            $current_status = $appointment['status'];
            
            if ($current_status === 'cancelled') {
                return ['success' => false, 'message' => 'Appointment is already cancelled'];
            } elseif ($current_status === 'completed') {
                return ['success' => false, 'message' => 'Cannot reject a completed appointment'];
            }

            // Update appointment status to cancelled
            $updateQuery = "
                UPDATE appointments 
                SET status = 'cancelled', 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE appointment_id = ?
            ";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("i", $appointment_id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                return ['success' => true, 'message' => 'Appointment rejected successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to reject appointment'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    // Get appointment requests for secretary (pending appointments)
    public function getAppointmentRequestsForSecretary($secretary_id) {
        try {
            $query = "
                SELECT 
                    a.appointment_id,
                    a.patient_id,
                    u.name as patient_name,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    a.queue_number,
                    a.reason
                FROM appointments a
                INNER JOIN patients p ON a.patient_id = p.patient_id
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN doctors d ON a.doctor_id = d.doctor_id
                INNER JOIN secretaries s ON d.doctor_id = s.assigned_doctor_id
                WHERE s.secretary_id = ?
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $secretary_id);
            var_dump($secretary_id);    
            $stmt->execute();
            $result = $stmt->get_result();
            $appointments = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            return $appointments;
            
        } catch (Exception $e) {
            return [];
        }
    }

    // Get today's appointments for doctor (approved appointments that secretary checked in)
    public function getTodaysAppointmentsForDoctor($doctor_id) {
        try {
            $query = "
                    SELECT 
                        a.appointment_id,
                        a.patient_id,
                        u.name as patient_name,
                        a.appointment_date,
                        a.appointment_time,
                        a.status,
                        a.queue_number,
                        a.reason
                    FROM appointments a
                    INNER JOIN patients p ON a.patient_id = p.patient_id
                    INNER JOIN users u ON p.user_id = u.id
                    WHERE a.doctor_id = ?
                    AND a.appointment_date = CURDATE()
                    OR a.status IN ('approved', 'completed', 'cancelled')
                    ORDER BY a.queue_number ASC, a.appointment_time ASC
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $doctor_id);
            var_dump($doctor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $appointments = $result->fetch_all(MYSQLI_ASSOC);
            var_dump($stmt->error);
            var_dump($result);
            $stmt->close();
            
            return $appointments;
            
        } catch (Exception $e) {
            return [];
        }
    }

    private function logActivity($appointment_id, $secretary_id, $action) {
        try {
            // Get actor_user_id from secretaries table
            $userQuery = "SELECT user_id FROM secretaries WHERE secretary_id = ?";
            $stmt = $this->conn->prepare($userQuery);
            $stmt->bind_param("i", $secretary_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $secretary = $result->fetch_assoc();
            $stmt->close();
            
            if ($secretary) {
                $actor_user_id = $secretary['user_id'];
                $message = $action === 'checked_in' ? 'Appointment checked in by secretary' : 'Appointment rejected by secretary';
                
                $activityQuery = "
                    INSERT INTO activity_logs 
                    (actor_user_id, secretary_id, appointment_id, activity_type, activity_message, created_at)
                    VALUES (?, ?, ?, 'appointment', ?, CURRENT_TIMESTAMP)
                ";
                
                $stmt = $this->conn->prepare($activityQuery);
                $stmt->bind_param("iiis", $actor_user_id, $secretary_id, $appointment_id, $message);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    public function getPendingAppointmentsForSecretary($secretary_id) {
    try {
        // First, get the secretary's assigned doctor
        $secretaryQuery = "SELECT assigned_doctor_id FROM secretaries WHERE secretary_id = ?";
        $stmt = $this->conn->prepare($secretaryQuery);
        $stmt->bind_param("i", $secretary_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $secretary = $result->fetch_assoc();
        $stmt->close();
        
        if (!$secretary || !$secretary['assigned_doctor_id']) {
            return [];
        }
        
        $assigned_doctor_id = $secretary['assigned_doctor_id'];
        
        // Only get PENDING appointments
        $query = "
            SELECT 
                a.appointment_id,
                a.patient_id,
                u.name as patient_name,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.queue_number,
                a.reason
            FROM appointments a
            INNER JOIN patients p ON a.patient_id = p.patient_id
            INNER JOIN users u ON p.user_id = u.id
            WHERE a.doctor_id = ?
            or a.status = 'pending'  
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $assigned_doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $appointments;
        
    } catch (Exception $e) {
        return [];
    }
}

}
?>