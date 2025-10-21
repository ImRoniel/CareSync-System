<?php


class DoctorModel {
    private $conn;
    
    //OBJECT INTANCES FOR THE EDITNG BUTTON MODEL FOR DOCTOR
    public function __construct($db) {
        $this->conn = $db;

    }

    public function searchDoctors($search = '') {
        // Add wildcard
        $search = "%" . $search . "%";

        $sql = "SELECT u.id AS user_id, u.name AS doctor_name, u.email, d.specialization 
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id
                WHERE u.role = 'doctor'
                AND (u.name LIKE ? OR u.email LIKE ? OR d.specialization LIKE ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();

        // ✅ This returns a mysqli_result object
        return $stmt->get_result();
    }
    //geting the doctor id in database
    public function getDoctorById($doctor_id) {
        $sql = "SELECT * FROM doctors WHERE doctor_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // udating the function in the databse
    public function updateDoctor($doctor_id, $data) {
        $sql = "UPDATE doctors 
                SET phone = ?, address = ?, license_no = ?, specialization = ?, 
                    years_experience = ?, clinic_room = ?
                WHERE doctor_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssisi",
            $data['phone'],
            $data['address'],
            $data['license_no'],
            $data['specialization'],
            $data['years_experience'],
            $data['clinic_room'],
            $doctor_id
        );
        return $stmt->execute();
    }
    public function getTotalPatients() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'patient'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
    public function getAllDoctors() {
    $sql = "SELECT d.doctor_id, u.name, d.specialization 
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            WHERE u.role = 'doctor'
            ORDER BY u.name ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    return $doctors;
}
    
    
}
?>

<!-- function logic for EDIT DOCTOR BUTTON -->



