<?php

require_once __DIR__ . '/../../model/appointment/ApointmentModel.php';

class AppointmentController {
    private $model;

    public function __construct($db) {
        $this->model = new AppointmentModel($db);
    }

    public function bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason = '') {
        // Validate date is not in the past
        if (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'Cannot book appointments in the past'];
        }

        // Check if time slot is available
        if (!$this->model->isTimeSlotAvailable($doctor_id, $appointment_date, $appointment_time)) {
            return ['success' => false, 'message' => 'This time slot is already booked. Please choose another time.'];
        }

        $appointment_id = $this->model->bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
        
        if ($appointment_id) {
            return [
                'success' => true, 
                'message' => 'Appointment booked successfully! Your appointment ID is: #' . $appointment_id,
                'appointment_id' => $appointment_id
            ];
        } else {
            return ['success' => false, 'message' => 'Failed to book appointment. Please try again.'];
        }
    }

    public function getAvailableDoctors() {
        return $this->model->getAvailableDoctors();
    }

    public function getPatientAppointments($patient_id) {
        return $this->model->getAppointmentsByPatient($patient_id);
    }

    public function getAppointmentDetails($appointment_id) {
        return $this->model->getAppointmentById($appointment_id);
    }

    public function showUpcomingAppointments($patient_id, $doctor_name = null, $dateTime = null, $type = null, $status = null) {
        return $this->model->upcomingAppointments($patient_id, $doctor_name, $dateTime, $type, $status);
    }

    
}
?>