<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'patient') {
    echo json_encode([]);
    exit;
}

try {
    $userId = intval($_SESSION['user_id']);

    // Resolve patient_id from users
    $sqlPatient = "SELECT patient_id FROM patients WHERE user_id = ?";
    $stmt = $conn->prepare($sqlPatient);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    $patientId = $row['patient_id'] ?? null;
    if (!$patientId) {
        echo json_encode([]);
        exit;
    }

    // Fetch appointment history for this patient
    $sql = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            COALESCE(a.reason, 'Consultation') AS type,
            COALESCE(du.name, '') AS doctor_name
        FROM appointments a
        LEFT JOIN doctors d ON d.doctor_id = a.doctor_id
        LEFT JOIN users du ON du.id = d.user_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";

    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param('i', $patientId);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $list = [];
    while ($r = $result->fetch_assoc()) {
        $r['date_time'] = $r['appointment_date'] . ' ' . $r['appointment_time'];
        $statusLower = strtolower($r['status'] ?? '');
        if ($statusLower === 'approved') {
            $r['status_label'] = 'Confirmed';
        } elseif ($statusLower === 'pending') {
            $r['status_label'] = 'Pending';
        } elseif ($statusLower === 'completed') {
            $r['status_label'] = 'Completed';
        } elseif ($statusLower === 'cancelled') {
            $r['status_label'] = 'Cancelled';
        } else {
            $r['status_label'] = ucfirst($statusLower);
        }
        $list[] = $r;
    }
    $stmt2->close();

    echo json_encode($list);
} catch (Throwable $e) {
    echo json_encode([]);
}
?>


