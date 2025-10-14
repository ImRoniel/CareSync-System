<?php
// ../model/billing/billing_model.php

function getRevenueThisWeek($conn, $doctor_id) {
    $sql = "
        SELECT SUM(total_amount) AS total_revenue
        FROM billing
        WHERE doctor_id = ?
        AND YEARWEEK(issued_at, 1) = YEARWEEK(CURDATE(), 1)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total_revenue'] ?? 0;
}
