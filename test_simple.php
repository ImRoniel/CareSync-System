<?php
// test_simple.php
session_start();
require_once 'config/db_connect.php';
require_once 'model/AppointmentsModel.php';

// Set session for testing (remove this after testing)
$_SESSION['user_id'] = 13; // Use one of your patient user IDs
$_SESSION['user_role'] = 'patient';

echo "<h1>Simple Doctor Retrieval Test</h1>";

// Create model instance
$model = new AppointmentsModel($conn);

// Test getting doctors
echo "<h2>Testing getAllDoctors()</h2>";
$doctors = $model->getAllDoctors();

echo "<p>Number of doctors found: " . count($doctors) . "</p>";

if (!empty($doctors)) {
    echo "<h3>Doctors List:</h3>";
    echo "<ul>";
    foreach ($doctors as $doctor) {
        echo "<li>ID: " . $doctor['doctor_id'] . " - Name: " . $doctor['name'] . " - Specialization: " . $doctor['specialization'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No doctors found. Checking for errors...</p>";
    echo "<p>Last error: " . $conn->error . "</p>";
}

// Test getting patient
echo "<h2>Testing getPatientByUserId()</h2>";
$patient = $model->getPatientByUserId($_SESSION['user_id']);
if ($patient) {
    echo "<p>Patient found: " . $patient['name'] . " (ID: " . $patient['patient_id'] . ")</p>";
} else {
    echo "<p>Patient not found for user ID: " . $_SESSION['user_id'] . "</p>";
}
?>