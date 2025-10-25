<?php
class SecretaryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSecretaryByUserId($user_id) {
        $sql = "
            SELECT 
                u.id, u.name, u.email, u.role,
                s.secretary_id, s.phone, s.address, s.department, s.employment_date, s.assigned_doctor_id
            FROM users u
            JOIN secretaries s ON u.id = s.user_id
            WHERE u.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    //method for updating a secretary profile method
    public function updateSecretaryProfile($user_id, $name, $email, $phone, $address, $department) {
    $sql = "
        UPDATE users 
        SET name = ?, email = ?
        WHERE id = ?;
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // update secretary table
    $sql2 = "
        UPDATE secretaries
        SET phone = ?, address = ?, department = ?
        WHERE user_id = ?;
    ";

    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->bind_param("sssi", $phone, $address, $department, $user_id);
    return $stmt2->execute(); // returns true or false
    }

}
?>
