<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/secretaryModel.php';

class SecretaryController {
    private $model;

    public function __construct($conn) {
        $this->model = new SecretaryModel($conn);
    }

    public function getSecretaryData($user_id) {
        return $this->model->getSecretaryByUserId($user_id);
    }
}

