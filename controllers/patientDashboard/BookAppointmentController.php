<?php
// controllers/appointment/AppointmentController.php

require_once '../../model/appointment/ApointmentModel.php';

class AppointmentController {
    private $appointmentModel;

    public function __construct($conn) {
        try {
            $this->appointmentModel = new AppointmentModel($conn);
        } catch (Exception $e) {
            error_log("AppointmentController construction failed: " . $e->getMessage());
            throw new Exception("Unable to initialize appointment system");
        }
    }

    public function getAvailableDoctors() {
        try {
            return $this->appointmentModel->getAvailableDoctors();
        } catch (Exception $e) {
            error_log("Error getting available doctors: " . $e->getMessage());
            return [];
        }
    }

    public function handleBookAppointment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bookAppointment();
        }
    }

    private function bookAppointment() {
        include '../../controllers/auth/session.php';
        
        try {
            // Validate required fields
            $required_fields = ['patient_id', 'doctor_id', 'appointment_date', 'appointment_time'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $patient_id = (int)$_POST['patient_id'];
            $doctor_id = (int)$_POST['doctor_id'];
            $appointment_date = $_POST['appointment_date'];
            $appointment_time = $_POST['appointment_time'];
            $reason = $_POST['reason'] ?? '';

            // Validate date and time
            $appointment_datetime = $appointment_date . ' ' . $appointment_time;
            if (strtotime($appointment_datetime) < time()) {
                throw new Exception("Cannot book appointment in the past");
            }

            // Book appointment
            $result = $this->appointmentModel->bookAppointment(
                $patient_id, 
                $doctor_id, 
                $appointment_date, 
                $appointment_time, 
                $reason
            );

            if ($result['success']) {
                $_SESSION['success_message'] = "Appointment booked successfully! Your queue number is: " . $result['queue_number'];
                
                // Log activity
                $this->logAppointmentActivity($patient_id, $doctor_id, $result['appointment_id']);
            } else {
                throw new Exception($result['error']);
            }

            header("Location: ../../views/patient/appointments.php");
            exit();

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: ../../views/patient/book_appointment.php");
            exit();
        }
    }

    private function logAppointmentActivity($patient_id, $doctor_id, $appointment_id) {
        global $conn;
        
        try {
            // Get user ID from patient ID
            $stmt = $conn->prepare("SELECT user_id FROM patients WHERE patient_id = ?");
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $patient = $result->fetch_assoc();
            $stmt->close();

            if ($patient) {
                $actor_user_id = $patient['user_id'];
                $activity_message = "Booked a new appointment";

                $stmt = $conn->prepare("
                    INSERT INTO activity_logs 
                    (actor_user_id, doctor_id, patient_id, appointment_id, activity_type, activity_message, created_at) 
                    VALUES (?, ?, ?, ?, 'appointment', ?, NOW())
                ");
                $stmt->bind_param("iiiss", $actor_user_id, $doctor_id, $patient_id, $appointment_id, $activity_message);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
        }
    }
}

// Handle the request if this file is called directly
if (isset($_POST['action']) && $_POST['action'] === 'book_appointment') {
    // Include your database connection
    require_once '../../config/db_connect.php';
    
    $controller = new AppointmentController($conn);
    $controller->handleBookAppointment();
}
?>