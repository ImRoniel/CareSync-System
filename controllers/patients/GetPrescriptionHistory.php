<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/PatientModel.php';
require_once __DIR__ . '/../../model/PrescriptionModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'patient') {
    echo json_encode([]);
    exit;
}

try {
    $userId = intval($_SESSION['user_id']);
    $patientModel = new PatientModel($conn);
    $prescriptionModel = new PrescriptionModel($conn);

    $patient = $patientModel->getPatientByUserId($userId);
    $patientId = $patient['patient_id'] ?? null;
    if (!$patientId) {
        echo json_encode([]);
        exit;
    }

    $prescriptions = $prescriptionModel->getAllPrescriptionsByPatientId($patientId);
    echo json_encode($prescriptions);
} catch (Throwable $e) {
    echo json_encode([]);
}
?>


