<?php
function getPrescriptionsToday($conn, $doctor_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total_prescriptions
        FROM prescriptions
        WHERE doctor_id = ?
        AND DATE(created_at) = CURDATE()
    ");

    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total_prescriptions'] ?? 0;
}

class PrescriptionModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addPrescription($appointment_id, $doctor_id, $patient_id, $medicine_name, $dosage, $frequency, $duration, $instructions = '') {
        $sql = "INSERT INTO prescriptions (appointment_id, doctor_id, patient_id, medicine_name, dosage, frequency, duration, instructions) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiisssss", $appointment_id, $doctor_id, $patient_id, $medicine_name, $dosage, $frequency, $duration, $instructions);
        
        return $stmt->execute();
    }

    public function getPrescriptionsByPatient($patient_id) {
        $sql = "SELECT p.*, doc.name as doctor_name, doc.specialization,
                       a.appointment_date, a.appointment_time
                FROM prescriptions p
                JOIN doctors d ON p.doctor_id = d.doctor_id
                JOIN users doc ON d.user_id = doc.id
                LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
                WHERE p.patient_id = ?
                ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getPrescriptionsByAppointment($appointment_id) {
        $sql = "SELECT p.*, pat.name as patient_name, doc.name as doctor_name
                FROM prescriptions p
                JOIN patients pt ON p.patient_id = pt.patient_id
                JOIN users pat ON pt.user_id = pat.id
                JOIN doctors d ON p.doctor_id = d.doctor_id
                JOIN users doc ON d.user_id = doc.id
                WHERE p.appointment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getPrescriptionById($prescription_id) {
        $sql = "SELECT p.*, pat.name as patient_name, doc.name as doctor_name,
                       a.appointment_date, a.appointment_time
                FROM prescriptions p
                JOIN patients pt ON p.patient_id = pt.patient_id
                JOIN users pat ON pt.user_id = pat.id
                JOIN doctors d ON p.doctor_id = d.doctor_id
                JOIN users doc ON d.user_id = doc.id
                LEFT JOIN appointments a ON p.appointment_id = a.appointment_id
                WHERE p.prescription_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePrescriptionStatus($prescription_id, $status) {
        $sql = "UPDATE prescriptions SET status = ? WHERE prescription_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $prescription_id);
        return $stmt->execute();
    }
}
?>

