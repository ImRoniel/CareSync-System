
<?php

$sessionPath = __DIR__ . '/../../controllers/auth/session.php';

if (!file_exists($sessionPath)) {
    echo "session.php not found";
    exit;
}

require_once $sessionPath;
require_once __DIR__ . '/../../config/db_connect.php';
//for fetching the data from the dashboard
require_once __DIR__ . '/../../controllers/admin/AdminController.php';

$controllerData = new AdminController($conn);
$data = $controllerData->index();

// Extract variables to use in HTML easily
extract($data);

//user controller, fetching data from contoller
require_once __DIR__ . '/../../controllers/admin/userController.php';
$controllerUsers = new UserController($conn);
$resultSystemOver = $controllerUsers->showAllUsers();

require_once __DIR__ . '/../../controllers/admin/DoctorController.php';

$controllerDoctor = new DoctorController($conn);
$doctors = $controllerDoctor->showDoctors();


require_once __DIR__ . '../../../controllers/admin/secretaryController.php';
                
$controllerSecretary = new SecretaryController($conn);
$secretaries = $controllerSecretary->ShowSecretary();


require_once __DIR__ . '/../../controllers/admin/patientController.php';
$controllerPatients = new PatientController($conn);
$resultPatientSystemOver = $controllerPatients->ShowPatient();

require_once __DIR__ . '/../../controllers/appointment/AppointmentController.php';
$appointmentsController = new AppointmentController($conn);
$totalAppointments = $appointmentsController->getTodayAppointments();

// Handle appointment actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $result = $appointmentsController->handleAppointmentAction();
    if ($result['success']) {
        echo '<div class="alert alert-success">' . htmlspecialchars($result['message']) . '</div>';
    } else {
        echo '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    }
}

