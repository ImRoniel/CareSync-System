<?php
require_once __DIR__ . '../../../controllers/auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/appointment/AppointmentController.php';

// Load controller and get available doctors
$controller = new AppointmentController($conn);
$doctors = $controller->getAvailableDoctors();

// Redirect if not logged in
if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="booking-page">
        <h1>Book an Appointment</h1>
        <form method="POST" action="../../controllers/appointment/book_appointment_action.php">

            <input type="hidden" name="patient_id" value="<?= $_SESSION['patient_id'] ?>">

            <!-- Doctor Selection -->
            <div class="form-group">
                <label for="doctor">Select Doctor</label>
                <select id="doctor" name="doctor_id" class="form-control" required>
                    <option value="">-- Select Doctor --</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= htmlspecialchars($doctor['doctor_id']) ?>">
                            <?= htmlspecialchars($doctor['name']) ?> — <?= htmlspecialchars($doctor['specialization']) ?>
                        </option>
                    <?php endforeach; ?>
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
                <label for="reason">Reason for Visit</label>
                <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Describe your symptoms or reason..."></textarea>
            </div>

            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </div>
        </form>
    </div>
</body>
</html>
