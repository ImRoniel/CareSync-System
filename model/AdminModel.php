<?php

//class for all function in admin dashboard
class AdminModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
//get total users from the database tabled users
    public function getTotalUsers() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        return $result->fetch_assoc()['total'] ?? 0;
    }
//get total doctors from the database tabled doctors
    public function getTotalDoctors() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'doctor'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getTotalSecretaries() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'secretary'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getTotalPatients() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'patient'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getAppointmentsToday() {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE DATE(created_at) = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'] ?? 0;
    }
}
?>
