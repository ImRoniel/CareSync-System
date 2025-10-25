<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/AppointmentsModel.php';


class AppointmentController {
    private $model;

    public function __construct($conn) {
        $this->model = new appointmentsModel($conn);
    }

    // public function ShowAppointmets(){
    //     return $this->model->getAllApointments();
    // }
    public function getTodayAppointments() {
        return $this->model->getTodayAppointmentsCount();
    }
}
?>
