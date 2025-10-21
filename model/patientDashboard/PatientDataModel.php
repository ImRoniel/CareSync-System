<?php
class PatientModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function selectBasicPatientInfo() {
        $query = "SELECT 
                    p.patient_id,
                    u.name,
                    u.email,
                    u.is_active,
                    p.phone,
                    p.age,
                    p.gender
                  FROM patients p
                  INNER JOIN users u ON p.user_id = u.id
                  ORDER BY u.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Convert to associative array (like fetchAll)
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }

        return $patients;
    }
}
?>