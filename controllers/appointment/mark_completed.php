<?php
require_once __DIR__ . '/../../controllers/auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = intval($_POST['appointment_id'] ?? 0);

    if ($appointmentId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE appointments SET status = 'completed', updated_at = NOW() WHERE appointment_id = ?");
    $stmt->bind_param('i', $appointmentId);
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Consultation marked as completed.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }

    $stmt->close();
    $conn->close();
}
?>
