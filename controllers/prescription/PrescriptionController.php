<?php
require_once __DIR__ . '/../../model/prescription/PrescriptionModel.php';

class PrescriptionController {
    private $model;

    public function __construct($db) {
        $this->model = new PrescriptionModel($db);
    }

    public function addPrescription($appointment_id, $doctor_id, $patient_id, $medicine_data) {
        $required = ['medicine_name', 'dosage', 'frequency', 'duration'];
        foreach ($required as $field) {
            if (empty($medicine_data[$field])) {
                return ['success' => false, 'message' => "All medication fields are required"];
            }
        }

        if ($this->model->addPrescription(
            $appointment_id, $doctor_id, $patient_id,
            $medicine_data['medicine_name'],
            $medicine_data['dosage'],
            $medicine_data['frequency'],
            $medicine_data['duration'],
            $medicine_data['instructions'] ?? ''
        )) {
            return ['success' => true, 'message' => 'Prescription added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add prescription'];
        }
    }

    public function getPatientPrescriptions($patient_id) {
        return $this->model->getPrescriptionsByPatient($patient_id);
    }

    public function getAppointmentPrescriptions($appointment_id) {
        return $this->model->getPrescriptionsByAppointment($appointment_id);
    }

    public function getPrescriptionDetails($prescription_id) {
        return $this->model->getPrescriptionById($prescription_id);
    }

    public function updatePrescriptionStatus($prescription_id, $status) {
        $valid_statuses = ['Active', 'Completed', 'Cancelled'];
        
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'message' => 'Invalid prescription status'];
        }

        if ($this->model->updatePrescriptionStatus($prescription_id, $status)) {
            return ['success' => true, 'message' => 'Prescription status updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update prescription status'];
        }
    }
}
?>