<?php
// controllers/admin/DoctorController.php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/doctor/DoctorModel.php';

class DoctorController {
    private $doctorModel;

    public function __construct($conn) {
        $this->doctorModel = new DoctorModel($conn);
    }

    public function search() {
        $searchTerm = $_GET['search'] ?? '';
        return $this->doctorModel->searchDoctors($searchTerm);
    }
}

// When accessed directly
$controller = new DoctorController($conn);
$doctors = $controller->search();
?>
