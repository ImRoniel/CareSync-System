<?php
require_once __DIR__ . '/../../model/patientDashboard/BillModel.php';

class BillController {
    private $model;

    public function __construct($conn) {
        $this->model = new BillModel($conn);
    }

    public function showPendingBills($patient_id) {
        return $this->model->getPendingBills($patient_id);
    }
}
