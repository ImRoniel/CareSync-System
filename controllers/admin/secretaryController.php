<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/secretaryModel.php';

class SecretaryController {
    private $secretaryModel;

    public function __construct($conn) {
        $this->secretaryModel = new SecretaryModel($conn);
    }

    public function index() {
        // Get all secretaries from the model
        $secretaries = $this->secretaryModel->getAllSecretary();
        return $secretaries;
    }
}

