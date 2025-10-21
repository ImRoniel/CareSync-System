<?php
class DoctorModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllDoctors() {
        $sql = "SELECT d.doctor_id, u.name
                FROM doctors d
                INNER JOIN users u ON d.doctor_id = u.id";
        $result = $this->conn->query($sql);

        $doctors = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $doctors[] = $row;
            }
        }

        return $doctors;
    }
}

?>
