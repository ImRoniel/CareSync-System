<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/userModel.php';

class UserController {
    private $model;

    public function __construct($conn) {
        $this->model = new UserModel($conn);
    }

    public function index($search = '') {
        return $this->model->getAllUsers($search);
    }

    public function delete($id) {
        if ($this->model->deleteUser($id)) {
            header("Location: ../../views/admin/user_list.php?msg=deleted");
            exit();
        } else {
            echo "Error deleting user.";
        }
    }
}

// ---------- ROUTING LOGIC -------------
$controller = new UserController($conn);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
    exit;
}

$search = $_GET['search'] ?? '';
$resultSystemOver = $controller->index($search);
?>
