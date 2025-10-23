<?php
class DoctorModel {
    private $model;

    public function __construct($conn) {
        $this->model = $conn;
    }

    // Fetch all doctors with their user info
    public function getAllDoctors() {
        $sql = "
            SELECT doctors.user_id, users.name AS doctor_name, doctors.specialization, users.email
            FROM doctors 
            JOIN users  ON doctors.user_id = users.id
        ";
        $result = $this->model->query($sql);
        return $result;
    }
}
?>
