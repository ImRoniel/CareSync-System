<?php
class SecretaryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllSecretary() {
        $sql = "SELECT 
                secretaries.*, 
                users.*, 
                du.name AS doctor_name
            FROM users
            CROSS JOIN secretaries
                ON users.id = secretaries.user_id
            LEFT JOIN doctors AS d
                ON secretaries.assigned_doctor_id = d.doctor_id
            LEFT JOIN users AS du
                ON d.user_id = du.id";
            $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

    public function getSecretaryByUserId($user_id) {
        $sql = "
             SELECT 
                u.id, u.name, u.email, u.role,
                s.secretary_id, s.phone, s.address, s.department, s.employment_date, s.assigned_doctor_id,
                du.name AS doctor_name
            FROM users u
            JOIN secretaries s ON u.id = s.user_id
            LEFT JOIN doctors d ON s.assigned_doctor_id = d.doctor_id
            LEFT JOIN users du ON d.user_id = du.id
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
