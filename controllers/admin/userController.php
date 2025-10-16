<?php
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
?>
