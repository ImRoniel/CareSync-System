<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/doctor_dashboard.css">
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
                    <a>Schedule</a>
                    <a>Patients</a>
                    <a>Prescriptions</a>
                    <a>Reports</a>
                </nav>
                
                <div class="nav-actions">
                    <div class="btn btn-secondary">Profile</div>
                    <button class="btn btn-primary" onclick="window.location.href='index.php'">Logout</button>
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
                    <h1>Doctor</h1>
                    <p>Welcome back, Dr. Name here</p>
                </div>
                <div class="user-info">
                    <div class="user-avatar">MC</div>
                    <div>
                        <p>Dr. Name here</p>
                        <small>type of doctor here e.g. cardiologist</small>
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
                            <h2>Today's Appointments</h2>
                            <div class="btn btn-secondary">View Schedule</div>
                        </div>
                        
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name here</td>
                                    <td>9:00 AM</td>
                                    <td>Consultation</td>
                                    <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>10:15 AM</td>
                                    <td>Follow-up</td>
                                    <td><span class="status-badge status-in-progress">In Progress</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>11:30 AM</td>
                                    <td>Check-up</td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>2:00 PM</td>
                                    <td>Consultation</td>
                                    <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                </tr>
                                <tr>
                                    <td>Name here</td>
                                    <td>3:45 PM</td>
                                    <td>Follow-up</td>
                                    <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Patient Statistics</h2>
                            <div class="btn btn-secondary">View Report</div>
                        </div>
                        
                        <div class="chart-container">
                            <p>Patient Visit Trends Chart</p>
                        </div>
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
                                <p>Add Availability</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <p>Create Prescription</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <p>Update Records</p>
                            </div>
                            
                            <div class="action-btn">
                                <div class="action-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <p>View Reports</p>
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
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New Prescription</h4>
                                    <p>Prescription created for Name here</p>
                                    <div class="activity-time">10 minutes ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Appointment Scheduled</h4>
                                    <p>New appointment with Name here</p>
                                    <div class="activity-time">1 hour ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>Payment Received</h4>
                                    <p>Payment from Name here</p>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </li>
                            
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h4>New Patient</h4>
                                    <p>Name here registered as new patient</p>
                                    <div class="activity-time">Yesterday</div>
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
                        <li><a>Schedule</a></li>
                        <li><a>Patients</a></li>
                        <li><a>Prescriptions</a></li>
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