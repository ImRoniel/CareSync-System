<?php
class appointmentsModel{
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    
    public function getTodayAppointmentsCount() {
        $query = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE DATE(appointment_date) = CURDATE()";
        $result = $this->conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            return $row['total_appointments'];
        }
        return 0;
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