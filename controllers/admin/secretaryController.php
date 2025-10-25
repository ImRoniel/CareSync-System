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

    //method for updating a editing a secretary profile 
    public function updateSecretary($user_id, $data) {
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $address = $data['address'];
    $department = $data['department'];

    return $this->model->updateSecretaryProfile($user_id, $name, $email, $phone, $address, $department);
}
}

