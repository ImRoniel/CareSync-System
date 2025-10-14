<?php
function getDoctorActivity($conn, $doctor_id, $limit = 5) {
    $stmt = $conn->prepare("
        SELECT a.activity_type, a.activity_message, a.created_at
        FROM activity_logs a
        WHERE a.doctor_id = ?
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $doctor_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $activities;
}
?>
