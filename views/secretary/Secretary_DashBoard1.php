<?php
$sessionPath = __DIR__ . '/../../controllers/auth/session.php';

if (!file_exists($sessionPath)) {
    echo "session.php not found";
    exit;
}

require_once $sessionPath;
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '../../../controllers/admin/secretaryController.php';
require_once __DIR__ . '/../../controllers/appointment/AppointmentController.php';

if (empty($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit;
}
// Redirect if not logged in or wrong role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'secretary') {
    header("Location: ../../login/login.php");
    exit;
}

$secretaryId = intval($_SESSION['user_id']); //sanitizing 


$secretaryController = new SecretaryController($conn);
$secretary = $secretaryController->getSecretaryData($secretaryId);

$appointmentController = new AppointmentController($conn);
$totalAppointment = $appointmentController->getTotalAppointments();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Secretary Dashboard</title>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .appointments-table th,
        .appointments-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
        }
        
        .appointments-table th {
            color: var(--text-medium);
            font-weight: 600;
        }
        
        .appointments-table tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-confirmed {
            background-color: rgba(46, 137, 73, 0.1);
            color: var(--primary);
        }
        
        .status-pending {
            background-color: rgba(173, 80, 87, 0.1);
            color: var(--danger);
        }
        
        .status-waiting {
            background-color: rgba(46, 96, 61, 0.1);
            color: var(--text-medium);
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
        
        .patient-queue {
            list-style: none;
        }
        
        .queue-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 10px;
        }
        
        .queue-info h4 {
            margin-bottom: 5px;
        }
        
        .queue-time {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .queue-status {
            font-weight: 600;
        }
        
        .prescription-requests {
            list-style: none;
        }
        
        .prescription-request-item {
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 15px;
        }
        
        .request-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .request-patient {
            font-weight: 600;
            color: var(--primary);
        }
        
        .request-date {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .request-details {
            color: var(--text-medium);
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
            max-width: 800px;
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
            max-height: 70vh;
            overflow-y: auto;
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
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .page {
            display: none;
            padding: 140px 0 40px;
        }
        
        .page.active {
            display: block;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 1024px) {
            .dashboard-grid {
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
            
            .dashboard, .page {
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
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            h1 { font-size: 2rem; }
            h2 { font-size: 1.75rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo" onclick="showPage('dashboard')">
                    <img src="../../assets/images/3.png"  alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a onclick="showPage('dashboard')">Dashboard</a>
                    <a onclick="showPage('appointments')">Appointments</a>
                    <a onclick="showPage('patients')">Patients</a>
                    <a onclick="showPage('queue')">Queue</a>
                    <a onclick="showPage('billing')">Billing</a>
                </nav>
                
                <div class="nav-actions">
                    <button class="btn btn-secondary" onclick="showModal('profile-modal')">Profile</button>
                     <button class="btn btn-primary" onclick="window.location.href='../../controllers/auth/logout.php'">Logout</button>
                </div>
                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <section id="dashboard" class="page active">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1>Secretary</h1>
                    <p>Welcome back, <?= htmlspecialchars($secretary['name']) ?></p>
                    <p> Assigned Doctor: <?= htmlspecialchars($secretary['doctor_name']) ?></p>
                </div>
                
                <div class="user-info">
                     <div class="user-avatar" id="userAvatar">
                            <?= strtoupper(substr($secretary['name'], 0, 2)) ?>
                        </div>
                    <div>
                        <p><?= htmlspecialchars($secretary['name']) ?></p>
                        <small>Clinic Secretary</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="total-appointments">48</h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="queue-count">8</h3>
                        <p>Patients in Queue</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-prescription"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="prescription-count">15</h3>
                        <p>Prescriptions to Process</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Appointment Requests</h2>
                            <button class="btn btn-secondary" onclick="showModal('appointments-modal')">View All</button>
                        </div>
                        
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Preferred Date</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name here</td>
                                    <td>Oct 18, 2023</td>
                                    <td>Dr. Name here</td>
                                    <td><span class="status-badge status-pending">Review</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>Oct 20, 2023</td>
                                    <td>Dr. Name here</td>
                                    <td><span class="status-badge status-pending">Review</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>Oct 22, 2023</td>
                                    <td>Dr. Name here</td>
                                    <td><span class="status-badge status-pending">Review</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="appointments" class="page">
        <div class="container">
            <!-- <div class="dashboard-header">
                <h1>Appointments</h1>
                <button class="btn btn-primary" onclick="showModal('schedule-modal')">New Appointment</button>
            </div> -->
            
            <div class="card">
                <div class="card-header">
                    <h2>Today's Appointments</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>9:00 AM</td>
                            <td>Name here</td>
                            <td>Dr. Name here</td>
                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('schedule-modal')">Edit</button>
                                <button class="btn btn-sm btn-danger">Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>10:30 AM</td>
                            <td>Name here</td>
                            <td>Dr. Name here</td>
                            <td><span class="status-badge status-waiting">Waiting</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('schedule-modal')">Edit</button>
                                <button class="btn btn-sm btn-danger">Cancel</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="patients" class="page">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Patient List</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PID-001</td>
                            <td>Name here</td>
                            <td>Phone here</td>
                            <td>Date here</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="showModal('register-modal')">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>PID-002</td>
                            <td>Name here</td>
                            <td>Phone here</td>
                            <td>Date here</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="showModal('register-modal')">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="queue" class="page">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Current Queue</h2>
                </div>
                <ul class="patient-queue">
                    <li class="queue-item">
                        <div class="queue-info">
                            <h4>Name here</h4>
                            <p class="queue-time">Arrived: Time here</p>
                        </div>
                        <div class="queue-status status-waiting">Waiting</div>
                        <button class="btn btn-primary">Check In</button>
                    </li>
                    <li class="queue-item">
                        <div class="queue-info">
                            <h4>Name here</h4>
                            <p class="queue-time">Arrived: Time here</p>
                        </div>
                        <div class="queue-status status-confirmed">With Doctor</div>
                        <button class="btn btn-secondary">Complete</button>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section id="billing" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Billing</h1>
                <button class="btn btn-primary" onclick="showModal('invoice-modal')">Create Invoice</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Pending Payments</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>INV-001</td>
                            <td>Name here</td>
                            <td>$Amount here</td>
                            <td>Date here</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Process Payment</button>
                            </td>
                        </tr>
                        <tr>
                            <td>INV-002</td>
                            <td>Name here</td>
                            <td>$Amount here</td>
                            <td>Date here</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Process Payment</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="queue-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Manage Patient Queue</h2>
                <span class="close" onclick="closeModal('queue-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="queue-management">
                    <div class="queue-item">
                        <div class="queue-info">
                            <h4>Name here</h4>
                            <p class="queue-time">Arrived: 8:45 AM</p>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary">Check In</button>
                            <button class="btn btn-danger">Remove</button>
                        </div>
                    </div>
                    <div class="queue-item">
                        <div class="queue-info">
                            <h4>Name here</h4>
                            <p class="queue-time">Arrived: 9:20 AM</p>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary">Check In</button>
                            <button class="btn btn-danger">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="appointments-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>All Appointment Requests</h2>
                <span class="close" onclick="closeModal('appointments-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Preferred Date</th>
                            <th>Doctor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Name here</td>
                            <td>Oct 18, 2023</td>
                            <td>Dr. Name here</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Approve</button>
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Name here</td>
                            <td>Oct 20, 2023</td>
                            <td>Dr. Name here</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Approve</button>
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="schedule-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Schedule Appointment</h2>
                <span class="close" onclick="closeModal('schedule-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="schedule-form">
                    <div class="form-group">
                        <label for="patient-name">Patient Name</label>
                        <input type="text" id="patient-name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="doctor">Doctor</label>
                        <select id="doctor" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <option value="dr-name">Dr. Name here</option>
                            <option value="dr-name">Dr. Name here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment-date">Date</label>
                        <input type="date" id="appointment-date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment-time">Time</label>
                        <input type="time" id="appointment-time" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('schedule-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="register-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Register Patient</h2>
                <span class="close" onclick="closeModal('register-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="register-form">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('register-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="prescription-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Process Prescription</h2>
                <span class="close" onclick="closeModal('prescription-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="prescription-form">
                    <div class="form-group">
                        <label for="prescription-patient">Patient Name</label>
                        <input type="text" id="prescription-patient" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="medication">Medication</label>
                        <input type="text" id="medication" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dosage">Dosage</label>
                        <input type="text" id="dosage" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea id="instructions" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('prescription-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Process</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="call-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Call Patient</h2>
                <span class="close" onclick="closeModal('call-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="call-form">
                    <div class="form-group">
                        <label for="call-patient">Patient Name</label>
                        <input type="text" id="call-patient" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="call-reason">Reason for Call</label>
                        <select id="call-reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="reminder">Appointment Reminder</option>
                            <option value="followup">Follow-up</option>
                            <option value="results">Test Results</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="call-notes">Notes</label>
                        <textarea id="call-notes" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('call-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Log Call</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="add-queue-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Patient to Queue</h2>
                <span class="close" onclick="closeModal('add-queue-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="add-queue-form">
                    <div class="form-group">
                        <label for="queue-patient">Patient Name</label>
                        <input type="text" id="queue-patient" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-queue-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add to Queue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="invoice-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create Invoice</h2>
                <span class="close" onclick="closeModal('invoice-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="invoice-form">
                    <div class="form-group">
                        <label for="invoice-patient">Patient Name</label>
                        <input type="text" id="invoice-patient" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="invoice-amount">Amount</label>
                        <input type="number" id="invoice-amount" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="invoice-service">Service</label>
                        <input type="text" id="invoice-service" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('invoice-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
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
                    <div class="user-avatar">NH</div>
                    <div>
                        <h3><?= htmlspecialchars($secretary['name']) ?></h3>
                        <p>Clinic Secretary</p>
                        <p><?= htmlspecialchars($secretary['email']) ?></p>
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
                    <input type="text" id="edit-name" name="name" class="form-control"
                            value="<?= htmlspecialchars($secretary['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" name="email" class="form-control"
                            value="<?= htmlspecialchars($secretary['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="edit-phone">Phone</label>
                    <input type="tel" id="edit-phone" name="phone" class="form-control"
                            value="<?= htmlspecialchars($secretary['phone']) ?>">
                </div>

                <div class="form-group">
                    <label for="edit-address">Address</label>
                    <input type="text" id="edit-address" name="address" class="form-control"
                            value="<?= htmlspecialchars($secretary['address'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="edit-department">Department</label>
                    <input type="text" id="edit-department" name="department" class="form-control"
                            value="<?= htmlspecialchars($secretary['department'] ?? '') ?>">
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
                        <li><a onclick="showPage('appointments')">Appointments</a></li>
                        <li><a onclick="showPage('patients')">Patients</a></li>
                        <li><a onclick="showPage('queue')">Queue</a></li>
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
        
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            const navActions = document.querySelector('.nav-actions');
            
            if (navLinks.style.display === 'flex') {
                navLinks.style.display = 'none';
                navActions.style.display = 'none';
            } else {
                navLinks.style.display = 'flex';
                navActions.style.display = 'flex';
                navLinks.style.flexDirection = 'column';
                navActions.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '100%';
                navLinks.style.left = '0';
                navLinks.style.right = '0';
                navLinks.style.backgroundColor = 'var(--bg-white)';
                navLinks.style.padding = '20px';
                navLinks.style.boxShadow = 'var(--shadow-md)';
            }
        });
        document.getElementById("edit-profile-form").addEventListener("submit", function(e) {
            e.preventDefault(); // Stop normal reload

            const formData = new FormData(this);

            fetch("../../controllers/secretaries/UpdateSecretaryProfile.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                alert("Profile updated successfully!");
                closeModal('edit-profile-modal');
                location.reload(); // refresh to show updated info
                } else {
                alert("Failed to update: " + data.message);
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("Something went wrong. Check console for details.");
            });
            });
    </script>
</body>
</html>