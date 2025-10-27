<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/admin/AdminController.php';
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
    $department = $_POST['department'] ?? '';

    $controller = new AdminController($conn);
    $success = $controller->updateAdmin($user_id, [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'department' => $department
    ]);

    if ($success) {
        // Update session name if changed
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>