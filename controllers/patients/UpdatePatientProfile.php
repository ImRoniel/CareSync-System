<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/PatientModel.php';
require_once __DIR__ . '/../../controllers/admin/patientController.php';
require_once __DIR__ . '/../../controllers/auth/session.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $controller = new PatientController($conn);
    $success = $controller-> updatePatient($user_id, [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
    ]);

    echo json_encode(['success' => $success]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>