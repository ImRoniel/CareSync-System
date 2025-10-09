<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Secretary Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
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
                    <a>Patients</a>
                    <a>Queue</a>
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
                    <h1>Secretary</h1>
                    <p>Welcome back, Name here</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">LT</div>
                    <div>
                        <p>Name here</p>
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
                        <h3>42</h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Patients in Queue</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-prescription"></i>
                    </div>
                    <div class="stat-info">
                        <h3>15</h3>
                        <p>Prescriptions to Process</p>
                    </div>
                </div>
                
                <div class="stat-card">
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
                            <div class="btn btn-secondary">Manage Queue</div>
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
                            <div class="btn btn-secondary">View All</div>
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
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <p>Schedule Appointment</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <p>Register Patient</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-file-prescription"></i>
                                </div>
                                <p>Process Prescription</p>
                            </div>
                            
                            <div class="action-btn">
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