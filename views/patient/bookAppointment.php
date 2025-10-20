<?php
session_start();
require_once __DIR__ .  '/../../config/db_connect.php';

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../../views/auth/login.php");
    exit();
}

// Get patient ID from session or database
$patient_id = $_SESSION['patient_id'] ?? null;
if (!$patient_id) {
    // If patient_id is not in session, get it from database
    $controller = new AppointmentController($conn);
    $controller->handleBookAppointment();
    
    $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();
    
    if ($patient) {
        $_SESSION['patient_id'] = $patient['patient_id'];
        $patient_id = $patient['patient_id'];
    }
}

// Fetch available doctors
$doctors = [];
try {
    $controller = new AppointmentController($conn);
    $controller->handleBookAppointment();
    
    $stmt = $conn->prepare("
        SELECT d.doctor_id, u.name, d.specialization, d.clinic_room 
        FROM doctors d 
        JOIN users u ON d.user_id = u.id 
        WHERE u.is_active = 1
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching doctors: " . $e->getMessage());
}
?>