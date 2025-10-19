<?php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/admin/SecretaryModel.php';

class SecretaryController {
    private $secretaryModel;

    public function __construct($conn) {
        $this->secretaryModel = new SecretaryModel($conn);
    }

    public function search() {
        $searchTerm = $_GET['search'] ?? '';
        return $this->secretaryModel->searchSecretaries($searchTerm);
    }
}

// Run immediately when included
$controller = new SecretaryController($conn);
$secretaries = $controller->search();
?>
