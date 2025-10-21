<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/patientDashboard/PatientDataModel.php';
class PatientController {
    private $patientModel;

    public function __construct($conn) {
        $this->patientModel = new PatientModel($conn);
        
    }

     public function index() {
        // Default method — load all patients for dashboard
        return $this->patientModel->selectBasicPatientInfo();
    }

    //IF i would need it if i want to fetch a patient b their id 
    public function getPatientById($patientId) {
        $result = $this->patientModel->selectBasicPatientInfo(['p.patient_id' => $patientId]);
        return $result ? $result[0] : null;
    }

}
?>