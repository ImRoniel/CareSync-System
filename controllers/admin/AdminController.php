<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/AdminModel.php';

class AdminController {
    private $model;

    public function __construct($conn) {
        $this->model = new AdminModel($conn);
    }

    public function index() {
        return [
            'totalUsers'       => $this->model->getTotalUsers(),
            'totalDoctors'     => $this->model->getTotalDoctors(),
            'totalSecretaries' => $this->model->getTotalSecretaries(),
            'totalPatients'    => $this->model->getTotalPatients(),
            'appointments'     => $this->model->getAppointmentsToday()
        ];
    }
}
?>
