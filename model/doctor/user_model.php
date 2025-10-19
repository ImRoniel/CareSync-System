<?php
function getUserById($conn, $id) {
    $stmt = $conn->prepare("
        SELECT 
            u.id,
            u.name,
            u.email,
            u.role,
            u.created_at,
            d.doctor_id,
            d.specialization,
            d.phone,
            d.license_no,
            d.years_experience,
            d.clinic_room
        FROM users AS u
        LEFT JOIN doctors AS d ON u.id = d.user_id
        WHERE u.id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}
?>
