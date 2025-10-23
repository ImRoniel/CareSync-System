<?php
class DoctorModel {
    private $model;

    public function __construct($conn) {
        $this->model = $conn;
    }

    // Fetch all doctors with their user info
    public function getAllDoctors() {
        $sql = "SELECT * 
                FROM users
                CROSS JOIN doctors
                ON users.id = doctors.user_id";
            $stmt = $this->model->query($sql);
        $result = $stmt;
        return $result;
    }
}
?>
