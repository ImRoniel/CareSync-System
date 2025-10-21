<?php
class AppointmentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    

    public function getAvailableDoctors() {
    $sql = "SELECT d.doctor_id, u.name, d.specialization, d.clinic_room 
            FROM doctors d 
            JOIN users u ON d.user_id = u.id 
            WHERE u.is_active = 1 
            ORDER BY u.name";
    
    $result = $this->conn->query($sql);
    $doctors = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
    
    return $doctors;
} 
    
    // Book appointment in the database and store it on data base
    public function bookAppointment($patient_id, $doctor_id, $date, $time, $reason) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $reason);
            $stmt->execute();
            $appointment_id = $stmt->insert_id;
            $stmt->close();

            // Return success + queue number (for example, appointment ID)
            return ['success' => true, 'appointment_id' => $appointment_id, 'queue_number' => $appointment_id];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    

    public function getAppointmentsByPatient($patient_id) {
        $sql = "SELECT a.*, u.name as doctor_name, d.specialization, d.clinic_room
                FROM appointments a 
                JOIN doctors d ON a.doctor_id = d.doctor_id 
                JOIN users u ON d.user_id = u.id 
                WHERE a.patient_id = ? 
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        return $stmt->get_result();
    }



    public function getAppointmentById($appointment_id) {
        $sql = "SELECT a.*, p.patient_id, p.user_id as patient_user_id, pu.name as patient_name,
                       d.doctor_id, du.name as doctor_name, d.specialization
                FROM appointments a
                JOIN patients p ON a.patient_id = p.patient_id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users du ON d.user_id = du.id
                WHERE a.appointment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    //asking if time is avialabale
    public function isTimeSlotAvailable($doctor_id, $appointment_date, $appointment_time) {
        $sql = "SELECT appointment_id FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? 
                AND status IN ('pending', 'approved')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows === 0;
    }

    //function method for the upcoming appoinments
    public function upcomingAppointments($patient_id, $doctor_name = null, $dateTime = null, $type = null, $status = null) {
        $sql = "SELECT a.appointment_id, u.name AS doctor_name, 
                CONCAT(a.appointment_date, ' - ', a.appointment_time) AS date_time, 
                a.reason, a.status
                FROM appointments a
                INNER JOIN doctors d ON a.doctor_id = d.doctor_id
                INNER JOIN users u ON d.doctor_id = u.id
                WHERE a.patient_id = ?";


        $params = [$patient_id];
        $types = "i";

        //filtering for good understanding
        if ($doctor_name) {
            $sql .= " AND u.name LIKE ?";
            $params[] = "%$doctor_name%";
            $types .= "s";
        }
        if ($dateTime) {
            $sql .= " AND CONCAT(a.appointment_date,' ',a.appointment_time) >= ?";
            $params[] = $dateTime;
            $types .= "s";
        }
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);

        // Bind dynamically
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        return $appointments; // Array of upcoming appointments
    }

}
?>