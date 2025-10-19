<?php
    require_once __DIR__ . '/../../config/db_connect.php';
    require_once __DIR__ . '/../../model/patient/patientModel.php';
//class for patient controller
class PatientController {
    private $model;


    //method constructor to ues patientcontroller 
    public function __construct($mysqli){
        $this->model = new PatientModel($mysqli);
    }

    public function index(){
        return $this->model->getAllPatients();
    }
}
?>