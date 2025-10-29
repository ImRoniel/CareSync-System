<?php
class PrescriptionModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getPrescriptionsByPatientId($patientId){
        $sql = "
            SELECT 
                p.prescription_id,
                p.appointment_id,
                p.patient_id,
                p.doctor_id,
                p.medicine_name,
                p.dosage,
                p.frequency,
                p.duration,
                p.instructions,
                p.diagnosis,
                p.prescription_text,
                p.status,
                p.created_at,
                COALESCE(u.name,'') AS doctor_name
            FROM prescriptions p
            LEFT JOIN doctors d ON d.doctor_id = p.doctor_id
            LEFT JOIN users u ON u.id = d.user_id
            WHERE p.patient_id = ?
              AND p.status = 'Active'
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        if(!$stmt){
            return [];
        }
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = [];
        while($row = $result->fetch_assoc()){
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }

    public function getAllPrescriptionsByPatientId($patientId){
        $sql = "
            SELECT 
                p.prescription_id,
                p.appointment_id,
                p.patient_id,
                p.doctor_id,
                p.medicine_name,
                p.dosage,
                p.frequency,
                p.duration,
                p.instructions,
                p.diagnosis,
                p.prescription_text,
                p.status,
                p.created_at,
                COALESCE(u.name,'') AS doctor_name
            FROM prescriptions p
            LEFT JOIN doctors d ON d.doctor_id = p.doctor_id
            LEFT JOIN users u ON u.id = d.user_id
            WHERE p.patient_id = ?
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        if(!$stmt){
            return [];
        }
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = [];
        while($row = $result->fetch_assoc()){
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }
}
?>

