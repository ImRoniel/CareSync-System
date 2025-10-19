<?php
// controllers/admin/DoctorController.php
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../model/doctor/DoctorModel.php';

class DoctorController {
    private $doctorModel;
    private $model;

    public function __construct($conn) {
        $this->doctorModel = new DoctorModel($conn);
        $this->model = new DoctorModel($conn);
    }

    public function search() {
        $searchTerm = $_GET['search'] ?? '';
        return $this->doctorModel->searchDoctors($searchTerm);
    }

    //logic method for editing the doctor using id | for showing the data to edot
    public function edit($doctor_id) {
        return $this->model->getDoctorById($doctor_id);
    }

    //hamdle update form 
    public function update($doctor_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'phone' => $_POST['phone'],
                'address' => $_POST['address'],
                'license_no' => $_POST['license_no'],
                'specialization' => $_POST['specialization'],
                'years_experience' => intval($_POST['years_experience']),
                'clinic_room' => $_POST['clinic_room']
            ];

            if ($this->model->updateDoctor($doctor_id, $data)) {
                header("Location: /CareSync-System/dashboard/admin_dashboard.php?message=Doctor updated successfully");
                exit;
            } else {
                echo "Error updating doctor.";
            }
        }
    }
     public function getPatientCount() {
        return $this->model->getTotalPatients();
    }
}

// When accessed directly
$controller = new DoctorController($conn);
$doctors = $controller->search();
?>
