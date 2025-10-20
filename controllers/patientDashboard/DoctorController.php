<?php
require_once "../../config/db_connect.php";
require_once "../../model/patient/DoctorModel.php";

class DoctorController {
    private $doctorModel;

    public function __construct($conn) {
        $this->doctorModel = new DoctorModel($conn);
    }

    public function listDoctors() {
        return $this->doctorModel->getAllDoctors();
    }
}

// Instantiate controller and get doctors
$controller = new DoctorController($conn);
$doctors = $controller->listDoctors();
?>