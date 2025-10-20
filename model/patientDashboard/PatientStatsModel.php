<?php
class PatientStatsModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Count upcoming appointments for the patient
    public function countUpcomingAppointments($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total 
            FROM appointments 
            WHERE patient_id = ? AND CONCAT(appointment_date,' ',appointment_time) >= NOW()
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Count active prescriptions
    public function countActivePrescriptions($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total 
            FROM prescriptions 
            WHERE patient_id = ? AND status = 'active'
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Count pending bills
    public function countPendingBills($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total 
            FROM billing
            WHERE patient_id = ? AND status = 'pending'
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Count health records
    public function countHealthRecords($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total 
            FROM health_records 
            WHERE patient_id = ?
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}
