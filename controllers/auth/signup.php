<?php
    session_start();
    include("includes/db_connect.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $name = trim($_POST['name']) ?? '';
        $email = trim($_POST['email'])  ?? '';
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
    }

    //this insert into users table 
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);     

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get last inserted user ID

        // Insert into specific role table
        if ($role === 'patient') {
            $phone = $_POST['phone'] ?? null;
            $address = $_POST['address'] ?? null;
            $conn->query("INSERT INTO patients (user_id, phone, address) VALUES ($user_id, '$phone', '$address')");
        } 
        elseif ($role === 'doctor') {
            $license = $_POST['license_no'] ?? '';
            $spec = $_POST['specialization'] ?? '';
            $conn->query("INSERT INTO doctors (user_id, license_no, specialization) VALUES ($user_id, '$license', '$spec')");
        } 
        elseif ($role === 'secretary') {
            $conn->query("INSERT INTO secretaries (user_id) VALUES ($user_id)");
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $name;

        // : Redirect based on role
        switch ($role) {
            case 'patient':
                header("Location: dashboard/patient_dashboard.php");
                break;
            case 'doctor':
                header("Location: dashboard/doctor_dashboard.php");
                break;
            case 'secretary':
                header("Location: dashboard/secretary_dashboard.php");
                break;
        }
        exit();
    } else {
        echo "Signup failed: " . $stmt->error;
    }

?>
