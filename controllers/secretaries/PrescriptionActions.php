<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../auth/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'secretary') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action !== 'create') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Resolve secretary and assigned doctor
$userId = intval($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

$doctorId = null;
$secQuery = "SELECT s.secretary_id, s.assigned_doctor_id FROM secretaries s INNER JOIN users u ON u.id = s.user_id WHERE u.id = ?";
$secStmt = $conn->prepare($secQuery);
$secStmt->bind_param('i', $userId);
$secStmt->execute();
$secResult = $secStmt->get_result();
$secRow = $secResult->fetch_assoc();
$secStmt->close();

if (!$secRow || empty($secRow['assigned_doctor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Secretary not assigned to a doctor']);
    exit;
}
$doctorId = intval($secRow['assigned_doctor_id']);

// Validate inputs
$appointmentId = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$patientId = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 0;

$medicineName = trim($_POST['medicine_name'] ?? '');
$dosage = trim($_POST['dosage'] ?? '');
$frequency = trim($_POST['frequency'] ?? '');
$duration = trim($_POST['duration'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$diagnosis = trim($_POST['diagnosis'] ?? '');
$prescriptionText = trim($_POST['prescription_text'] ?? '');

if ($appointmentId <= 0 || $patientId <= 0 || $medicineName === '' || $dosage === '') {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Optional: verify appointment belongs to assigned doctor and is completed
$verifyQuery = "SELECT appointment_id FROM appointments WHERE appointment_id = ? AND patient_id = ? AND doctor_id = ? AND status = 'completed'";
$verifyStmt = $conn->prepare($verifyQuery);
$verifyStmt->bind_param('iii', $appointmentId, $patientId, $doctorId);
$verifyStmt->execute();
$verifyRes = $verifyStmt->get_result();
$verifyOk = $verifyRes && $verifyRes->num_rows > 0;
$verifyStmt->close();

if (!$verifyOk) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment for prescription']);
    exit;
}

// Insert prescription
$insert = "
    INSERT INTO prescriptions
        (appointment_id, doctor_id, patient_id, medicine_name, dosage, frequency, duration, instructions, diagnosis, prescription_text, status, created_at)
    VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', NOW())
";

$stmt = $conn->prepare($insert);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    'iiisssssss',
    $appointmentId,
    $doctorId,
    $patientId,
    $medicineName,
    $dosage,
    $frequency,
    $duration,
    $instructions,
    $diagnosis,
    $prescriptionText
);

$ok = $stmt->execute();
$newId = $stmt->insert_id;
$err = $stmt->error;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Failed to create prescription: ' . $err]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Prescription created successfully', 'prescription_id' => $newId]);
exit;
?>


