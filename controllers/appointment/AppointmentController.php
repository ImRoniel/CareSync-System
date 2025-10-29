<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/AppointmentsModel.php';
require_once __DIR__ . '/../../model/DoctorModel.php';


class AppointmentController {
     private $model;
     private $appointmentsModel;
    private $doctorModel;
    private $secretary_id;
    
    public function __construct($conn) {
        $this->model = new appointmentsModel($conn);
        $this->appointmentsModel = new AppointmentsModel($conn);
        $this->doctorModel = new DoctorModel($conn);
        $this->secretary_id = $_SESSION['user']['secretary_id'] ?? $_SESSION['secretary_id'] ?? null;
    }
    
     public function getTodayAppointments() {
        return $this->model->getTodayAppointmentsCount();
     }
    
    /**
     * Show the appointment booking form - CLEAN VERSION
     */
    public function showBookingForm() {
        // Your session.php already checks if user is logged in
        // Additional check: verify user is a patient
        if ($_SESSION['user_role'] !== 'patient') {
            $_SESSION['message'] = "Access denied. Patient role required.";
            $_SESSION['message_type'] = 'error';
            header("Location: /CareSync-System/login/login.php");
            exit;
        }
        
        // Get patient ID from session user_id
        $patient = $this->model->getPatientByUserId($_SESSION['user_id']);
        
        // Check if patient profile exists
        if (!$patient) {
            $_SESSION['message'] = "Patient profile not found. Please complete your profile.";
            $_SESSION['message_type'] = 'error';
            header("Location: /CareSync-System/login/login.php");
            exit;
        }
        
        // Get all available doctors
        $doctors = $this->model->getAllDoctors();
        
        // Include the view
        include __DIR__ . '/../../views/patient/book_appointment.php';
    }
    
