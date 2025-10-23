<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/secretaryModel.php';

class SecretaryController {
    private $conn;

    public function __construct($conn) {
        $this->conn = new SecretaryModel($conn);
    }

    public function showSecretaries() {
        // Get all secretaries from the model
        $secretaries = $this->conn->getAllSecretary();
        return $secretaries;
    }
}

