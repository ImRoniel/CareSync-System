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