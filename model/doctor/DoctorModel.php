<?php
// model/doctor/DoctorModel.php
class DoctorModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 🗂 Fetch doctor info by ID
    public function getDoctorById($id) {
        $sql = "SELECT u.id, u.name, u.email, d.specialization
                FROM users u
                LEFT JOIN doctors d ON u.id = d.user_id
                WHERE u.id = ? AND u.role = 'doctor'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🖋 Update doctor info
    public function updateDoctor($id, $name, $email, $specialization) {
        // Update basic info in users
        $updateUser = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $updateUser->bind_param("ssi", $name, $email, $id);
        $updateUser->execute();
        $updateUser->close();

        // Check if doctor record exists
        $check = $this->conn->prepare("SELECT * FROM doctors WHERE user_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        // Update or insert specialization
        if ($exists) {
            $updateDoc = $this->conn->prepare("UPDATE doctors SET specialization = ? WHERE user_id = ?");
            $updateDoc->bind_param("si", $specialization, $id);
            $updateDoc->execute();
            $updateDoc->close();
        } else {
            $insertDoc = $this->conn->prepare("INSERT INTO doctors (user_id, specialization) VALUES (?, ?)");
            $insertDoc->bind_param("is", $id, $specialization);
            $insertDoc->execute();
            $insertDoc->close();
        }

        return true;
    }
}
?>
