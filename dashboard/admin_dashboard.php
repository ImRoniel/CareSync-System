<?php
require_once __DIR__ . '/../config/db_connect.php'; //our main db
require_once __DIR__ . '/../controllers/admin/userController.php'; //controller admin in user controller
require_once __DIR__ . '/../controllers/admin/patientController.php';
require_once __DIR__ . '/../controllers/admin/DoctorController.php'; //controller admin in doctor controller 
require_once __DIR__ . '/../model/appointment/appointment_model.php'; // appointment function 
require_once __DIR__ . '/../controllers/secretary/secretariesController.php';
//require_once __DIR__ . '/../views/admin/edit_user.php';

// Include database connection
$error_message = "";

// Count total users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($totalUsersQuery);

if ($result && $row = $result->fetch_assoc()) {
    $totalUsers = $row['total_users'];  
} else {
    $error_message .= "<!-- Error fetching total users -->";
    $totalUsers = 0;
}

// Count total doctors
$doctorQuery = "SELECT COUNT(*) AS total_doctors FROM users WHERE role = 'doctor'";
$doctorResult = $conn->query($doctorQuery);

if ($doctorResult && $row = $doctorResult->fetch_assoc()) {
    $totalDoctors = $row['total_doctors'];
} else {
    $error_message .= "<!-- Error fetching total doctors -->";
    $totalDoctors = 0;
}

// Count total patients
$patientQuery = "SELECT COUNT(*) AS total_patients FROM users WHERE role = 'patient'";
$patientResult = $conn->query($patientQuery);

if ($patientResult && $row = $patientResult->fetch_assoc()) {
    $totalPatients = $row['total_patients'];
} else {
    $error_message .= "<!-- Error fetching total patients -->";
    $totalPatients = 0;
}

//count total secretaries
$secretaryQuery = "SELECT COUNT(*) AS total_secretaries FROM users WHERE role = 'secretary'";
$secretaryResult = $conn->query($secretaryQuery);

if($secretaryResult && $row = $secretaryResult->fetch_assoc()){
    $totalSecretaries = $row['total_secretaries'];
} else {
    $error_message .= "<!-- Error fetching total secretaries -->";
    $totalSecretaries = 0;
}
// Optional: echo errors as HTML comments (hidden)
echo $error_message;


$appointments = [];
if (!empty($user['doctor_id'])) {
    $appointments = getDoctorAppointments($conn, $user['doctor_id']);
}


$controller = new UserController($conn);
$resultSystemOver = $controller->index();

if (!$resultSystemOver) {
    echo "No users found.";
    exit();
}
$userQuery = "
    SELECT 
        id,
        name,
        email,
        role,
        DATE_FORMAT(created_at, '%W, %h:%i %p') AS created_at
    FROM users
    ORDER BY created_at DESC
";
$resultSystemOver = $conn->query($userQuery);

if (!$resultSystemOver) {
    die('Query failed: ' . $conn->error);
}



    if (!empty($_GET['search'])) {
        $search = strtolower(trim($_GET['search']));
        $doctors = array_filter($doctors, function($doc) use ($search) {
            return str_contains(strtolower($doc['doctor_name']), $search) ||
                   str_contains(strtolower($doc['specialization'] ?? ''), $search) ||
                   str_contains(strtolower($doc['email']), $search);
        });
    }

//  Initialize controller
// $doctorController = new DoctorController($conn);
// if(!$doctorController){
//     echo $error_message = "doctor controller not found";
// }
// //  Check if there's an ID
// if (!isset($_GET['id'])) {
//     die("Invalid request. No doctor ID provided.");
// }
// $id = intval($_GET['id']);

// // Get doctor info
// $doctor = $doctorController->getDoctor($id);
// if (!$doctor) die("Doctor not found.");

// // Handle form submission
// $message = "";
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = "Dr. " . trim($_POST['name']);
//     $email = trim($_POST['email']);
//     $specialization = trim($_POST['specialization']);

//     $doctorController->updateDoctor($id, $name, $email, $specialization);
//     header("Location: /Caresync-System/dashboard/admin_dashboard.php?message=Doctor updated successfully");
//     exit;
// }

$patientController = new PatientController($conn);
$resultPatientSystemOver = $patientController->index();

