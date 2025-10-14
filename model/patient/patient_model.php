<?php
function getTotalPatients($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM patients");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total'] ?? 0;
}
?>