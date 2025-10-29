<?php
require_once 'config/db_connect.php';
require_once 'model/AppointmentsModel.php';

// Test the model directly
$appointmentModel = new AppointmentsModel($conn);

// Test with a known appointment_id and secretary_id
$test_appointment_id = 13; // Use an appointment_id that exists
$test_secretary_id = 11;   // Use your secretary_id

echo "Testing checkInAppointment...\n";
$result = $appointmentModel->checkInAppointment($test_appointment_id, $test_secretary_id);
print_r($result);

echo "\nTesting rejectAppointment...\n";
$result = $appointmentModel->rejectAppointment($test_appointment_id, $test_secretary_id);
print_r($result);
?>