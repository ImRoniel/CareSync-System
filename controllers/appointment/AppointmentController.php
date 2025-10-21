<?php
require_once __DIR__ . '/../../model/appointment/ApointmentModel.php';


class AppointmentController {
    private $model;

    public function __construct($db) {
        $this->model = new AppointmentModel($db);
    }

    public function bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason = '') {
        if (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'Cannot book appointments in the past'];
        }

        if (!$this->model->isTimeSlotAvailable($doctor_id, $appointment_date, $appointment_time)) {
            return ['success' => false, 'message' => 'This time slot is already booked. Please choose another time.'];
        }

        $result = $this->model->bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Appointment booked successfully! Your appointment ID is: #' . $result['appointment_id'],
                'appointment_id' => $result['appointment_id']
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to book appointment. Please try again.'];
        }
    }

    public function getAvailableDoctors() {
        return $this->model->getAvailableDoctors();
    }
}
?>
