<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session first
session_start();

// Define the root path
define('ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))));

// Include files with absolute paths
require_once ROOT_PATH . '/config/db_connect.php';
require_once ROOT_PATH . '/model/AppointmentsModel.php';

// Set header first
header('Content-Type: application/json');

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'secretary') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check appointment_id
if (!isset($_POST['appointment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
    exit;
}

$appointment_id = intval($_POST['appointment_id']);
$user_id = $_SESSION['user_id'];

try {
    // Get secretary_id from user_id
    $secretaryQuery = "SELECT secretary_id FROM secretaries WHERE user_id = ?";
    $stmt = $conn->prepare($secretaryQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $secretary = $result->fetch_assoc();
    $stmt->close();
    
    if (!$secretary) {
        throw new Exception('Secretary not found');
    }
    
    $secretary_id = $secretary['secretary_id'];
    
    // Use the AppointmentsModel
    $appointmentModel = new AppointmentsModel($conn);
    $result = $appointmentModel->checkInAppointment($appointment_id, $secretary_id);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>