<?php
require_once __DIR__ . '/../../controllers/auth/session.php';
include("includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']) ?? '';
    $email = trim($_POST['email']) ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? '';

    // ✅ Insert into users table
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get last inserted user ID

        // ✅ Role-specific inserts
        if ($role === 'patient') {
            $phone = $_POST['phone'] ?? null;
            $address = $_POST['address'] ?? null;

            $sqlPatient = "INSERT INTO patients (user_id, phone, address) VALUES (?, ?, ?)";
            $stmtPatient = $conn->prepare($sqlPatient);
            $stmtPatient->bind_param("iss", $user_id, $phone, $address);
            $stmtPatient->execute();

        } elseif ($role === 'doctor') {
            $license = $_POST['license_no'] ?? '';
            $spec = $_POST['specialization'] ?? '';

            $sqlDoctor = "INSERT INTO doctors (user_id, license_no, specialization) VALUES (?, ?, ?)";
            $stmtDoctor = $conn->prepare($sqlDoctor);
            $stmtDoctor->bind_param("iss", $user_id, $license, $spec);
            $stmtDoctor->execute();

        } elseif ($role === 'secretary') {
            $phone = $_POST['phone'] ?? null;
            $address = $_POST['address'] ?? null;
            $department = $_POST['department'] ?? null;
            $employment_date = date('Y-m-d'); // you can modify this if you have a form field
            $assigned_doctor_id = $_POST['assigned_doctor_id'] ?? null;

            // ✅ Ensure null if doctor not assigned
            if (empty($assigned_doctor_id)) {
                $assigned_doctor_id = null;
            }

            $sqlSec = "INSERT INTO secretaries (user_id, phone, address, department, employment_date, assigned_doctor_id)
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmtSec = $conn->prepare($sqlSec);
            $stmtSec->bind_param("issssi", $user_id, $phone, $address, $department, $employment_date, $assigned_doctor_id);
            $stmtSec->execute();
        }

        // ✅ Set session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $name;

        // ✅ Redirect based on role
        switch ($role) {
            case 'patient':
                header("Location: dashboard/patient_dashboard1.php");
                break;
            case 'doctor':
                header("Location: dashboard/doctor_dashboard1.php");
                break;
            case 'secretary':
                header("Location: dashboard/secretary_dashboard1.php");
                break;
        }
        exit();

    } else {
        echo "Signup failed: " . $stmt->error;
    }
}
?>
