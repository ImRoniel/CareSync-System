<?php
$sessionPath = __DIR__ . '/../../controllers/auth/session.php';

if (!file_exists($sessionPath)) {
    echo "session.php not found";
    exit;
}

require_once $sessionPath;
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/admin/DoctorController.php';
require_once '../../controllers/appointment/AppointmentController.php';
if (empty($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit;
}
// Redirect if not logged in or wrong role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: ../../login/login.php");
    exit;
}

$doctorId = intval($_SESSION['user_id']); 
$doctorController = new DoctorController($conn);
$doctor = $doctorController->getDoctorData($doctorId);



$appointmentController = new AppointmentController($conn);
$appointmentsData = $appointmentController->getDoctorAppointments();
$todaysAppointments = $appointmentsData['success'] ? $appointmentsData['appointments'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Doctor Dashboard</title>
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
        
        .status-in-progress {
            background-color: rgba(46, 96, 61, 0.1);
            color: var(--text-medium);
        }
        
        .status-completed {
            background-color: rgba(46, 137, 73, 0.1);
            color: var(--primary);
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
                    <img src="../../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a onclick="showPage('dashboard')">Dashboard</a>
                    <a onclick="showPage('schedule')">Schedule</a>
                    <a onclick="showPage('patients')">Patients</a>
                    <a onclick="showPage('prescriptions')">Prescriptions</a>
                    <!-- <a onclick="showPage('reports')">Reports</a> -->
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
                <a onclick="showPage('schedule'); hideMobileNav()">Schedule</a>
                <a onclick="showPage('patients'); hideMobileNav()">Patients</a>
                <a onclick="showPage('prescriptions'); hideMobileNav()">Prescriptions</a>
                <a onclick="showPage('reports'); hideMobileNav()">Reports</a>
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
                    <h1>Doctor Dashboard</h1>
                    <p>Welcome back, <?= htmlspecialchars($doctor['name']) ?></p>
                    <p>Assigned Secretary: <?= htmlspecialchars($doctor['secretary_name']) ?></p>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($doctor['name'], 0, 2)) ?></div>
                    <div>
                        <p><?= htmlspecialchars($doctor['name']) ?></p>
                        <small><?php echo isset($doctor['specialization']) ? htmlspecialchars($doctor['specialization']) : 'N/A'; ?></p></small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>18</h3>
                        <p>Today's Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>142</h3>
                        <p>Total Patients</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Prescriptions Today</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>My Appointments</h2>
                            <div class="filter-options">
                                <select id="appointment-filter" onchange="filterAppointments()">
                                    <option value="all">All Appointments</option>
                                    <option value="today">Today Only</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <button class="btn btn-secondary" onclick="showPage('schedule')">View Schedule</button>
                            </div>
                        </div>
                        
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <!-- <th>Type</th> -->
                                    <th>Status</th>
                                    <th>Queue #</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="appointments-tbody">
                                <?php if (empty($todaysAppointments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No appointments found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($todaysAppointments as $appointment): ?>
                                    <tr data-appointment-id="<?php echo $appointment['appointment_id']; ?>" 
                                        data-date="<?php echo $appointment['appointment_date']; ?>"
                                        data-status="<?php echo $appointment['status']; ?>">
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                       
                                        <td>
                                            <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $appointment['queue_number'] ?: 'N/A'; ?></td>
                                        <td>
                                            <?php if ($appointment['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-success" onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>, 'approved')">Approve</button>
                                                <button class="btn btn-sm btn-danger" onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>, 'cancelled')">Cancel</button>
                                            <?php elseif ($appointment['status'] === 'approved'): ?>
                                                <button class="btn btn-sm btn-primary" onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>, 'completed')">Complete</button>
                                                <button class="btn btn-sm btn-warning" onclick="showAppointmentDetails(<?php echo $appointment['appointment_id']; ?>)">Details</button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" onclick="showAppointmentDetails(<?php echo $appointment['appointment_id']; ?>)">View</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- <div class="card">
                        <div class="card-header">
                            <h2>Patient Statistics</h2>
                            <button class="btn btn-secondary" onclick="showPage('reports')">View Report</button>
                        </div>
                        
                        <div class="chart-container">
                            <p>Patient Visit Trends Chart</p>
                        </div>
                    </div> -->
                </div>
                
                
            </div>
        </div>
    </section>

    <section id="schedule" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Schedule</h1>
                <button class="btn btn-primary" onclick="showModal('add-availability-modal')">Add Availability</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Weekly Schedule</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Date Here</td>
                            <td>Time Here</td>
                            <td>Name here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('appointment-details-modal')">Details</button>
                                <button class="btn btn-sm btn-danger">Cancel</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Date Here</td>
                            <td>Time Here</td>
                            <td>Name here</td>
                            <td>Type Here</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('appointment-details-modal')">Details</button>
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
            <div class="dashboard-header">
                <h1>Patients</h1>
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Search patients...">
                    <button class="btn btn-primary">Search</button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Patient List</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Last Visit</th>
                            <th>Next Appointment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ID Here</td>
                            <td>Name here</td>
                            <td>Date Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-confirmed">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Records</button>
                            </td>
                        </tr>
                        <tr>
                            <td>ID Here</td>
                            <td>Name here</td>
                            <td>Date Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-confirmed">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Records</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="prescriptions" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Prescriptions</h1>
                <button class="btn btn-primary" onclick="showModal('create-prescription-modal')">Create Prescription</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Recent Prescriptions</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Medication</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Name here</td>
                            <td>Medication Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-confirmed">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('prescription-details-modal')">View</button>
                                <button class="btn btn-sm btn-primary">Refill</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Name here</td>
                            <td>Medication Here</td>
                            <td>Date Here</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>
                                <button class="btn btn-sm btn-secondary" onclick="showModal('prescription-details-modal')">View</button>
                                <button class="btn btn-sm btn-primary">Renew</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="reports" class="page">
        <div class="container">
            <div class="dashboard-header">
                <h1>Reports & Analytics</h1>
                <button class="btn btn-primary" onclick="showModal('generate-report-modal')">Generate Report</button>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>Appointment Reports</h2>
                    </div>
                    <div class="chart-container">
                        <p>Appointment Statistics Chart</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Revenue Reports</h2>
                    </div>
                    <div class="chart-container">
                        <p>Revenue Analysis Chart</p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Report History</h2>
                </div>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Date Generated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Monthly Appointment Summary</td>
                            <td>Date Here</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Patient Demographics Analysis</td>
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

    <div id="add-availability-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Availability</h2>
                <span class="close" onclick="closeModal('add-availability-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="add-availability-form">
                    <div class="form-group">
                        <label for="availability-date">Date</label>
                        <input type="date" id="availability-date" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-time">Start Time</label>
                            <input type="time" id="start-time" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end-time">End Time</label>
                            <input type="time" id="end-time" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="appointment-duration">Appointment Duration</label>
                        <select id="appointment-duration" class="form-control" required>
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-availability-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Availability</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="create-prescription-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create Prescription</h2>
                <span class="close" onclick="closeModal('create-prescription-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="create-prescription-form">
                    <div class="form-group">
                        <label for="prescription-patient">Select Patient</label>
                        <select id="prescription-patient" class="form-control" required>
                            <option value="">Select Patient</option>
                            <option value="patient1">Name here</option>
                            <option value="patient2">Name here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="medication-name">Medication Name</label>
                        <input type="text" id="medication-name" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dosage">Dosage</label>
                            <input type="text" id="dosage" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="frequency">Frequency</label>
                            <input type="text" id="frequency" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="text" id="duration" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea id="instructions" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('create-prescription-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Prescription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="update-records-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Patient Records</h2>
                <span class="close" onclick="closeModal('update-records-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="update-records-form">
                    <div class="form-group">
                        <label for="record-patient">Select Patient</label>
                        <select id="record-patient" class="form-control" required>
                            <option value="">Select Patient</option>
                            <option value="patient1">Name here</option>
                            <option value="patient2">Name here</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="record-type">Record Type</label>
                        <select id="record-type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="diagnosis">Diagnosis</option>
                            <option value="treatment">Treatment Plan</option>
                            <option value="progress">Progress Notes</option>
                            <option value="lab">Lab Results</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="record-date">Date</label>
                        <input type="date" id="record-date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="record-notes">Notes</label>
                        <textarea id="record-notes" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('update-records-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Records</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="generate-report-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Generate Report</h2>
                <span class="close" onclick="closeModal('generate-report-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="generate-report-form">
                    <div class="form-group">
                        <label for="report-type">Report Type</label>
                        <select id="report-type" class="form-control" required>
                            <option value="">Select Report Type</option>
                            <option value="appointments">Appointments Report</option>
                            <!-- <option value="patients">Patient Statistics</option> -->
                            <option value="prescriptions">Prescription Analysis</option>
                            <option value="revenue">Revenue Report</option>
                            <option value="performance">Performance Metrics</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="report-start-date">Start Date</label>
                            <input type="date" id="report-start-date" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="report-end-date">End Date</label>
                            <input type="date" id="report-end-date" class="form-control">
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
                        <button type="button" class="btn btn-secondary" onclick="closeModal('generate-report-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="appointment-details-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <span class="close" onclick="closeModal('appointment-details-modal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="user-info">
                    <div class="user-avatar">NH</div>
                    <div>
                        <h3>Name here</h3>
                        <p>Patient</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><strong>Appointment Time:</strong></label>
                    <p>Date Here - Time Here</p>
                </div>
                
                <div class="form-group">
                    <label><strong>Appointment Type:</strong></label>
                    <p>Type Here</p>
                </div>
                
                <div class="form-group">
                    <label><strong>Reason for Visit:</strong></label>
                    <p>Reason Here</p>
                </div>
                
                <div class="form-actions">
                    <button class="btn btn-secondary" onclick="closeModal('appointment-details-modal')">Close</button>
                    <button class="btn btn-primary">Start Consultation</button>
                </div>
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
                    <div class="user-avatar">MC</div>
                    <div>
                        <h3>Dr. Name here</h3>
                        <p>Cardiologist</p>
                        <p>email@caresync.com</p>
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
                        <input type="email" id="edit-email" class="form-control" value="email@caresync.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-specialization">Specialization</label>
                        <input type="text" id="edit-specialization" class="form-control" value="Cardiologist" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="tel" id="edit-phone" class="form-control" value="Phone Here">
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
                        <li><a onclick="showPage('schedule')">Schedule</a></li>
                        <li><a onclick="showPage('patients')">Patients</a></li>
                        <li><a onclick="showPage('prescriptions')">Prescriptions</a></li>
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
        // JavaScript for filtering
        function filterAppointments() {
            const filter = document.getElementById('appointment-filter').value;
            const rows = document.querySelectorAll('#appointments-tbody tr');
            const today = new Date().toISOString().split('T')[0];
            
            rows.forEach(row => {
                const date = row.getAttribute('data-date');
                const status = row.getAttribute('data-status');
                let show = true;
                
                switch (filter) {
                    case 'today':
                        show = date === today;
                        break;
                    case 'upcoming':
                        show = date >= today && status !== 'completed' && status !== 'cancelled';
                        break;
                    case 'pending':
                        show = status === 'pending';
                        break;
                    case 'completed':
                        show = status === 'completed';
                        break;
                    // 'all' shows everything
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        setInterval(refreshAppointments, 30000);

        
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
        
        document.getElementById('add-availability-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Availability added successfully!');
            closeModal('add-availability-modal');
            this.reset();
        });
        
        document.getElementById('create-prescription-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Prescription created successfully!');
            closeModal('create-prescription-modal');
            this.reset();
        });
        
        document.getElementById('update-records-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Patient records updated successfully!');
            closeModal('update-records-modal');
            this.reset();
        });
        
        document.getElementById('generate-report-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Report generation started!');
            closeModal('generate-report-modal');
            this.reset();
        });
        
        document.getElementById('edit-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Profile updated successfully!');
            closeModal('edit-profile-modal');
        });
    </script>
</body>
</html>