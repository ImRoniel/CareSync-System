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
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-secondary:hover {
            background-color: rgba(46, 137, 73, 0.1);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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
            cursor: pointer;
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
            cursor: pointer;
            padding: 8px 0;
            position: relative;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-links a.active {
            color: var(--primary);
            font-weight: 600;
        }
        
        .nav-links a.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
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
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
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
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
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
        
        .appointments-table tr {
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        
        .appointments-table tr:hover {
            background-color: rgba(46, 137, 73, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: scale(1.05);
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
            background-color: rgba(46, 137, 73, 0.15);
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
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
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .queue-item:hover {
            border-color: var(--primary);
            transform: translateX(5px);
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
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .queue-status:hover {
            transform: scale(1.05);
        }
        
        .prescription-requests {
            list-style: none;
        }
        
        .prescription-request-item {
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .prescription-request-item:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
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
            transition: color 0.3s ease;
            cursor: pointer;
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
            cursor: pointer;
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-medium);
        }
        
        .modal-close:hover {
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-medium);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
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
                <a class="logo" id="logo">
                    <img src="../assets/images/3.png" alt="CareSync Logo" class="logo-image">
                    <span>CareSync</span>
                </a>
                
                <nav class="nav-links">
                    <a class="active">Dashboard</a>
                    <a>Appointments</a>
                    <a>Patients</a>
                    <a>Queue</a>
                    <a>Billing</a>
                </nav>
                
                <div class="nav-actions">
                    <button class="btn btn-secondary" id="profileBtn">Profile</button>
                    <button class="btn btn-primary" id="logoutBtn">Logout</button>
                </div>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <h1>Secretary</h1>
                    <p>Welcome back, Name here</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">LT</div>
                    <div>
                        <p>Name here</p>
                        <small>Clinic Secretary</small>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card" id="appointmentsStat">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>42</h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card" id="queueStat">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Patients in Queue</p>
                    </div>
                </div>
                
                <div class="stat-card" id="prescriptionsStat">
                    <div class="stat-icon">
                        <i class="fas fa-file-prescription"></i>
                    </div>
                    <div class="stat-info">
                        <h3>15</h3>
                        <p>Prescriptions to Process</p>
                    </div>
                </div>
                
                <div class="stat-card" id="callsStat">
                    <div class="stat-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="stat-info">
                        <h3>23</h3>
                        <p>Calls Today</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="left-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Patient Queue</h2>
                            <button class="btn btn-secondary" id="manageQueueBtn">Manage Queue</button>
                        </div>
                        
                        <ul class="patient-queue">
                            <li class="queue-item">
                                <div class="queue-info">
                                    <h4>Name here</h4>
                                    <p class="queue-time">Arrived: 8:45 AM</p>
                                </div>
                                <div class="queue-status status-waiting">Waiting</div>
                            </li>
                            <li class="queue-item">
                                <div class="queue-info">
                                    <h4>Name here</h4>
                                    <p class="queue-time">Arrived: 9:20 AM</p>
                                </div>
                                <div class="queue-status status-waiting">Waiting</div>
                            </li>
                            <li class="queue-item">
                                <div class="queue-info">
                                    <h4>Name here</h4>
                                    <p class="queue-time">Arrived: 9:35 AM</p>
                                </div>
                                <div class="queue-status status-confirmed">With Doctor</div>
                            </li>
                            <li class="queue-item">
                                <div class="queue-info">
                                    <h4>Name here</h4>
                                    <p class="queue-time">Arrived: 9:50 AM</p>
                                </div>
                                <div class="queue-status status-pending">Check-in</div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Appointment Requests</h2>
                            <button class="btn btn-secondary" id="viewAllAppointmentsBtn">View All</button>
                        </div>
                        
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Preferred Date</th>
                                    <th>Doctor</th>
                                    <th>Action</th>
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
                
                <div class="right-column">
                    <div class="card">
                        <div class="card-header">
                            <h2>Quick Actions</h2>
                        </div>
                        
                        <div class="quick-actions">
                            <div class="action-btn" id="scheduleAppointmentBtn">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <p>Schedule Appointment</p>
                            </div>
                            
                            <div class="action-btn" id="registerPatientBtn">
                                <div class="action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <p>Register Patient</p>
                            </div>
                            
                            <div class="action-btn" id="processPrescriptionBtn">
                                <div class="action-icon">
                                    <i class="fas fa-file-prescription"></i>
                                </div>
                                <p>Process Prescription</p>
                            </div>
                            
                            <div class="action-btn" id="callPatientBtn">
                                <div class="action-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <p>Call Patient</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Prescription Requests</h2>
                        </div>
                        
                        <ul class="prescription-requests">
                            <li class="prescription-request-item">
                                <div class="request-header">
                                    <span class="request-patient">Name here</span>
                                    <span class="request-date">Today, 9:15 AM</span>
                                </div>
                                <div class="request-details">
                                    <p><strong>Medication:</strong> Lisinopril 10mg</p>
                                    <p><strong>Reason:</strong> Refill Request</p>
                                </div>
                            </li>
                            <li class="prescription-request-item">
                                <div class="request-header">
                                    <span class="request-patient">Name here</span>
                                    <span class="request-date">Yesterday, 2:30 PM</span>
                                </div>
                                <div class="request-details">
                                    <p><strong>Medication:</strong> Metformin 500mg</p>
                                    <p><strong>Reason:</strong> New Prescription</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Activity</h2>
                        </div>
                        
                        <ul class="activity-list">
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New Patient Registered</h4>
                                    <p>Name here added to system</p>
                                    <div class="activity-time">30 minutes ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Appointment Confirmed</h4>
                                    <p>Name here - Oct 15, 2:00 PM</p>
                                    <div class="activity-time">1 hour ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-file-prescription"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Prescription Processed</h4>
                                    <p>For Name here</p>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Patient Called</h4>
                                    <p>Reminder for Name here</p>
                                    <div class="activity-time">3 hours ago</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Schedule Appointment -->
    <div class="modal" id="scheduleAppointmentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Schedule New Appointment</h2>
                <button class="modal-close" id="closeScheduleModal">&times;</button>
            </div>
            <div class="form-group">
                <label for="patientSelect">Select Patient</label>
                <select class="form-control" id="patientSelect">
                    <option value="">Choose a patient</option>
                    <option value="1">Name here</option>
                    <option value="2">Name here</option>
                    <option value="3">Name here</option>
                </select>
            </div>
            <div class="form-group">
                <label for="appointmentDate">Appointment Date</label>
                <input type="date" class="form-control" id="appointmentDate">
            </div>
            <div class="form-group">
                <label for="appointmentTime">Appointment Time</label>
                <input type="time" class="form-control" id="appointmentTime">
            </div>
            <div class="form-group">
                <label for="doctorSelect">Select Doctor</label>
                <select class="form-control" id="doctorSelect">
                    <option value="">Choose a doctor</option>
                    <option value="1">Dr. Name here</option>
                    <option value="2">Dr. Name here</option>
                    <option value="3">Dr. Name here</option>
                </select>
            </div>
            <div class="form-group">
                <label for="appointmentReason">Reason for Visit</label>
                <textarea class="form-control" id="appointmentReason" rows="3"></textarea>
            </div>
            <button class="btn btn-primary" style="width: 100%;">Schedule Appointment</button>
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
                        <li><a>Dashboard</a></li>
                        <li><a>Appointments</a></li>
                        <li><a>Patients</a></li>
                        <li><a>Queue</a></li>
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