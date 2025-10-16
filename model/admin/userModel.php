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


// controller logic for search
function getUsers($conn, $search = null) {
    if ($search) {
        $sql = "SELECT * FROM users 
                WHERE name LIKE ? OR email LIKE ? OR role LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%" . $search . "%";
        $stmt->bind_param("sss", $like, $like, $like);
    } else {
        $sql = "SELECT * FROM users";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
    return $users;
}

//doctor logic 
// function getDoctors($conn, $search = null) {
//     if ($search) {
//         $sql = "SELECT * FROM users 
//                 WHERE role = 'doctor' 
//                 AND (name LIKE ? OR email LIKE ?)";
//         $stmt = $conn->prepare($sql);
//         $like = "%" . $search . "%";
//         $stmt->bind_param("ss", $like, $like);
//     } else {
//         $sql = "SELECT * FROM users WHERE role = 'doctor'";
//         $stmt = $conn->prepare($sql);
//     }

//     $stmt->execute();
//     $result = $stmt->get_result();

//     $doctors = [];
//     while ($row = $result->fetch_assoc()) {
//         $doctors[] = $row;
//     }

//     $stmt->close();
//     return $doctors;
// }

//code for system overview

//model\admin\userModel.php
?>