    /**
     * Handle appointment booking submission
     */
    public function bookAppointment($postData) {
        // Validate required fields
        if (empty($postData['doctor_id']) || empty($postData['appointment_date']) || 
            empty($postData['appointment_time']) || empty($postData['reason'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Sanitize data
        $doctorId = intval($postData['doctor_id']);
        $appointmentDate = $this->sanitizeDate($postData['appointment_date']);
        $appointmentTime = $this->sanitizeTime($postData['appointment_time']);
        $reason = htmlspecialchars(trim($postData['reason']));
        
        // Get patient ID
        $patient = $this->model->getPatientByUserId($_SESSION['user_id']);
        if (!$patient) {
            return ['success' => false, 'message' => 'Patient profile not found'];
        }
        
        $patientId = $patient['patient_id'];
        
        // Validate date not in past
        if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'Cannot book appointment for past dates'];
        }
        
        // Check patient availability
        if (!$this->model->checkPatientAvailability($patientId, $appointmentDate, $appointmentTime)) {
            return ['success' => false, 'message' => 'You already have an appointment at this time'];
        }
        
        // Check doctor availability
        if (!$this->model->checkDoctorAvailability($doctorId, $appointmentDate, $appointmentTime)) {
            return ['success' => false, 'message' => 'Doctor is not available at the selected time'];
        }
        
        // Book appointment
        try {
            $result = $this->model->bookAppointment(
                $patientId, 
                $doctorId, 
                $appointmentDate, 
                $appointmentTime, 
                $reason
            );
            
            if ($result) {
                return ['success' => true, 'message' => 'Appointment booked successfully! Your appointment is pending approval.'];
            } else {
                return ['success' => false, 'message' => 'Failed to book appointment'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    private function sanitizeDate($date) {
        $timestamp = strtotime($date);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : date('Y-m-d');
    }
    
    private function sanitizeTime($time) {
        $timestamp = strtotime($time);
        return $timestamp !== false ? date('H:i:s', $timestamp) : '09:00:00';
    }

    //a controller for the patient count appointment 
    public function getUpcomingAppointmentsCountController($patientId) {
        return $this->model->getUpcomingAppointmentsCount($patientId);
    }

    public function getTotalAppointments(){
        return $this->model->getTotalAppointments();
    }
    
    /**
     * Get all appointments for admin dashboard
     */
    public function getAppointments() {
        return $this->model->getAllAppointments();
    }
    
    /**
     * Handle appointment actions (confirm, cancel, reschedule, complete)
     */
    public function handleAppointmentAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $appointmentId = $_POST['appointment_id'] ?? 0;
            
            if (!$appointmentId) {
                return ['success' => false, 'message' => 'Invalid appointment ID'];
            }
            
            // Verify appointment exists
            $appointment = $this->model->getAppointmentById($appointmentId);
            if (!$appointment) {
                return ['success' => false, 'message' => 'Appointment not found'];
            }
            
            switch ($action) {
                case 'confirm':
                    $result = $this->model->confirmAppointment($appointmentId);
                    $message = $result ? 'Appointment confirmed successfully' : 'Failed to confirm appointment';
                    break;
                    
                case 'cancel':
                    $result = $this->model->cancelAppointment($appointmentId);
                    $message = $result ? 'Appointment cancelled successfully' : 'Failed to cancel appointment';
                    break;
                    
                case 'complete':
                    $result = $this->model->completeAppointment($appointmentId);
                    $message = $result ? 'Appointment marked as completed' : 'Failed to complete appointment';
                    break;
                    
                case 'reschedule':
                    $newDate = $_POST['new_date'] ?? '';
                    $newTime = $_POST['new_time'] ?? '';
                    
                    if (!$newDate || !$newTime) {
                        return ['success' => false, 'message' => 'Date and time are required for rescheduling'];
                    }
                    
                    $result = $this->model->rescheduleAppointment($appointmentId, $newDate, $newTime);
                    $message = $result ? 'Appointment rescheduled successfully' : 'Failed to reschedule appointment';
                    break;
                    
                default:
                    return ['success' => false, 'message' => 'Invalid action'];
            }
            
            return [
                'success' => $result,
                'message' => $message
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid request method'];
    }
    
    /**
     * Get appointment status badge class
     */
    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'approved':
                return 'status-badge status-active';
            case 'pending':
                return 'status-badge status-pending';
            case 'completed':
                return 'status-badge status-completed';
            case 'cancelled':
                return 'status-badge status-cancelled';
            default:
                return 'status-badge status-pending';
        }
    }
    
    /**
     * Get status display text
     */
    public function getStatusDisplayText($status) {
        switch ($status) {
            case 'approved':
                return 'Confirmed';
            case 'pending':
                return 'Pending';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            default:
                return 'Pending';
        }
    }
    
    /**
     * Get action buttons based on status
     */
    public function getActionButtons($appointmentId, $status) {
        $buttons = '';
        
        switch ($status) {
            case 'pending':
                $buttons = '
                    <button class="btn btn-sm btn-primary" onclick="confirmAppointment(' . $appointmentId . ')">Confirm</button>
                    <button class="btn btn-sm btn-danger" onclick="cancelAppointment(' . $appointmentId . ')">Cancel</button>
                ';
                break;
                
            case 'approved':
                $buttons = '
                    <button class="btn btn-sm btn-secondary" onclick="showRescheduleModal(' . $appointmentId . ')">Reschedule</button>
                    <button class="btn btn-sm btn-success" onclick="completeAppointment(' . $appointmentId . ')">Complete</button>
                    <button class="btn btn-sm btn-danger" onclick="cancelAppointment(' . $appointmentId . ')">Cancel</button>
                ';
                break;
                
            case 'completed':
                $buttons = '
                    <span class="text-muted">Completed</span>
                ';
                break;
                
            case 'cancelled':
                $buttons = '
                    <span class="text-muted">Cancelled</span>
                ';
                break;
        }
        
        return $buttons;
    }

    public function showRescheduleForm($appointmentId) {
        $appointment = $this->model->getAppointmentById($appointmentId);
        
        if (!$appointment) {
            header('Location: Admin_Dashboard1.php?error=Appointment not found');
            exit();
        }
        
        return $appointment;
    }
    public function rescheduleAppointment($appointmentId, $newDate, $newTime) {
        if (empty($appointmentId) || empty($newDate) || empty($newTime)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        $result = $this->model->rescheduleAppointment($appointmentId, $newDate, $newTime);
        
        if ($result) {
            return ['success' => true, 'message' => 'Appointment rescheduled successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to reschedule appointment'];
        }
    }

    /**
 * Cancel appointment
 */
    public function cancelAppointment($appointmentId) {
        if (empty($appointmentId)) {
            return ['success' => false, 'message' => 'Appointment ID is required'];
        }
        
        // Verify appointment exists
        $appointment = $this->model->getAppointmentById($appointmentId);
        if (!$appointment) {
            return ['success' => false, 'message' => 'Appointment not found'];
        }
        
        // Check if already cancelled
        if ($appointment['status'] === 'cancelled') {
            return ['success' => false, 'message' => 'Appointment is already cancelled'];
        }
        
        $result = $this->model->cancelAppointment($appointmentId);
        
        if ($result) {
            return ['success' => true, 'message' => 'Appointment cancelled successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to cancel appointment'];
        }
    }


    public function getDoctorAppointments() {
        try {

            include_once __DIR__ . '/../../controllers/auth/session.php';
            
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return ['success' => false, 'message' => 'User not authenticated'];
            }

            $doctor = $this->doctorModel->getDoctorByUserId($userId);
            if (!$doctor) {
                return ['success' => false, 'message' => 'Doctor not found'];
            }

            $appointments = $this->appointmentsModel->getDoctorAppointments($doctor['doctor_id']);
            
            return [
                'success' => true,
                'appointments' => $appointments
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching appointments: ' . $e->getMessage()
            ];
        }
    }

    public function updateAppointmentStatus() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $appointmentId = $input['appointment_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$appointmentId || !$status) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            $validStatuses = ['pending', 'approved', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                return ['success' => false, 'message' => 'Invalid status'];
            }

            $result = $this->appointmentsModel->updateAppointmentStatus($appointmentId, $status);
            
            if ($result) {
                return ['success' => true, 'message' => 'Appointment status updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update appointment status'];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating appointment: ' . $e->getMessage()
            ];
        }
    }
    public function checkInAppointment() {
        header('Content-Type: application/json');
        
        if (!$this->secretary_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
        
        if (!$appointment_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
            return;
        }

        $result = $this->appointmentsModel->checkInAppointment($appointment_id, $this->secretary_id);
        echo json_encode($result);
    }

    public function rejectAppointment() {
        header('Content-Type: application/json');
        
        if (!$this->secretary_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
        
        if (!$appointment_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
            return;
        }

        $result = $this->appointmentsModel->rejectAppointment($appointment_id, $this->secretary_id);
        echo json_encode($result);
    }

    public function getAppointmentRequests() {
        header('Content-Type: application/json');
        
        if (!$this->secretary_id) {
            echo json_encode([]);
            return;
        }

        $appointments = $this->appointmentsModel->getAppointmentRequestsForSecretary($this->secretary_id);
        echo json_encode($appointments);
    }

    

}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $controller = new AppointmentController($conn);
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_todays_appointments':
            echo json_encode($controller->getDoctorAppointments());
            break;
        case 'update_status':
            echo json_encode($controller->updateAppointmentStatus());
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}


?>

