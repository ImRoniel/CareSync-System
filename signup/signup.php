<?php
include_once('../config/db_connect.php');

$role = $_GET['role'] ?? null;

if (!$role || !in_array($role, ['doctor', 'patient', 'secretary'])) {
    header("Location: role_selection.php");
    exit();
}

if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check for duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $stmt->execute();
        $user_id = $conn->insert_id;

        // Role-specific logic
        if ($role === 'doctor') {
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $license_no = $_POST['license_no'];
            $specialization = $_POST['specialization'];
            $years_experience = $_POST['years_experience'];
            $clinic_room = $_POST['clinic_room'];

            $stmt2 = $conn->prepare("INSERT INTO doctors (user_id, phone, address, license_no, specialization, years_experience, clinic_room) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("issssis", $user_id, $phone, $address, $license_no, $specialization, $years_experience, $clinic_room);
            $stmt2->execute();
        }
        elseif ($role === 'patient') {
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $age = $_POST['age'];
            $gender = $_POST['gender'];
            $blood_type = $_POST['blood_type'];
            $emergency_contact_name = $_POST['emergency_contact_name'];
            $emergency_contact_phone = $_POST['emergency_contact_phone'];
            $medical_history = $_POST['medical_history'];

            $stmt2 = $conn->prepare("INSERT INTO patients (user_id, phone, address, age, gender, blood_type, emergency_contact_name, emergency_contact_phone, medical_history)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("ississsss", $user_id, $phone, $address, $age, $gender, $blood_type, $emergency_contact_name, $emergency_contact_phone, $medical_history);
            $stmt2->execute();
        }
        elseif ($role === 'secretary') {
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $department = $_POST['department'];
            $employment_date = $_POST['employment_date'];
            $assigned_doctor_id = $_POST['assigned_doctor_id'] ?? null;

            $stmt2 = $conn->prepare("INSERT INTO secretaries (user_id, phone, address, department, employment_date, assigned_doctor_id)
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("issssi", $user_id, $phone, $address, $department, $employment_date, $assigned_doctor_id);
            $stmt2->execute();
        }

        header("Location: ../login/login.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($role) ?> Signup - CareSync</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>
    <div class="signup-container">
        <h2>Signup as <?= ucfirst($role) ?></h2>
        <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <?php if ($role === 'doctor'): ?>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="text" name="license_no" placeholder="License Number" required>
                <input type="text" name="specialization" placeholder="Specialization" required>
                <input type="number" name="years_experience" placeholder="Years of Experience" required>
                <input type="text" name="clinic_room" placeholder="Clinic Room">
            <?php elseif ($role === 'patient'): ?>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="number" name="age" placeholder="Age" required>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <select name="blood_type">
                    <option value="">Select Blood Type</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
                <input type="text" name="emergency_contact_name" placeholder="Emergency Contact Name">
                <input type="text" name="emergency_contact_phone" placeholder="Emergency Contact Phone">
                <textarea name="medical_history" placeholder="Medical History (optional)"></textarea>
            <?php elseif ($role === 'secretary'): ?>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="text" name="department" placeholder="Department" required>
                <label>Employment Date:</label>
                <input type="date" name="employment_date" required>
                <input type="number" name="assigned_doctor_id" placeholder="Assigned Doctor ID (optional)">
            <?php endif; ?>

            <button type="submit" name="signup">Create Account</button>
        </form>
        <p>Already have an account? <a href="../login/login.php">Login here</a></p>
    </div>
</body>
</html>
