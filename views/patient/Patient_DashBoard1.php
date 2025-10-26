<?php
$sessionPath = __DIR__ . '/../../controllers/auth/session.php';

if (!file_exists($sessionPath)) {
    echo "session.php not found";
    exit;
}

require_once $sessionPath;
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/admin/patientController.php';
require_once __DIR__ . '/../../model/PatientModel.php';

if (empty($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit;
}
// Redirect if not logged in or wrong role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header("Location: ../../login/login.php");
    exit;
}


$patientId = intval($_SESSION['user_id']); 

$patientController = new PatientController($conn);
$patient = $patientController->getPatientData($patientId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareSync - Patient Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/PatientDashboard.css"> <!-- Link to your CSS file -->
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
                    <a onclick="showPage('appointments')">Appointments</a>
                    <a onclick="showPage('prescriptions')">Prescriptions</a>
                    <a onclick="showPage('health-records')">Health Records</a>
                    <a onclick="showPage('billing')">Billing</a>
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
                    <p>Welcome back, <?= htmlspecialchars($patient['name']) ?></p>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($patient['name'], 0, 2)) ?></div>
                    <div>
                        <p><?= htmlspecialchars($patient['name']) ?></p>
                        <small>Patient ID: <?= htmlspecialchars($patient['patient_id']) ?></small>
                    </div>
                </div>
            </div>

            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3><?= htmlspecialchars($totalAppointments) ?></h3>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-prescription"></i></div>
                    <div class="stat-info">
                        <h3></h3>
                        <p>Active Prescriptions</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-info">
                        <h3></h3>
                        <p>Pending Bills</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="stat-info">
                        <h3></h3>
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
              <button class="btn btn-primary" onclick="window.location.href='/CareSync-System/views/patient/book_appointment.php'">Book Appointment</button>

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

            <form id="book-appointment-form" method="POST" action="../../controllers/appointment/book_appointment_action.php">
                <input type="hidden" name="patient_id" value="<?= $_SESSION['patient_id'] ?? '' ?>">

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

                <div class="form-group">
                    <label for="appointment-reason">Reason for Visit</label>
                    <textarea id="appointment-reason" name="reason" class="form-control" rows="3" placeholder="Describe your symptoms or reason..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('book-appointment-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
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
                    <div class="user-avatar"><?= strtoupper(substr($patient['name'], 0, 2)) ?></div>
                    <div>
                        <h3><?= htmlspecialchars($patient['name']) ?></h3>
                        <p>Patient</p>
                        <p><?= htmlspecialchars($patient['email']) ?></p>
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
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="edit-name">Full Name</label>
                        <input type="text" id="edit-name" name="name" class="form-control"
                            value="<?= htmlspecialchars($patient['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" id="edit-email" name="email" class="form-control"
                            value="<?= htmlspecialchars($patient['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="tel" id="edit-phone" name="phone" class="form-control"
                            value="<?= htmlspecialchars($patient['phone']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="edit-address">Address</label>
                        <input type="text" id="edit-address" name="address" class="form-control"
                            value="<?= htmlspecialchars($patient['address']) ?>">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('edit-profile-modal')">Cancel</button>
                        <button type="submit" name="save_changes" class="btn btn-primary">Save Changes</button>
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

    <script src="../../assets/js/PatientDashboard.js"></script>
                            
</body>
</html>