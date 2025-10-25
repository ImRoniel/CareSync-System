<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/userModel.php';

class UserController {
    private $model;

    public function __construct($conn) {
        $this->model = new UserModel($conn);
    }

    public function showAllUsers() {
        return $this->model->getAllUsers();
    }

   
    // ✅ Delete user action
    public function deleteUser($id) {
        if ($this->model->deleteUserById($id)) {
            header("Location: /CareSync-System/views/admin/Admin_Dashboard1.php?message=User deleted successfully");
            exit;
        } else {
            echo "❌ Error deleting user.";
        }
    }
}

// ✅ Run controller logic (outside class)
$controller = new UserController($conn);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->deleteUser(intval($_GET['id']));
}


?>
