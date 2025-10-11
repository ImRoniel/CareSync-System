<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'secretary') {
    header("Location: ../login/login.php");
    exit();
}
?>

<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Patient</title>
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
            cursor: default;
            gap: 10px;
            font-size: 1rem;
            pointer-events: none;
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
            cursor: default;
            pointer-events: none;
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
            transition: none;
            cursor: default;
            pointer-events: none;
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
            cursor: default;
            pointer-events: none;
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
            cursor: default;
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
            transition: none;
            cursor: default;
            pointer-events: none;
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
            transition: none;
            cursor: default;
            pointer-events: none;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 0.875rem;
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
                <div class="logo">
                    <img src="../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </div>
                
                <nav class="nav-links">
                    <a>Dashboard</a>
                    <a>Appointments</a>
                    <a>Prescriptions</a>
                    <a>Health Records</a>
                    <a>Billing</a>
                </nav>
                
                <div class="nav-actions">
                    <div class="btn btn-secondary">Profile</div>
                    <div class="btn btn-primary">Logout</div>
                </div>
                
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1>Patient</h1>
                    <p>Welcome back, Name here</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">SJ</div>
                    <div>
                        <p>Name here</p>
                        <small>Patient ID: ID idk?</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>2</h3>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="stat-info">
                        <h3>5</h3>
                        <p>Active Prescriptions</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-info">
                        <h3>1</h3>
                        <p>Pending Bills</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Health Records</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Upcoming Appointments</h2>
                            <div class="btn btn-secondary">View All</div>
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
                                    <td>Oct 1, 2025 - 10:00 AM</td>
                                    <td>Follow-up</td>
                                    <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td>Dr. Name here</td>
                                    <td>Oct 2, 2025 - 2:30 PM</td>
                                    <td>Consultation</td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Current Prescriptions</h2>
                            <div class="btn btn-secondary">View All</div>
                        </div>
                        
                        <ul class="prescription-list">
                            <li class="prescription-item">
                                <div class="prescription-header">
                                    <span class="prescription-doctor">Dr. Name here</span>
                                    <span class="prescription-date">Sept 28, 2023</span>
                                </div>
                                <div class="prescription-details">
                                    <p><strong>Medication:</strong> Lisinopril 10mg</p>
                                    <p><strong>Dosage:</strong> Once daily</p>
                                    <p><strong>Refills:</strong> 2 remaining</p>
                                </div>
                            </li>
                            <li class="prescription-item">
                                <div class="prescription-header">
                                    <span class="prescription-doctor">Dr. Name here</span>
                                    <span class="prescription-date">Sept 15, 2023</span>
                                </div>
                                <div class="prescription-details">
                                    <p><strong>Medication:</strong> Metformin 500mg</p>
                                    <p><strong>Dosage:</strong> Twice daily</p>
                                    <p><strong>Refills:</strong> 1 remaining</p>
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
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <p>Book Appointment</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <p>Request Refill</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <p>View Records</p>
                            </div>
                            
                            <div class="action-btn">
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
                                    <div class="activity-time">2 days ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Prescription Refilled</h4>
                                    <p>Lisinopril 10mg</p>
                                    <div class="activity-time">1 week ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Bill Paid</h4>
                                    <p>Consultation fee - $150</p>
                                    <div class="activity-time">2 weeks ago</div>
                                </div>
                            </li>
                        </ul>
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
                        <li><a>Dashboard</a></li>
                        <li><a>Appointments</a></li>
                        <li><a>Prescriptions</a></li>
                        <li><a>Health Records</a></li>
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
</body>
</html>