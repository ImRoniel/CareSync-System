<?php
require_once __DIR__ . '../../../config/db_connect.php';
require_once __DIR__ . '../../../model/DoctorModel.php';

class DoctorController {
    private $model;

    public function __construct($conn) {
        $this->model = new DoctorModel($conn);
    }

    // Get all doctors for the view
    public function showDoctors() {
        return $this->model->getAllDoctors();
    }

    //this for showing the data of the doctor that currentlly login 
    public function getDoctorData($user_id) {
        return $this->model->getDoctorByUserId($user_id);
    }
}

?>
