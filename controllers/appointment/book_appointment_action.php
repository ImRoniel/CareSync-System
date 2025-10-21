<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/appointment/AppointmentController.php';

$controller = new AppointmentController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'] ?? '';

    $result = $controller->bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);

    $_SESSION['appointment_feedback'] = $result['message'];
    header('Location: ../../dashboard/Patient_DashBoard1.php');
    exit;
} else {
    echo "Invalid request.";
}
?>
