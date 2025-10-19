<?php
function getUserById($conn, $id) {
    $stmt = $conn->prepare("
        SELECT 
            users.id,
            users.name,
            users.email,
            users.role,
            users.created_at,
            doctors.doctor_id,
            doctors.specialization,
            doctors.phone,
            doctors.license_no,
            doctors.years_experience,
            doctors.clinic_room
        FROM users u
        LEFT JOIN u doctors ON users.id = doctors.user_id
        WHERE users.id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

?>
