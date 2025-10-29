<?php
require_once 'config/db_connect.php';

$test_appointment_id = 13;
$test_secretary_id = 11;

echo "<h3>Debugging Appointment Relationships</h3>";

// 1. Check the appointment details
echo "<h4>1. Appointment Details (ID: $test_appointment_id)</h4>";
$query = "SELECT * FROM appointments WHERE appointment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $test_appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if ($appointment) {
    echo "Appointment Found:<br>";
    echo "Patient ID: " . $appointment['patient_id'] . "<br>";
    echo "Doctor ID: " . $appointment['doctor_id'] . "<br>";
    echo "Status: " . $appointment['status'] . "<br>";
    echo "Date: " . $appointment['appointment_date'] . "<br>";
} else {
    echo "Appointment not found!<br>";
}

// 2. Check secretary details
echo "<h4>2. Secretary Details (ID: $test_secretary_id)</h4>";
$query = "SELECT * FROM secretaries WHERE secretary_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $test_secretary_id);
$stmt->execute();
$result = $stmt->get_result();
$secretary = $result->fetch_assoc();
$stmt->close();

if ($secretary) {
    echo "Secretary Found:<br>";
    echo "User ID: " . $secretary['user_id'] . "<br>";
    echo "Assigned Doctor ID: " . $secretary['assigned_doctor_id'] . "<br>";
} else {
    echo "Secretary not found!<br>";
}

// 3. Check the relationship between secretary and doctor for this appointment
echo "<h4>3. Secretary-Doctor Relationship Check</h4>";
if ($appointment && $secretary) {
    $query = "
        SELECT 
            a.appointment_id,
            a.doctor_id as appointment_doctor_id,
            s.secretary_id,
            s.assigned_doctor_id as secretary_assigned_doctor_id,
            d.doctor_id,
            d.user_id as doctor_user_id
        FROM appointments a
        INNER JOIN doctors d ON a.doctor_id = d.doctor_id
        INNER JOIN secretaries s ON d.doctor_id = s.assigned_doctor_id
        WHERE a.appointment_id = ? 
        AND s.secretary_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $test_appointment_id, $test_secretary_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $relationship = $result->fetch_assoc();
    $stmt->close();
    
    if ($relationship) {
        echo "Relationship Found!<br>";
        echo "Appointment Doctor ID: " . $relationship['appointment_doctor_id'] . "<br>";
        echo "Secretary Assigned Doctor ID: " . $relationship['secretary_assigned_doctor_id'] . "<br>";
        echo "Do they match? " . ($relationship['appointment_doctor_id'] == $relationship['secretary_assigned_doctor_id'] ? 'YES' : 'NO') . "<br>";
    } else {
        echo "No relationship found between secretary and appointment's doctor.<br>";
        echo "This means the secretary is not assigned to the doctor who has this appointment.<br>";
    }
}

// 4. Show all secretaries and their assigned doctors
echo "<h4>4. All Secretaries and Their Assigned Doctors</h4>";
$query = "
    SELECT s.secretary_id, u.name as secretary_name, 
           d.doctor_id, du.name as doctor_name,
           s.assigned_doctor_id
    FROM secretaries s
    INNER JOIN users u ON s.user_id = u.id
    LEFT JOIN doctors d ON s.assigned_doctor_id = d.doctor_id
    LEFT JOIN users du ON d.user_id = du.id
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo "Secretary: " . $row['secretary_name'] . " (ID: " . $row['secretary_id'] . ") ";
    echo "→ Assigned to Doctor: " . ($row['doctor_name'] ? $row['doctor_name'] . " (ID: " . $row['doctor_id'] . ")" : "NOT ASSIGNED") . "<br>";
}

// 5. Show all appointments with their doctors
echo "<h4>5. All Pending Appointments with Their Doctors</h4>";
$query = "
    SELECT a.appointment_id, a.patient_id, a.doctor_id, a.status,
           u.name as patient_name, du.name as doctor_name
    FROM appointments a
    INNER JOIN patients p ON a.patient_id = p.patient_id
    INNER JOIN users u ON p.user_id = u.id
    INNER JOIN doctors d ON a.doctor_id = d.doctor_id
    INNER JOIN users du ON d.user_id = du.id
    WHERE a.status = 'pending'
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo "Appointment ID: " . $row['appointment_id'] . " - Patient: " . $row['patient_name'] . " - Doctor: " . $row['doctor_name'] . " (ID: " . $row['doctor_id'] . ")<br>";
}
?>