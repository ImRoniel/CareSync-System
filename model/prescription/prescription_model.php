<?php
function getPrescriptionsToday($conn, $doctor_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total_prescriptions
        FROM prescriptions
        WHERE doctor_id = ?
        AND DATE(created_at) = CURDATE()
    ");

    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total_prescriptions'] ?? 0;
}
?>
