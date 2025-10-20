<?php
require_once __DIR__ . '/../controllers/auth/session.php'; //our session 
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../controllers/appointment/AppointmentController.php';
require_once __DIR__ . '/../model/patientDashboard/DoctorModel.php';

$appointmentController = new AppointmentController($conn);
$doctors = $appointmentController->getAvailableDoctors(); // code for getting all doctor


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $patient_id = $_SESSION['patient_id']; // Set during patient login
    $doctor_id = intval($_POST['doctor_id']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'] ?? '';

    $result = $appointmentController->bookAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);
    
    if ($result['success']) {
        echo '<div class="alert alert-success">' . $result['message'] . '</div>';
    } else {
        echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
    }
}
//FOR UPCOMING APPOINTMENT CALLING METHOD
// Example: get upcoming appointments for the logged-in patient
$patient_id = $_SESSION['patient_id'] ?? 0;
$appointments = $appointmentController->showUpcomingAppointments($patient_id);

$stats = $stats ?? [
    'upcomingAppointments' => 0,
    'activePrescriptions' => 0,
    'pendingBills' => 0,
    'healthRecords' => 0,
];


$prescriptions = $prescriptions ?? [];

$bills = $bills ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Patient Dashboard</title>
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
        
        .status-confirmed {
            background-color: rgba(46, 137, 73, 0.1);
            color: var(--primary);
        }
        
        .status-pending {
            background-color: rgba(173, 80, 87, 0.1);
            color: var(--danger);
        }
        
        .status-completed {
            background-color: rgba(46, 96, 61, 0.1);
            color: var(--text-medium);
        }
        
        .status-processing {
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
        
        .prescription-list {
            list-style: none;
        }
        
        .prescription-item {
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 15px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .prescription-doctor {
            font-weight: 600;
            color: var(--primary);
        }
        
        .prescription-date {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .prescription-details {
            color: var(--text-medium);
        }
        
        .health-record-list {
            list-style: none;
        }
        
        .health-record-item {
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 15px;
        }
        
        .record-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .record-type {
            font-weight: 600;
            color: var(--primary);
        }
        
        .record-date {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .record-details {
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #2e8949;
            color: white;
        }

        .btn-secondary {
            background-color: #8e979eff;
            color: white;
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
                <a href="#" class="logo" onclick="showPage('dashboard')">
                    <img src="../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a onclick="showPage('dashboard')">Dashboard</a>
                    <a onclick="showPage('appointments')">Appointments</a>
                    <a onclick="showPage('prescriptions')">Prescriptions</a>
                    <a onclick="showPage('health-records')">Health Records</a>
                    <a onclick="showPage('billing')">Billing</a>
                </nav>
                
                <div class="nav-actions">
                    <button class="btn btn-secondary" onclick="showModal('profile-modal')">Profile</button>
                    <button class="btn btn-primary">Logout</button>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="mobile-nav" id="mobileNav">
                <a onclick="showPage('dashboard'); hideMobileNav()">Dashboard</a>
                <a onclick="showPage('appointments'); hideMobileNav()">Appointments</a>
                <a onclick="showPage('prescriptions'); hideMobileNav()">Prescriptions</a>
                <a onclick="showPage('health-records'); hideMobileNav()">Health Records</a>
                <a onclick="showPage('billing'); hideMobileNav()">Billing</a>
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
                    <h1>Patient Dashboard</h1>
                    <p>Welcome back, Name here</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">NH</div>
                    <div>
                        <p>Name here</p>
                        <small>Patient ID: ID Here</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['upcomingAppointments'] ?></h3>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-prescription"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['activePrescriptions'] ?></h3>
                        <p>Active Prescriptions</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['pendingBills'] ?></h3>
                        <p>Pending Bills</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['healthRecords'] ?></h3>
                        <p>Health Records</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Upcoming Appointments</h2>
                            <button class="btn btn-secondary" onclick="showPage('appointments')">View All</button>
                        </div>
                        
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($appointments)): ?>
                                    <?php foreach($appointments as $appt): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                                            <td><?= htmlspecialchars($appt['date_time']) ?></td>
                                            <td><?= htmlspecialchars($appt['type']) ?></td>
                                            <td>
                                                <?php
                                                    $status = strtolower($appt['status']);
                                                    $badgeClass = match($status) {
                                                        'confirmed' => 'status-confirmed',
                                                        'pending' => 'status-pending',
                                                        'cancelled' => 'status-cancelled',
                                                        default => 'status-unknown'
                                                    };
                                                ?>
                                                <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($appt['status']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No upcoming appointments found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Current Prescriptions</h2>
                            <button class="btn btn-secondary" onclick="showPage('prescriptions')">View All</button>
                        </div>
                        
                        <ul class="prescription-list">
                            <li class="prescription-item">
                                <div class="prescription-header">
                                    <span class="prescription-doctor">Dr. Name here</span>
                                    <span class="prescription-date">Date Here</span>
                                </div>
                                <div class="prescription-details">
                                    <p><strong>Medication:</strong> Medication Here</p>
                                    <p><strong>Dosage:</strong> Dosage Here</p>
                                    <p><strong>Refills:</strong> Refills Here</p>
                                </div>
                            </li>
                            <li class="prescription-item">
                                <div class="prescription-header">
                                    <span class="prescription-doctor">Dr. Name here</span>
                                    <span class="prescription-date">Date Here</span>
                                </div>
                                <div class="prescription-details">
                                    <p><strong>Medication:</strong> Medication Here</p>
                                    <p><strong>Dosage:</strong> Dosage Here</p>
                                    <p><strong>Refills:</strong> Refills Here</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="right-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Quick Actions</h2>
                        </div>
                        
                        <div class="quick-actions">
                            <div class="action-btn" onclick="showModal('book-appointment-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <p>Book Appointment</p>
                            </div>
                            
                            <div class="action-btn" onclick="showModal('request-refill-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <p>Request Refill</p>
                            </div>
                            
                            <div class="action-btn" onclick="showModal('request-records-modal')">
                                <div class="action-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <p>Request Records</p>
                            </div>
                            
                            <div class="action-btn" onclick="showPage('billing')">
                                <div class="action-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <p>Pay Bill</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Activity</h2>
                        </div>
                        
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Appointment Booked</h4>
                                    <p>Follow-up with Dr. Name here</p>
                                    <div class="activity-time">Time Here</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Prescription Refilled</h4>
                                    <p>Medication Here</p>
                                    <div class="activity-time">Time Here</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Bill Paid</h4>
                                    <p>Consultation fee - Amount Here</p>
                                    <div class="activity-time">Time Here</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="appointments" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>My Appointments</h1>
                <button class="btn btn-primary" onclick="showModal('book-appointment-modal')">Book Appointment</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Appointments</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($appointments)): ?>
                            <?php foreach($appointments as $appt): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                                    <td><?= htmlspecialchars($appt['date_time']) ?></td>
                                    <td><?= htmlspecialchars($appt['type']) ?></td>
                                    <td>
                                        <?php
                                            $status = strtolower($appt['status']);
                                            $badgeClass = match($status) {
                                                'confirmed' => 'status-confirmed',
                                                'pending' => 'status-pending',
                                                'cancelled' => 'status-cancelled',
                                                default => 'status-unknown'
                                            };
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($appt['status']) ?></span>
                                    </td>
                                     <td>
                                        <button class="btn btn-sm btn-secondary" onclick="showModal('reschedule-modal')">Reschedule</button>
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No upcoming appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
           
            <div class="card">
                <div class="card-header">
                    <h2>Appointment History</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dr. Name here</td>
                            <td>Date Here - Time Here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Dr. Name here</td>
                            <td>Date Here - Time Here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                        </tr>
                        <tr>
                            <td>Dr. Name here</td>
                            <td>Date Here - Time Here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="prescriptions" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>My Prescriptions</h1>
                <button class="btn btn-primary" onclick="showModal('request-refill-modal')">Request Refill</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Active Prescriptions</h2>
                </div>
                <ul class="prescription-list">
                    <?php if (!empty($prescriptions)): ?>
                        <?php foreach ($prescriptions as $p): ?>
                            <li class="prescription-item">
                                <div class="prescription-header">
                                    <span class="prescription-doctor"><?= htmlspecialchars($p['doctor_name']) ?></span>
                                    <span class="prescription-date"><?= htmlspecialchars($p['date_prescribed']) ?></span>
                                </div>
                                <div class="prescription-details">
                                    <p><strong>Medication:</strong> <?= htmlspecialchars($p['medication']) ?></p>
                                    <p><strong>Dosage:</strong> <?= htmlspecialchars($p['dosage']) ?></p>
                                    <p><strong>Instructions:</strong> <?= htmlspecialchars($p['instructions']) ?></p>
                                    <p><strong>Refills:</strong> <?= htmlspecialchars($p['refills']) ?></p>
                                    <p><strong>Expires:</strong> <?= htmlspecialchars($p['expires']) ?></p>
                                </div>
                                <div class="form-actions">
                                    <button class="btn btn-primary" onclick="showModal('request-refill-modal')">Request Refill</button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="prescription-item">
                            <p>No active prescriptions found.</p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Prescription History</h2>
                </div>
                <ul class="prescription-list">
                    <li class="prescription-item">
                        <div class="prescription-header">
                            <span class="prescription-doctor">Dr. Name here</span>
                            <span class="prescription-date">Date Here</span>
                        </div>
                        <div class="prescription-details">
                            <p><strong>Medication:</strong> Medication Here</p>
                            <p><strong>Dosage:</strong> Dosage Here</p>
                            <p><strong>Instructions:</strong> Instructions Here</p>
                            <p><strong>Status:</strong> Status Here</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section id="health-records" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Health Records</h1>
                <button class="btn btn-primary" onclick="showModal('request-records-modal')">Request Records</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Medical History</h2>
                </div>
                <ul class="health-record-list">
                    <li class="health-record-item">
                        <div class="record-header">
                            <span class="record-type">Record Type Here</span>
                            <span class="record-date">Date Here</span>
                        </div>
                        <div class="record-details">
                            <p><strong>Ordered by:</strong> Dr. Name here</p>
                            <p><strong>Results:</strong> Results Here</p>
                            <p><strong>Notes:</strong> Notes Here</p>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary">Download</button>
                        </div>
                    </li>
                    <li class="health-record-item">
                        <div class="record-header">
                            <span class="record-type">Record Type Here</span>
                            <span class="record-date">Date Here</span>
                        </div>
                        <div class="record-details">
                            <p><strong>Doctor:</strong> Dr. Name here</p>
                            <p><strong>Findings:</strong> Findings Here</p>
                            <p><strong>Recommendations:</strong> Recommendations Here</p>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary">Download</button>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Vital Signs History</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Blood Pressure</th>
                            <th>Heart Rate</th>
                            <th>Weight</th>
                            <th>Temperature</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Date Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                        </tr>
                        <tr>
                            <td>Date Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                        </tr>
                        <tr>
                            <td>Date Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                            <td>Value Here</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Record Request History</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Request Date</th>
                            <th>Record Type</th>
                            <th>Status</th>
                            <th>Estimated Completion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Date Here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-processing">Processing</span></td>
                            <td>Date Here</td>
                            <td>
                                <button class="btn btn-sm btn-secondary">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Date Here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-completed">Ready</span></td>
                            <td>Date Here</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Download</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="billing" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Billing & Payments</h1>
                <button class="btn btn-primary" onclick="showModal('payment-modal')">Make Payment</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Pending Bills</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($bills)): ?>
                            <?php foreach($bills as $bill): ?>
                                <tr>
                                    <td><?= htmlspecialchars($bill['invoice_number']) ?></td>
                                    <td><?= htmlspecialchars($bill['service']) ?></td>
                                    <td><?= htmlspecialchars($bill['bill_date']) ?></td>
                                    <td><?= htmlspecialchars(number_format($bill['amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars($bill['due_date']) ?></td>
                                    <td>
                                        <span class="status-badge status-pending"><?= htmlspecialchars($bill['status']) ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="showModal('payment-modal')">Pay Now</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No pending bills found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Payment History</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ID Here</td>
                            <td>Service Here</td>
                            <td>Date Here</td>
                            <td>Amount Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-completed">Paid</span></td>
                        </tr>
                        <tr>
                            <td>ID Here</td>
                            <td>Service Here</td>
                            <td>Date Here</td>
                            <td>Amount Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-completed">Paid</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="book-appointment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book Appointment</h2>
                <span class="close" onclick="closeModal('book-appointment-modal')">&times;</span>
            </div>

            <div class="modal-body">
                <form id="book-appointment-form" method="POST" action="../controllers/appointment//AppointmentController.php">
                    
                    <!-- Hidden patient ID -->
                    <input type="hidden" name="patient_id" value="<?= $_SESSION['patient_id'] ?? '' ?>">
                    
                    <!-- Doctor Dropdown -->
                    <div class="form-group">
                        <label for="appointment-doctor">Select Doctor</label>
                        <select id="appointment-doctor" name="doctor_id" class="form-control" required>
                            <option value="">Select Doctor</option>
                            <?php if (!empty($doctors)): ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= htmlspecialchars($doctor['doctor_id']); ?>">
                                        <?= htmlspecialchars($doctor['name']); ?> - <?= htmlspecialchars($doctor['specialization']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>No doctors available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Appointment Type -->
                    <div class="form-group">
                        <label for="appointment-type">Appointment Type</label>
                        <select id="appointment-type" name="appointment_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="consultation">Consultation</option>
                            <option value="follow-up">Follow-up</option>
                            <option value="annual-checkup">Annual Checkup</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>

                    <!-- Date and Time -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="appointment-date">Preferred Date</label>
                            <input type="date" id="appointment-date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment-time">Preferred Time</label>
                            <input type="time" id="appointment-time" name="appointment_time" class="form-control" required>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="form-group">
                        <label for="appointment-reason">Reason for Visit</label>
                        <textarea id="appointment-reason" name="reason" class="form-control" rows="3" placeholder="Please describe your symptoms or reason for appointment..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('book-appointment-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div id="reschedule-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reschedule Appointment</h2>
                <span class="close" onclick="closeModal('reschedule-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="reschedule-form">
                    <div class="form-group">
                        <label for="reschedule-doctor">Doctor</label>
                        <input type="text" id="reschedule-doctor" class="form-control" value="Dr. Name here" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="reschedule-type">Appointment Type</label>
                        <input type="text" id="reschedule-type" class="form-control" value="Type Here" readonly>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reschedule-date">New Date</label>
                            <input type="date" id="reschedule-date" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="reschedule-time">New Time</label>
                            <input type="time" id="reschedule-time" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reschedule-reason">Reason for Rescheduling</label>
                        <textarea id="reschedule-reason" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('reschedule-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reschedule Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="request-refill-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Request Prescription Refill</h2>
                <span class="close" onclick="closeModal('request-refill-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="request-refill-form">
                    <div class="form-group">
                        <label for="refill-medication">Select Medication</label>
                        <select id="refill-medication" class="form-control" required>
                            <option value="">Select Medication</option>
                            <option value="medication">Medication Here</option>
                            <option value="medication">Medication Here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="refill-pharmacy">Preferred Pharmacy</label>
                        <input type="text" id="refill-pharmacy" class="form-control" value="Pharmacy Here">
                    </div>
                    
                    <div class="form-group">
                        <label for="refill-notes">Additional Notes</label>
                        <textarea id="refill-notes" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('request-refill-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Request Refill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="request-records-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Request Medical Records</h2>
                <span class="close" onclick="closeModal('request-records-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="request-records-form">
                    <div class="form-group">
                        <label for="records-type">Type of Records Needed</label>
                        <select id="records-type" class="form-control" required>
                            <option value="">Select Record Type</option>
                            <option value="full-history">Full Medical History</option>
                            <option value="lab-results">Lab Results Only</option>
                            <option value="visit-summary">Visit Summaries</option>
                            <option value="imaging">Imaging Reports</option>
                            <option value="vaccination">Vaccination Records</option>
                            <option value="specific">Specific Records</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="records-date-range">Date Range</label>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start-date">From</label>
                                <input type="date" id="start-date" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="end-date">To</label>
                                <input type="date" id="end-date" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="records-format">Preferred Format</label>
                        <select id="records-format" class="form-control">
                            <option value="digital">Digital Copy (PDF)</option>
                            <option value="printed">Printed Copy</option>
                            <option value="both">Both Digital and Printed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="records-purpose">Purpose of Request</label>
                        <textarea id="records-purpose" class="form-control" rows="3" placeholder="Please specify why you need these records..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="records-delivery">Delivery Method</label>
                        <select id="records-delivery" class="form-control">
                            <option value="portal">Patient Portal</option>
                            <option value="email">Email</option>
                            <option value="mail">Regular Mail</option>
                            <option value="pickup">In-Person Pickup</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('request-records-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Make Payment</h2>
                <span class="close" onclick="closeModal('payment-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="payment-form">
                    <div class="form-group">
                        <label for="payment-invoice">Invoice</label>
                        <select id="payment-invoice" class="form-control" required>
                            <option value="">Select Invoice</option>
                            <option value="inv-id">ID Here - Amount Here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment-method">Payment Method</label>
                        <select id="payment-method" class="form-control" required>
                            <option value="">Select Method</option>
                            <option value="credit-card">Credit Card</option>
                            <option value="debit-card">Debit Card</option>
                            <option value="bank-transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" class="form-control" placeholder="1234 5678 9012 3456">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry-date">Expiry Date</label>
                            <input type="text" id="expiry-date" class="form-control" placeholder="MM/YY">
                        </div>
                        
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" class="form-control" placeholder="123">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('payment-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Make Payment</button>
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
                        <h3>Name here</h3>
                        <p>Patient</p>
                        <p>email@example.com</p>
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
                        <label for="edit-email">Email</label>
                        <input type="email" id="edit-email" class="form-control" value="email@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="tel" id="edit-phone" class="form-control" value="Phone Here">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-address">Address</label>
                        <textarea id="edit-address" class="form-control" rows="3">Address Here</textarea>
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
                        <li><a onclick="showPage('prescriptions')">Prescriptions</a></li>
                        <li><a onclick="showPage('health-records')">Health Records</a></li>
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
        
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileNav = document.getElementById('mobileNav');
        
        mobileMenuBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
        });
        
        function hideMobileNav() {
            mobileNav.classList.remove('active');
        }
        
        document.getElementById('book-appointment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Appointment request submitted successfully!');
            closeModal('book-appointment-modal');
            this.reset();
        });
        
        document.getElementById('reschedule-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Appointment rescheduled successfully!');
            closeModal('reschedule-modal');
        });
        
        document.getElementById('request-refill-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Prescription refill request submitted!');
            closeModal('request-refill-modal');
        });
        
        document.getElementById('request-records-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Medical records request submitted successfully!');
            closeModal('request-records-modal');
            this.reset();
        });
        
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Payment processed successfully!');
            closeModal('payment-modal');
        });
        
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Profile updated successfully!');
            closeModal('edit-profile-modal');
        });
                // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Form validation
        document.getElementById('book-appointment-form')?.addEventListener('submit', function(e) {
            const appointmentDate = document.getElementById('appointment-date').value;
            const appointmentTime = document.getElementById('appointment-time').value;
            const selectedDateTime = new Date(appointmentDate + ' ' + appointmentTime);
            
            if (selectedDateTime < new Date()) {
                e.preventDefault();
                alert('Cannot book appointment in the past. Please select a future date and time.');
                return false;
            }
        });
    </script>
</body>
</html>