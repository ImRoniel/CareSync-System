<?php
class SecretaryModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchSecretaries($search = '') {
        $search = "%" . $search . "%";

        $sql = "SELECT u.id AS user_id, u.name, u.email, s.department
                FROM users u
                LEFT JOIN secretaries s ON u.id = s.user_id
                WHERE u.role = 'secretary'
                AND (u.name LIKE ? OR u.email LIKE ? OR s.department LIKE ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
