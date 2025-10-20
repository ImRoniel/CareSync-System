<?php
// require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/patientDashboard/patientModel.php';

class PatientController {
    private $model;

    // ✅ Constructor: initializes the model using the shared DB connection
    public function __construct($mysqli) {
        $this->model = new PatientModel($mysqli);
    }

    // ✅ Fetch all patients (with optional search)
    public function index($search = '') {
        return $this->model->getAllPatients($search);
    }
}
?>
