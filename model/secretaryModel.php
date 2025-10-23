<?php
class SecretaryModel {
    private $model;

    public function __construct($conn) {
        $this->model = $conn;
    }

    public function getAllSecretary(){
        $sql = "SELECT * 
                FROM users
                CROSS JOIN secretaries
                ON users.id = secretaries.user_id";
        $stmt = $this->model->query($sql);
        $result = $stmt;
        return $result;
    }
}
?>
