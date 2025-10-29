<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/PatientModel.php';

class PatientController{
    private $model;

    public function __construct($conn){
        $this->model = new PatientModel($conn);
    }

    public function ShowPatient(){
        return $this->model->getAllPatient();
    }

    public function getPatientData($user_id) {
        return $this->model->getPatientByUserId($user_id);
    }

    /**
     * Get patient by ID
     */
    // public function getPatientById($patientId) {
    //     return $this->model->getPatientByUserId($patientId);
    // }
    
    
    /**
     * Update patient information
     */
    

    // public function getPatientById2($patientId){
    //     return $this->model->getPatientByUserId2($patientId);
    // }

    // public function updatePatient($user_id, $data) {
    // $name = $data['name'];
    // $email = $data['email'];
    // $phone = $data['phone'];
    // $address = $data['address'];
    

    // return $this->model->updatePatientProfile($user_id, $name, $email, $phone, $address);
    // }

    public function getPatientById($patientId) {
        return $this->model->getPatientById($patientId);
    }
    
    /**
     * Update patient information
     */
    public function updatePatient($patientId, $postData) {
        // Validate required fields
        if (empty($patientId)) {
            return ['success' => false, 'message' => 'Patient ID is required'];
        }
        
        // Sanitize data
        $phone = htmlspecialchars(trim($postData['phone'] ?? ''));
        $address = htmlspecialchars(trim($postData['address'] ?? ''));
        $age = intval($postData['age'] ?? 0);
        $gender = htmlspecialchars(trim($postData['gender'] ?? ''));
        $bloodType = htmlspecialchars(trim($postData['blood_type'] ?? ''));
        $emergencyContactName = htmlspecialchars(trim($postData['emergency_contact_name'] ?? ''));
        $emergencyContactPhone = htmlspecialchars(trim($postData['emergency_contact_phone'] ?? ''));
        $medicalHistory = htmlspecialchars(trim($postData['medical_history'] ?? ''));
        
        // Validate age
        if ($age < 0 || $age > 120) {
            return ['success' => false, 'message' => 'Age must be between 0 and 120'];
        }
        
        // Update patient
        $result = $this->model->updatePatient(
            $patientId, 
            $phone, 
            $address, 
            $age, 
            $gender, 
            $bloodType, 
            $emergencyContactName, 
            $emergencyContactPhone, 
            $medicalHistory
        );
        
        if ($result) {
            return ['success' => true, 'message' => 'Patient updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update patient'];
        }
    }
}
?>
