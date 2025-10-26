<?php
// controllers/appointment/book_appointment_action.php

// Include your session management FIRST
require_once __DIR__ . '/../auth/session.php';

require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/AppointmentController.php';

// Additional role check (since session.php doesn't check roles specifically)
if ($_SESSION['user_role'] !== 'patient') {
    $_SESSION['message'] = "Access denied. Patient role required to book appointments.";
    $_SESSION['message_type'] = 'error';
    header("Location: /CareSync-System/book_appointment.php");
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
    
    // Redirect back to booking page
    header("Location: /CareSync-System/book_appointment.php");
    exit;
} else {
    $_SESSION['message'] = "Invalid request method";
    $_SESSION['message_type'] = 'error';
    header("Location: /CareSync-System/book_appointment.php");
    exit;
}
?>