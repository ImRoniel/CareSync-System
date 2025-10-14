<?php
function getDoctorAppointments($conn, $doctor_id) {
    $stmt = $conn->prepare("
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.queue_number,
            u.name AS patient_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN users u ON p.user_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date, a.appointment_time
    ");

    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $appointments;
}
?>
