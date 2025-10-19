<?php
require_once __DIR__ . '/../../../CareSync-System/config/db_connect.php';
require_once __DIR__ . '/../../model/admin/userModel.php';

class UserController {
    private $model;

    public function __construct($mysqli) {
        $this->model = new UserModel($mysqli);
    }

    public function index() {
        return $this->model->getAllUsers();
    }
}

//IN USER THAT, this is the delete logic 
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: /Caresync-System/dashboard/admin_dashboard.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
}

// $search = isset($_GET['search']) ? trim($_GET['search']) : null;
// $users = getUsers($conn, $search);

// $conn->close();
// $search = isset($_GET['search']) ? trim($_GET['search']) : null;
// $doctors = getDoctors($conn, $search);

//logic for user search implementation  
$userModel = new UserModel($conn);

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $search = $_GET['search'] ?? '';
    $users = $userModel->getAllUsers($search);
    include "../../views/admin/user_list.php";
    exit;
}

?>
