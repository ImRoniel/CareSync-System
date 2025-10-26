<?php
// book_appointment.php (main entry point)

// Include session management FIRST
require_once __DIR__ . '/controllers/auth/session.php';

// Include required files
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/controllers/appointment/AppointmentController.php';

// Create controller instance
$controller = new AppointmentController($conn);

// Display the booking form
$controller->showBookingForm();
?>