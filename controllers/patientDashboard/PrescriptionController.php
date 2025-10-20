<?php
require_once __DIR__ . '/../../model/patientDashboard/PrescriptionModel.php';

class PrescriptionController {
    private $model;

    public function __construct($conn) {
        $this->model = new PrescriptionModel($conn);
    }

    public function showActivePrescriptions($patient_id) {
        return $this->model->getActivePrescriptions($patient_id);
    }
}
