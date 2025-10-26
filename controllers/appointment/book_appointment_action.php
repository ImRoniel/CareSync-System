<?php
// controllers/appointment/book_appointment_action.php

// Include your session management
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/AppointmentController.php';

// Additional role check 
if ($_SESSION['user_role'] !== 'patient') {
    $_SESSION['message'] = "Access denied. Patient role required to book appointments.";
    $_SESSION['message_type'] = 'error';
    header("Location: /CareSync-System/views/patient/Patient_Dashboard1.php");
    exit;
}

// Create controller instance
$controller = new AppointmentController($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->bookAppointment($_POST);
    
    // Set session message
    $_SESSION['message'] = $result['message'];
    $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
    
    // Redirect based on success/failure
    if ($result['success']) {
        // Redirect to dashboard on success
        header("Location: /CareSync-System/views/patient/Patient_Dashboard1.php");
    } else {
        // Redirect back to booking page on failure
        header("Location: /CareSync-System/views/patient/book_appointment.php");
    }
    exit;
} else {
    $_SESSION['message'] = "Invalid request method";
    $_SESSION['message_type'] = 'error';
    header("Location: /CareSync-System/views/patient/Patient_Dashboard1.php");
    exit;
}
?>