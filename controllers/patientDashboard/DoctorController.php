<?php

require_once "../../model/patient/DoctorModel.php";

class DoctorController {
    private $doctorModel;

    public function __construct($conn) {
        
    }

    public function listDoctors() {
        return $this->doctorModel->getAllDoctors();
    }
}

// Instantiate controller and get doctors
$controller = new DoctorController($conn);
$doctors = $controller->listDoctors();
?>