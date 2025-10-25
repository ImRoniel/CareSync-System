<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/PatientModel.php';

class PatientController{
    private $model;

    public function __construct($conn){
        $this->model = new PatientModel($conn);
    }

    public function getPatientData($user_id) {
        return $this->model->getPatientByUserId($user_id);
    }
}
?>
