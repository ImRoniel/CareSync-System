<?php
function getDoctorAppointments($conn, $doctor_id) {
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.queue_number,
            u.name AS patient_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN users u ON p.user_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date, a.appointment_time
    ");

    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $appointments;
}
?>
<?php
class AppointmentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason = '') {
        // Get the next queue number for the doctor on that date
        $queue_sql = "SELECT COALESCE(MAX(queue_number), 0) + 1 as next_queue 
                     FROM appointments 
                     WHERE doctor_id = ? AND appointment_date = ?";
        $stmt = $this->conn->prepare($queue_sql);
        $stmt->bind_param("is", $doctor_id, $appointment_date);
        $stmt->execute();
        $queue_result = $stmt->get_result();
        $queue_data = $queue_result->fetch_assoc();
        $queue_number = $queue_data['next_queue'];

        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, queue_number, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissis", $patient_id, $doctor_id, $appointment_date, $appointment_time, $queue_number, $reason);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function getAvailableDoctors() {
        $sql = "SELECT d.doctor_id, u.name, d.specialization, d.clinic_room 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                WHERE u.is_active = 1 
                ORDER BY u.name";
        return $this->conn->query($sql);
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
}
?>
