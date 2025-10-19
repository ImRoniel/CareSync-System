<?php
require_once __DIR__ . '/../../model/secretaries/secretariesModel.php';

class SecretaryController {
    private $secretaryModel;

    public function __construct($conn) {
        $this->secretaryModel = new SecretaryModel($conn);
    }

     public function getProfile($userId) {
        return $this->secretaryModel->getSecretaryByUserId($userId);
    }
   
}

?>

<?php

class secretariesControllerForAdmin {
    private $model;

    public function __construct($conn) {
        $this->model = new secretariesModelForAdmin($conn);
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        return $this->model->getSecretaries($search);
    }
}
?>
