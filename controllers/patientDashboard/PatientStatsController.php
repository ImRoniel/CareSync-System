<?php
require_once __DIR__ . '/../../model/patientDashboard/PatientStatsModel.php';

class PatientStatsController {
    private $model;

    public function __construct($conn) {
        $this->model = new PatientStatsModel($conn);
    }

    public function getStats($patient_id) {
        return [
            'upcomingAppointments' => $this->model->countUpcomingAppointments($patient_id),
            'activePrescriptions' => $this->model->countActivePrescriptions($patient_id),
            'pendingBills' => $this->model->countPendingBills($patient_id),
            'healthRecords' => $this->model->countHealthRecords($patient_id),
        ];
    }
}