// Get all appointments
$appointments = $appointmentsController->getAppointments();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2E8949;      
            --primary-dark: #2E8949;  
            --primary-light: #AD5057; 
            --secondary: #CFCFCF;     
            --accent: #AD5057;        
            --danger: #AD5057;        
            
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
            max-width: 1200px;
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
        
        .btn-info {
            background-color: #2196F3;
            color: white;
        }
        
        .btn-warning {
            background-color: #FF9800;
            color: white;
        }
        
        .btn-success {
            background-color: #4CAF50;
            color: white;
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
            cursor: pointer;
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
        
        .page {
            display: none;
            padding: 140px 0 40px;
        }
        
        .page.active {
            display: block;
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
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(46, 137, 73, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            color: var(--primary);
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
            color: #FF9800;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-light);
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
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            text-align: center;
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
            width: 100%; /* Ensure full width of grid cell */
            height: 100%; /* Ensure full height of grid cell */
            box-sizing: border-box; /* Include padding in width/height calculation */
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
        
        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        
        .profile-sidebar {
            background-color: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 3rem;
            margin: 0 auto 20px;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .profile-stat {
            text-align: center;
            padding: 15px;
            background-color: var(--bg-light);
            border-radius: var(--radius-md);
        }
        
        .profile-stat h3 {
            margin-bottom: 5px;
            font-size: 1.5rem;
        }
        
        .profile-stat p {
            margin-bottom: 0;
            font-size: 0.9rem;
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
            cursor: pointer;
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
            cursor: pointer;
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
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }
        
        .mobile-nav {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: var(--bg-white);
            box-shadow: var(--shadow-md);
            flex-direction: column;
            padding: 20px;
            gap: 15px;
        }
        
        .mobile-nav.active {
            display: flex;
        }
        
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .profile-container {
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
            
            .page {
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
                <a   class="logo" onclick="showPage('dashboard')">
                    <img src="../../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a onclick="showPage('dashboard')">Dashboard</a>
                    <a onclick="showPage('users')">Users</a>
                    <!-- <a onclick="showPage('reports')">Reports</a> -->
                    <a onclick="showPage('settings')">Settings</a>
                </nav>
                
                <div class="nav-actions">
                    <button class="btn btn-secondary" onclick="showModal('profile-modal')">Profile</button>
                     <button class="btn btn-primary" onclick="window.location.href='../../controllers/auth/logout.php'">Logout</button>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="mobile-nav" id="mobileNav">
                <a onclick="showPage('dashboard'); hideMobileNav()">Dashboard</a>
                <a onclick="showPage('users'); hideMobileNav()">Users</a>
                <a onclick="showPage('reports'); hideMobileNav()">Reports</a>
                <a onclick="showPage('settings'); hideMobileNav()">Settings</a>
                <div class="mobile-nav-actions">
                    <button class="btn btn-secondary" onclick="showModal('profile-modal'); hideMobileNav()">Profile</button>
                    <button class="btn btn-primary">Logout</button>
                </div>
            </div>
        </div>
    </header>

    <section id="dashboard" class="page active">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, Roniel C. Carbon</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">SA</div>
                    <div>
                        <p>Roniel C. Carbon</p>
                        <small>Administrator Account</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalUsers ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalDoctors ?></h3>
                        <p>Doctors</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalSecretaries ?></h3>
                        <p>Secretaries</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-procedures"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalPatients ?></h3>
                        <p>Patients</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>$3,240</h3>
                        <p>Revenue This Week</p>
                    </div>
                </div>
            </div>

            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>System Overview</h2>
                            <!-- f -->
                        </div>
                        
                        <div class="tabs">
                            <div class="tab active" data-tab="users">Users</div>
                            <div class="tab" data-tab="doctors">Doctors</div>
                            <div class="tab" data-tab="secretaries">Secretaries</div>
                            <div class="tab" data-tab="patients">Patients</div>
                            <div class="tab" data-tab="appointments">Appointments</div>
                        </div>
                        
                        <div class="tab-content active" id="users-tab">
                            <div class="search-box">
                                
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
                        
                        <div class="tab-content" id="doctors-tab">
                            
                            <div class="user-management-grid">
                                <?php if ($doctors && $doctors->num_rows > 0 ): ?>
                                    <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                        <div class="user-card">
                                            <div class="user-avatar-large">
                                                <?= strtoupper(substr($doctor['name'], 0, 1)) ?>
                                            </div>
                                            <div class="user-details">
                                                <h3>Dr. <?= htmlspecialchars($doctor['name']) ?></h3>
                                                <p><?= htmlspecialchars($doctor['specialization'] ?? 'No specialization') ?></p>
                                                <p><?= htmlspecialchars($doctor['email']) ?></p>
                                                <p><span class="status-badge status-active">Active</span></p>
                                            </div>
                                            <div class="user-actions">
                                                <button class="btn btn-sm btn-secondary"
                                                        onclick="window.location.href='/Caresync-System/views/admin/edit_doctor.php?id=<?= htmlspecialchars($doctor['doctor_id']) ?>'">
                                                    Edit
                                                </button>
                                                <!-- <button class="btn btn-sm btn-info"
                                                        onclick="window.location.href='/Caresync-System/views/admin/schedule_doctor.php?id=<?= htmlspecialchars($doctor['doctor_id']) ?>'">
                                                    Schedule
                                                </button> -->
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">⚠️ No doctors found.</p>
                                <?php endif; ?>
                            </div>



                        </div>
                        
                        <div class="tab-content" id="secretaries-tab">
                            <div class="user-management-grid">
                                <?php if($secretaries && $secretaries->num_rows > 0):  ?>
                                    <?php while($sec = $secretaries->fetch_assoc()): ?> 
                                    
                                        <div class="user-card">
                                                <div class="user-avatar-large">
                                                        <?= strtoupper(substr($sec['name'], 0, 1)) ?>
                                                </div>
                                                <div class="user-details">
                                                    <h3><?= htmlspecialchars($sec['name']) ?></h3>
                                                    <p><?= htmlspecialchars($sec['department'] ?? 'No Department') ?></p>
                                                    <p><?= htmlspecialchars($sec['email']) ?></p>
                                                    <p><span class="status-badge status-active"><?= htmlspecialchars($sec['doctor_name'] ?? 'No doctor assigned') ?></span></p>
                                                </div>

                                                <!-- balikan natin ito mamaya -->
                                                <div class="user-actions">
                                                    <button class="btn btn-sm btn-secondary"
                                                            onclick="window.location.href='/Caresync-System/views/admin/edit_secretary.php?id=<?= htmlspecialchars($sec['secretary_id']) ?>'"> 
                                                        Edit
                                                    </button>

                                                    <!-- <button class="btn btn-sm btn-info"
                                                            onclick="window.location.href='/Caresync-System/views/admin/schedule_doctor.php?id=<?= htmlspecialchars($sec['secretary_id']) ?>'">
                                                        Schedule
                                                    </button> -->
                                                </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">⚠️ No secretaries found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="tab-content" id="patients-tab">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Patient ID</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Doctor</th>
                                            <th>Last Visit</th>
                                            <!-- <th>Status</th> -->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($resultPatientSystemOver->num_rows > 0): ?>
                                            <?php while($row = $resultPatientSystemOver->fetch_assoc()):?>
                                        
                                                <tr>
                                                    <td><?= htmlspecialchars($row['patient_id']); ?></td>
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
                        
                        <div class="tab-content" id="appointments-tab">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Appointment ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <!-- <th>Type</th> -->
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($appointments)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No appointments found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?> - <?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                                    <td>
                                                        <span class="status-badge status-active"><?php echo htmlspecialchars($appointment['status']) ?> </span>
                                                    </td>
                                                    <td>
                                                        <a href="Reschedule_Appointment.php?appointment_id=<?php echo htmlspecialchars($appointment['appointment_id']); ?>" 
                                                            class="btn btn-sm btn-secondary">Reschedule</a>
                                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="right-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Upcoming Appointments</h2>
                        </div>
                        
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon" style="background-color: rgba(46, 137, 73, 0.1); color: var(--primary);">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Patient Name Here</h4>
                                    <p>With Dr. Name Here - 10:00 AM</p>
                                    <div class="activity-time">Today</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon" style="background-color: rgba(33, 150, 243, 0.1); color: #2196F3;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Patient Name Here</h4>
                                    <p>With Dr. Name Here - 11:30 AM</p>
                                    <div class="activity-time">Today</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon" style="background-color: rgba(255, 152, 0, 0.1); color: #FF9800;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Patient Name Here</h4>
                                    <p>With Dr. Name Here - 2:15 PM</p>
                                    <div class="activity-time">Today</div>
                                </div>
                            </li>
                        </ul>
                    </div>                    
                </div>
            </div>
        </div>
    </section>

    <section id="users" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>User Management</h1>
                <button class="btn btn-primary" onclick="showModal('add-user-modal')">Add New User</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>All Users</h2>
                    
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
        </div>
    </section>

    <section id="settings" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>System Settings</h1>
                <p>Configure system preferences</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>General Settings</h2>
                </div>
                <form id="general-settings-form">
                    <div class="form-group">
                        <label for="clinic-name">Clinic Name</label>
                        <input type="text" id="clinic-name" class="form-control" value="CareSync Medical Center">
                    </div>
                    
                    <div class="form-group">
                        <label for="clinic-address">Clinic Address</label>
                        <textarea id="clinic-address" class="form-control" rows="3">Address Here</textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="clinic-phone">Clinic Phone</label>
                            <input type="tel" id="clinic-phone" class="form-control" value="Phone Here">
                        </div>
                        
                        <div class="form-group">
                            <label for="clinic-email">Clinic Email</label>
                            <input type="email" id="clinic-email" class="form-control" value="contact@caresync.com">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Clinic Hours</h2>
                </div>
                <form id="clinic-hours-form">
                    <div class="form-group">
                        <label for="opening-time">Opening Time</label>
                        <input type="time" id="opening-time" class="form-control" value="08:00">
                    </div>
                    
                    <div class="form-group">
                        <label for="closing-time">Closing Time</label>
                        <input type="time" id="closing-time" class="form-control" value="17:00">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save Hours</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div id="add-user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New User</h2>
                <span class="close" onclick="closeModal('add-user-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="add-user-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" class="form-control" required>
                            <option value="">Select role</option>
                            <option value="doctor">Doctor</option>
                            <option value="secretary">Secretary</option>
                            <option value="patient">Patient</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="specialization">Specialization/Position</label>
                        <input type="text" id="specialization" class="form-control">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-user-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <span class="close" onclick="closeModal('edit-user-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit-first-name">First Name</label>
                            <input type="text" id="edit-first-name" class="form-control" value="Name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit-last-name">Last Name</label>
                            <input type="text" id="edit-last-name" class="form-control" value="Here" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-email">Email Address</label>
                        <input type="email" id="edit-email" class="form-control" value="email@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-role">User Role</label>
                        <select id="edit-role" class="form-control" required>
                            <option value="doctor">Doctor</option>
                            <option value="secretary">Secretary</option>
                            <option value="patient">Patient</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-specialization">Specialization/Position</label>
                        <input type="text" id="edit-specialization" class="form-control" value="Specialization">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('edit-user-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="appointment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Schedule New Appointment</h2>
                <span class="close" onclick="closeModal('appointment-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <div class="form-group">
                        <label for="patient">Select Patient</label>
                        <select id="patient" class="form-control" required>
                            <option value="">Select patient</option>
                            <option value="patient1">Patient Name Here</option>
                            <option value="patient2">Patient Name Here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="doctor">Select Doctor</label>
                        <select id="doctor" class="form-control" required>
                            <option value="">Select doctor</option>
                            <option value="doctor1">Dr. Name Here</option>
                            <option value="doctor2">Dr. Name Here</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="appointment-date">Date</label>
                            <input type="date" id="appointment-date" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment-time">Time</label>
                            <input type="time" id="appointment-time" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('appointment-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule</button>
                    </div>
                </form>
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
                <form id="reports-form">
                    <div class="form-group">
                        <label for="report-type">Report Type</label>
                        <select id="report-type" class="form-control">
                            <option value="appointments">Appointments Report</option>
                            <option value="users">User Activity Report</option>
                            <option value="patients">Patient Report</option>
                            <option value="financial">Financial Report</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-date">Start Date</label>
                            <input type="date" id="start-date" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="end-date">End Date</label>
                            <input type="date" id="end-date" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="report-format">Report Format</label>
                        <select id="report-format" class="form-control">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('reports-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="settings-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>System Settings</h2>
                <span class="close" onclick="closeModal('settings-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="settings-form">
                    <div class="form-group">
                        <label for="system-name">System Name</label>
                        <input type="text" id="system-name" class="form-control" value="CareSync">
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select id="timezone" class="form-control">
                            <option value="est">Eastern Time</option>
                            <option value="cst">Central Time</option>
                            <option value="pst">Pacific Time</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language" class="form-control">
                            <option value="en">English</option>
                            <option value="es">Spanish</option>
                            <option value="fr">French</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('settings-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>My Profile</h2>
                <span class="close" onclick="closeModal('profile-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="user-info">
                    <div class="user-avatar">SA</div>
                    <div>
                        <h3>System Administrator</h3>
                        <p>Administrator Account</p>
                        <p>admin@caresync.com</p>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal('profile-modal')">Close</button>
                    <button class="btn btn-primary" onclick="showModal('edit-profile-modal')">Edit Profile</button>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-profile-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <span class="close" onclick="closeModal('edit-profile-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="edit-profile-form">
                    <div class="form-group">
                        <label for="edit-name">Full Name</label>
                        <input type="text" id="edit-name" class="form-control" value="System Administrator" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" id="edit-email" class="form-control" value="admin@caresync.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="tel" id="edit-phone" class="form-control" value="+1 (555) 123-4567">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('edit-profile-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CareSync</h3>
                    <p>A comprehensive clinic management system designed to streamline operations and improve patient care.</p>
                    <div class="social-links">
                        <a><i class="fab fa-facebook-f"></i></a>
                        <a><i class="fab fa-twitter"></i></a>
                        <a><i class="fab fa-linkedin-in"></i></a>
                        <a><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a onclick="showPage('dashboard')">Dashboard</a></li>
                        <li><a onclick="showPage('users')">Users</a></li>
                        <li><a onclick="showPage('reports')">Reports</a></li>
                        <li><a onclick="showPage('settings')">Settings</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul>
                        <li><a>Help Center</a></li>
                        <li><a>Contact Us</a></li>
                        <li><a>Privacy Policy</a></li>
                        <li><a>Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 CareSync Clinic Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function showPage(pageId) {
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            document.getElementById(pageId).classList.add('active');
        }

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab + '-tab').classList.add('active');
            });
        });
        
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileNav = document.getElementById('mobileNav');
        
        mobileMenuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
        });
        
        function hideMobileNav() {
            mobileNav.classList.remove('active');
        }
        
        document.getElementById('add-user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('User added successfully!');
            closeModal('add-user-modal');
            this.reset();
        });
        
        document.getElementById('edit-user-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('User updated successfully!');
            closeModal('edit-user-modal');
        });
        
        document.getElementById('appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Appointment scheduled successfully!');
            closeModal('appointment-modal');
        });
        
        document.getElementById('reports-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Report generation started!');
            closeModal('reports-modal');
        });
        
        document.getElementById('settings-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('System settings updated successfully!');
            closeModal('settings-modal');
        });
        
        document.getElementById('general-settings-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('General settings updated successfully!');
        });
        
        document.getElementById('clinic-hours-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Clinic hours updated successfully!');
        });
        
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Profile updated successfully!');
            closeModal('edit-profile-modal');
        });
    </script>
</body>
</html>