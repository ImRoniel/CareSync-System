<?php

// require_once __DIR__ . '/../../config/db_connect.php';
// require_once __DIR__ . '/../../controllers/admin/secretaryController.php';
// require_once __DIR__ . '/../../controllers/auth/session.php';
// require_once __DIR__ . '/../../model/secretaryModel.php';
// header('Content-Type: application/json');

// if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'secretary') {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

// $secretaryUserId = intval($_SESSION['user_id']);
// $action = $_GET['action'] ?? '';
// $appointmentId = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : (isset($_REQUEST['appointment_id']) ? intval($_REQUEST['appointment_id']) : 0);

// if (!$appointmentId) {
//     echo json_encode(['success' => false, 'message' => 'Invalid appointment id']);
//     exit;
// }

// $controller = new SecretaryController($conn);

// if ($action === 'checkin') {
//     $ok = $controller->checkinAppointment($secretaryUserId, $appointmentId);
//     if ($ok) {
//         // redirect to doctor's dashboard view (adjust path if your routing differs)
//         echo json_encode(['success' => true, 'redirect' => '/CareSync-System/views/doctor/Doctor_DashBoard1.php']);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'Failed to check in appointment']);
//     }
//     exit;
// }

// if ($action === 'reject') {
//     $ok = $controller->rejectAppointment($secretaryUserId, $appointmentId);
//     if ($ok) {
//         echo json_encode(['success' => true, 'message' => 'Appointment rejected']);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'Failed to reject appointment']);
//     }
//     exit;
// }

// echo json_encode(['success' => false, 'message' => 'Unknown action']);
?>