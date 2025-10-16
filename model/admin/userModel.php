<?php
class UserModel {
    private $conn;

    public function __construct($mysqli) {
        $this->conn = $mysqli;
    }

    public function getAllUsers() {
        $sql = "SELECT id, name, email, role, created_at FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
