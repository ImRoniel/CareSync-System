<?php
// controllers/admin/DoctorController.php
require_once __DIR__ . '/../../model/doctor/DoctorModel.php';

class DoctorController {
    private $model;

    public function __construct($conn) {
        $this->model = new DoctorModel($conn);
    }

    // Get a doctor
    public function getDoctor($id) {
        return $this->model->getDoctorById($id);
    }

    // Update a doctor
    public function updateDoctor($id, $name, $email, $specialization) {
        return $this->model->updateDoctor($id, $name, $email, $specialization);
    }
}
?>