// Create controller and fetch all users
$secretariesController = new secretariesControllerForAdmin($conn);
$secretaryResult = $secretariesController->index();

if (!$secretaryResult) {
    die("Query failed: " . $conn->error);  // 🔍 Debug message
}

if ($secretaryResult->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // your logic
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS styles remain the same */
        :root {
            --primary: #2E8949;      
            --primary-dark: #1e5c30;  
            --primary-light: #4CAF50; 
            --secondary: #CFCFCF;     
            --accent: #AD5057;        
            --danger: #AD5057;        
            --warning: #FF9800;       
            --info: #2196F3;          
            
            --text-dark: #111814;     
            --text-medium: #2E603D;   
            --text-light: #CFCFCF;    
            
            --bg-white: #FFFFFF;      
            --bg-light: #f5f7f9;      
            --bg-gray: #CFCFCF;       
            
            --border-light: #CFCFCF;  
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.5rem; }
        
        p {
            margin-bottom: 1.5rem;
            color: var(--text-medium);
        }
        
        .section-title {
            margin-bottom: 2rem;
        }
        
        .section-title h2 {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .section-title h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            gap: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-info {
            background-color: var(--info);
            color: white;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }
        
        header {
            background-color: var(--bg-white);
            box-shadow: var(--shadow-sm);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .nav-links {
            display: flex;
            gap: 32px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
        }
        
        .nav-links a.active:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary);
        }
        
        .nav-actions {
            display: flex;
            gap: 16px;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-dark);
            cursor: pointer;
        }
        
        .dashboard {
            padding: 140px 0 40px;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-icon.primary {
            background-color: rgba(46, 137, 73, 0.1);
            color: var(--primary);
        }
        
        .stat-icon.warning {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning);
        }
        
        .stat-icon.info {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--info);
        }
        
        .stat-icon.danger {
            background-color: rgba(173, 80, 87, 0.1);
            color: var(--danger);
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: var(--text-medium);
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .card {
            background-color: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .card-header h2 {
            font-size: 1.3rem;
            color: var(--primary);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
        }
        
        table th {
            color: var(--text-medium);
            font-weight: 600;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        table tr {
            transition: background-color 0.2s ease;
        }
        
        table tr:hover {
            background-color: rgba(46, 137, 73, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: rgba(46, 137, 73, 0.1);
            color: var(--primary);
        }
        
        .status-inactive {
            background-color: rgba(207, 207, 207, 0.5);
            color: var(--text-light);
        }
        
        .status-pending {
            background-color: rgba(173, 80, 87, 0.1);
            color: var(--danger);
        }
        
        .status-warning {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning);
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .activity-item:hover {
            background-color: rgba(46, 137, 73, 0.05);
            border-radius: var(--radius-md);
            padding-left: 10px;
            padding-right: 10px;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(46, 137, 73, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }
        
        .activity-content h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .activity-content p {
            color: var(--text-medium);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .activity-time {
            color: var(--text-light);
            font-size: 0.8rem;
        }
        
        .chart-container {
            height: 200px;
            background-color: rgba(46, 137, 73, 0.05);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-medium);
            margin-top: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .chart-container:hover {
            background-color: rgba(46, 137, 73, 0.1);
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: rgba(46, 137, 73, 0.05);
            border-radius: var(--radius-md);
            padding: 20px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background-color: rgba(46, 137, 73, 0.1);
            transform: translateY(-5px);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        .action-btn p {
            font-size: 0.9rem;
            color: var(--text-medium);
            margin-bottom: 0;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .tab:hover {
            color: var(--primary);
        }
        
        .tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-box input {
            flex: 1;
        }
        
        .user-management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .user-card {
            background-color: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .user-details {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .user-details h3 {
            margin-bottom: 5px;
        }
        
        .user-details p {
            margin-bottom: 5px;
            color: var(--text-medium);
        }
        
        .user-actions {
            display: flex;
            gap: 10px;
            width: 100%;
        }
        
        .user-actions .btn {
            flex: 1;
        }
        
        footer {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 50px 0 20px;
            margin-top: 50px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            color: white;
            margin-bottom: 20px;
            font-size: 1.25rem;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 12px;
        }
        
        .footer-column ul li a {
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .footer-column p {
            color: var(--text-light);
        }
        
        .copyright p {
            color: var(--text-light);
        }
        
        .social-links {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 0.875rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: var(--bg-white);
            margin: 5% auto;
            padding: 0;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 600px;
            box-shadow: var(--shadow-lg);
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .modal-header h2 {
            margin: 0;
        }
        
        .close {
            color: var(--text-light);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: var(--text-dark);
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .mobile-nav {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            width: 100%;
            background-color: var(--bg-white);
            box-shadow: var(--shadow-md);
            z-index: 999;
            padding: 20px;
            flex-direction: column;
            gap: 15px;
        }
        
        .mobile-nav.active {
            display: flex;
        }
        
        .mobile-nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .mobile-nav a:last-child {
            border-bottom: none;
        }
        
        .mobile-nav a.active {
            color: var(--primary);
        }
        
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                padding: 15px 0;
            }
            
            .nav-links, .nav-actions {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .dashboard {
                padding: 120px 0 30px;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .user-management-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            h1 { font-size: 2rem; }
            h2 { font-size: 1.75rem; }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo">
                    <img src="../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a href="#" class="active">Dashboard</a>
                    <a href="#">Users</a>
                    <a href="#">System</a>
                    <a href="#">Reports</a>
                    <a href="#">Settings</a>
                </nav>
                
                <div class="nav-actions">
                    <button class="btn btn-secondary" onclick="showModal('profile-modal')">Profile</button>
                    <button class="btn btn-primary" onclick="window.location.href='../controllers/auth/logout.php'">Logout</button>
                </div>
                
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="mobile-nav" id="mobile-nav">
        <a href="#" class="active">Dashboard</a>
        <a href="#">Users</a>
        <a href="#">System</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
        <a href="#" onclick="showModal('profile-modal')">Profile</a>
        <a href="#" onclick="logout()">Logout</a>
    </div>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1>Admin</h1>
                    <p>Welcome back, Roniel C. Carbon</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar" onclick="showModal('profile-modal')">SA</div>
                    <div>
                        <p>Roniel C. Carbon</p>
                        <small>System Administrator</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalUsers ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalDoctors ?></h3>
                        <p>Doctors</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalSecretaries ?></h3>
                        <p>Secretaries</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-procedures"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $totalPatients?></h3>
                        <p>Patients</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($appointments); ?></h3>
                        <p>Appointments Today</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>System Overview</h2>
                            <button class="btn btn-secondary" onclick="showModal('reports-modal')">Generate Report</button>
                        </div>
                        
                        <div class="tabs">
                            <div class="tab active" data-tab="users">Users</div>
                            <div class="tab" data-tab="doctors">Doctors</div>
                            <div class="tab" data-tab="secretaries">Secretaries</div>
                            <div class="tab" data-tab="patients">Patients</div>
                            <div class="tab" data-tab="appointments">Appointments</div>
                        </div>
                        
                        <div class="tab-content active" id="users">
                            <div class="search-box">
                                <form class="search-box" method="GET" action="/../Caresync-System/controllers/admin/userController.php">
                                <input type="hidden" name="action" value="list">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search users..." 
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <button type="submit" class="btn btn-primary ">Search</button>
                                </form>
                            </div>
                        

                            
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <!-- <th>Status</th> -->
                                            <th>Last Login</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($resultSystemOver->num_rows > 0): ?>
                                            <?php while ($row = $resultSystemOver->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['name']); ?></td>
                                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                                    <td><?= htmlspecialchars($row['role']); ?></td>
                                                    <!-- <td><span class="status-badge status-active">Active</span></td> -->
                                                    <td><?= htmlspecialchars($row['created_at']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-secondary" onclick="window.location.href='/Caresync-System/views/admin/edit_user.php?id=<?= htmlspecialchars($row['id']) ?>'">
                                                            Edit
                                                        </button>

                                                        <button class="btn btn-sm btn-danger"
                                                                 onclick="if(confirm('Are you sure you want to delete this user?')) window.location.href='/Caresync-System/controllers/admin/delete_user.php?id=<?= htmlspecialchars($row['id']) ?>'">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>    
                                                <td colspan="6" style="text-align: center;">No users found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        

                        <div class="tab-content" id="doctors">
                            <div class="search-box">
                                <form method="GET" action="">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search doctors..."
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <button type="submit" class="btn btn-primary mt-2">Search</button>
                                </form>
                            </div>

                            <div class="user-management-grid">
                                <?php if ($doctors && $doctors->num_rows > 0): ?>
                                    <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                        <div class="user-card">
                                            <div class="user-avatar-large">
                                                <?= strtoupper(substr($doctor['doctor_name'], 0, 1)) ?>
                                            </div>
                                            <div class="user-details">
                                                <h3>Dr. <?= htmlspecialchars($doctor['doctor_name']) ?></h3>
                                                <p><?= htmlspecialchars($doctor['specialization'] ?? 'No specialization') ?></p>
                                                <p><?= htmlspecialchars($doctor['email']) ?></p>
                                            </div>
                                            <div class="user-actions">
                                                <button class="btn btn-sm btn-secondary"
                                                        onclick="window.location.href='/Caresync-System/views/admin/edit_doctor.php?id=<?= htmlspecialchars($doctor['user_id']) ?>'">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-info"
                                                        onclick="window.location.href='/Caresync-System/views/admin/schedule_doctor.php?id=<?= htmlspecialchars($doctor['user_id']) ?>'">
                                                    Schedule
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">⚠️ No doctors found.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- secretaries -->
                        <div class="tab-content" id="secretaries">
                            <div class="search-box">
                                <form method="GET" action="">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search secretaries..."
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <button type="submit" class="btn btn-primary mt-2">Search</button>
                                </form>
                            </div>

                            <div class="user-management-grid">
                                <?php if($secretaryResult->num_rows > 0):  ?>
                                    <?php while($secretaries = $secretaryResult->fetch_assoc()): ?> 
                                    
                                        <div class="user-card">
                                                <div class="user-avatar-large">
                                                    <?= strtoupper(substr($secretaries['secretary_name'], 0, 1)) ?>
                                                </div>
                                                <div class="user-details">
                                                    <h3><?= htmlspecialchars($secretaries['secretary_name']) ?></h3>
                                                    <p><?= htmlspecialchars($secretaries['department'] ?? 'No Department') ?></p>
                                                    <p><?= htmlspecialchars($secretaries['email']) ?></p>
                                                    
                                                </div>

                                                <!-- balikan natin ito mamaya -->
                                                <div class="user-actions">
                                                    <button class="btn btn-sm btn-secondary"
                                                            onclick="window.location.href='/CareSync-System/views/admin/edit_secretary.php?id=<?= htmlspecialchars($sec['user_id']) ?>'">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-info"
                                                        onclick="window.location.href='/Caresync-System/views/admin/schedule_doctor.php?id=<?= htmlspecialchars($doctor['user_id']) ?>'">
                                                    Schedule
                                                </button>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">⚠️ No secretaries found.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="tab-content" id="patients">
                            <div class="search-box">
                                <div class="search-box">
                                    <input type="text" class="form-control" placeholder="Search patients...">
                                    <button class="btn btn-primary">Search</button>
                                </div>
                            </div>
                            
                            
                            <div class="table-responsive">   
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Patient ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Doctor</th>
                                            <th>Last Login</th> 
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($resultPatientSystemOver->num_rows > 0): ?>
                                            <?php while($row = $resultPatientSystemOver->fetch_assoc()):?>
                                        
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                                    <td><?= htmlspecialchars($row['name']); ?></td>
                                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                                    <td><?= htmlspecialchars($row['doctor_name'] ?? 'No doctor') ?></td>
                                                    <td><?= htmlspecialchars($row['created_at']); ?></td>
                                                    <td>
                                                        <!-- we need to put a validation for this button viwe -->
                                                        <button class="btn btn-sm btn-secondary" onclick="viewPatient('Name Here')">View</button> 
                                                        <!-- we need to put a validatio for this button  -->
                                                        <button class="btn btn-sm btn-info" onclick="viewRecords('Name Here')">Records</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile ?>  
                                        <?php endif ?>
                                    </tbody>
                                </table>                         
                            </div>
                    </div>
                        <!-- integrate it here -->
                         
                        <div class="tab-content" id="appointments">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search appointments...">
                                <button class="btn btn-primary">Search</button>
                            </div>
                                    
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Appointment ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ID Here</td>
                                            <td>Name Here</td>
                                            <td>Dr. Name Here</td>
                                            <td>Date & Time Here</td>
                                            <td><span class="status-badge status-active">Scheduled</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-secondary" onclick="viewAppointment('ID Here')">View</button>
                                                <button class="btn btn-sm btn-warning" onclick="rescheduleAppointment('ID Here')">Reschedule</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ID Here</td>
                                            <td>Name Here</td>
                                            <td>Dr. Name Here</td>
                                            <td>Date & Time Here</td>
                                            <td><span class="status-badge status-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-secondary" onclick="viewAppointment('ID Here')">View</button>
                                                <button class="btn btn-sm btn-primary" onclick="approveAppointment('ID Here')">Approve</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ID Here</td>
                                            <td>Name Here</td>
                                            <td>Dr. Name Here</td>
                                            <td>Date & Time Here</td>
                                            <td><span class="status-badge status-active">Scheduled</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-secondary" onclick="viewAppointment('ID Here')">View</button>
                                                <button class="btn btn-sm btn-warning" onclick="rescheduleAppointment('ID Here')">Reschedule</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>System Performance</h2>
                            <button class="btn btn-secondary" onclick="showModal('performance-modal')">View Details</button>
                        </div>
                        <div class="chart-container" onclick="showModal('performance-modal')">
                            <p>Click to view detailed performance metrics</p>
                        </div>
                    </div>
                </div>
                <!-- //right column  -->
                <div class="right-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Activity</h2>
                            <button class="btn btn-secondary" onclick="showModal('activity-modal')">View All</button>
                        </div>
                        
                        <ul class="activity-list">
                            <li class="activity-item" onclick="viewActivityDetails('login')">
                                <div class="activity-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>User Login</h4>
                                    <p>Dr. Name Here logged in to the system</p>
                                    <div class="activity-time">Today, 08:45 AM</div>
                                </div>
                            </li>
                            
                            <li class="activity-item" onclick="viewActivityDetails('appointment')">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New Appointment</h4>
                                    <p>New appointment scheduled with Dr. Name Here</p>
                                    <div class="activity-time">Today, 08:30 AM</div>
                                </div>
                            </li>
                            
                            <li class="activity-item" onclick="viewActivityDetails('user')">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New User</h4>
                                    <p>New patient registered in the system</p>
                                    <div class="activity-time">Yesterday, 05:20 PM</div>
                                </div>
                            </li>
                            
                            <li class="activity-item" onclick="viewActivityDetails('record')">
                                <div class="activity-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Medical Record</h4>
                                    <p>Medical record updated for Name Here</p>
                                    <div class="activity-time">Yesterday, 03:45 PM</div>
                                </div>
                            </li>
                            
                            <li class="activity-item" onclick="viewActivityDetails('system')">
                                <div class="activity-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>System Update</h4>
                                    <p>System maintenance completed successfully</p>
                                    <div class="activity-time">Yesterday, 02:15 AM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Quick Actions</h2>
                        </div>
                        
                        <div class="quick-actions">
                            <div class="action-btn" onclick="showModal('add-user-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <p>Add User</p>
                            </div>
                            
                            <div class="action-btn" onclick="showModal('add-doctor-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <p>Add Doctor</p>
                            </div>
                            
                            <div class="action-btn" onclick="showModal('schedule-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <p>Manage Schedule</p>
                            </div>
                            
                            <div class="action-btn" onclick="showModal('settings-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <p>System Settings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CareSync</h3>
                    <p>Streamlining healthcare management for better patient care and efficient medical practice operations.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="#">Patient Management</a></li>
                        <li><a href="#">Appointment Scheduling</a></li>
                        <li><a href="#">Medical Records</a></li>
                        <li><a href="#">Billing & Invoicing</a></li>
                        <li><a href="#">Reporting & Analytics</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Healthcare St, Medical City</li>
                        <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@caresync.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 CareSync. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>User Profile</h2>
                <span class="close" onclick="closeModal('profile-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" value="System Administrator">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="admin@caresync.com">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" class="form-control" value="Administrator" disabled>
                </div>
                <div class="form-group">
                    <label>Last Login</label>
                    <input type="text" class="form-control" value="Today, 08:30 AM" disabled>
                </div>
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal('profile-modal')">Cancel</button>
                    <button class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div id="add-user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New User</h2>
                <span class="close" onclick="closeModal('add-user-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-control" placeholder="Enter full name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control">
                        <option>Patient</option>
                        <option>Doctor</option>
                        <option>Secretary</option>
                        <option>Administrator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" placeholder="Enter password">
                </div>
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal('add-user-modal')">Cancel</button>
                    <button class="btn btn-primary" onclick="addUser()">Add User</button>
                </div>
            </div>
        </div>
    </div>

    <div id="reports-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Generate Report</h2>
                <span class="close" onclick="closeModal('reports-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Report Type</label>
                    <select class="form-control">
                        <option>User Activity</option>
                        <option>Appointment Statistics</option>
                        <option>Financial Report</option>
                        <option>System Performance</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Format</label>
                    <select class="form-control">
                        <option>PDF</option>
                        <option>Excel</option>
                        <option>CSV</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal('reports-modal')">Cancel</button>
                    <button class="btn btn-primary" onclick="generateReport()">Generate</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Activate the corresponding tab
            document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
        }
        
        // Initialize tabs
        document.addEventListener('DOMContentLoaded', function() {
        
            // Add click event to all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    showTab(tabId);
                });
            });
        });
        
        // Modal functionality
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
        
        // Mobile menu functionality
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobile-nav');
            mobileNav.classList.toggle('active');
        }
        
        // User management functions
        function editUser(userName) {
            alert(`Editing user: ${userName}`);
            // In a real application, this would open a form with user details
        }
        
        function deactivateUser(userName) {
            if (confirm(`Are you sure you want to deactivate ${userName}?`)) {
                alert(`${userName} has been deactivated.`);
                // In a real application, this would make an API call
            }
        }
        
        function activateUser(userName) {
            alert(`${userName} has been activated.`);
            // In a real application, this would make an API call
        }
        
        function approveUser(userName) {
            alert(`${userName} has been approved.`);
            // In a real application, this would make an API call
        }
        
        // Doctor management functions
        function viewDoctor(doctorName) {
            alert(`Viewing details for: ${doctorName}`);
            // In a real application, this would open a detailed view
        }
        
        function editDoctor(doctorName) {
            alert(`Editing doctor: ${doctorName}`);
            // In a real application, this would open a form with doctor details
        }
        
        function viewSchedule(doctorName) {
            alert(`Viewing schedule for: ${doctorName}`);
            // In a real application, this would open a schedule view
        }
        
        function approveDoctor(doctorName) {
            alert(`${doctorName} has been approved.`);
            // In a real application, this would make an API call
        }
        
        // Patient management functions
        function viewPatient(patientName) {
            alert(`Viewing details for: ${patientName}`);
            // In a real application, this would open a detailed view
        }
        
        function viewRecords(patientName) {
            alert(`Viewing medical records for: ${patientName}`);
            // In a real application, this would open medical records
        }
        
        // Appointment management functions
        function viewAppointment(appointmentId) {
            alert(`Viewing appointment: ${appointmentId}`);
            // In a real application, this would open appointment details
        }
        
        function rescheduleAppointment(appointmentId) {
            alert(`Rescheduling appointment: ${appointmentId}`);
            // In a real application, this would open a rescheduling form
        }
        
        function approveAppointment(appointmentId) {
            alert(`Appointment ${appointmentId} has been approved.`);
            // In a real application, this would make an API call
        }
        
        // Activity functions
        function viewActivityDetails(activityType) {
            alert(`Viewing details for ${activityType} activity`);
            // In a real application, this would open activity details
        }
        
        // Other functions
        function updateProfile() {
            alert('Profile updated successfully!');
            closeModal('profile-modal');
        }
        
        function addUser() {
            alert('User added successfully!');
            closeModal('add-user-modal');
        }
        
        function generateReport() {
            alert('Report generated successfully!');
            closeModal('reports-modal');
        }
        
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                alert('You have been logged out.');
                // In a real application, this would redirect to login page
            }
        }
    </script>
    
</body>
</html> 