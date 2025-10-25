<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all users (with optional search filter)
    public function getAllUsers() {
        $sql = "SELECT id, name, email, role, created_at FROM users";
        $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

      public function deleteUserById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // public function searchSecretary(){
    //     $sql = "SELECT * FROM ";
    // }
    
}
?>
