<?php
class PrescriptionModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get active prescriptions for a patient
    public function getActivePrescriptions($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT p.prescription_id, u.name AS doctor_name, p.date_prescribed, 
                   p.medication, p.dosage, p.instructions, p.refills, p.expires
            FROM prescriptions p
            INNER JOIN doctors d ON p.doctor_id = d.doctor_id
            INNER JOIN users u ON d.doctor_id = u.id
            WHERE p.patient_id = ? AND p.status = 'active'
            ORDER BY p.date_prescribed DESC
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }

        return $prescriptions;
    }
}
