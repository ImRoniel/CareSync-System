<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get all users (with optional search filter)
    public function getAllUsers($search = '') {
        $sql = "SELECT id, name, email, role, created_at FROM users";
        if (!empty($search)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ? OR role LIKE ?";
            $stmt = $this->conn->prepare($sql);
            $like = "%" . $search . "%";
            $stmt->bind_param("sss", $like, $like, $like);
        } else {
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Delete a user
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
