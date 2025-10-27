<?php
require_once '../../controllers/appointment/AppointmentController.php';

// Get database connection and create controller
global $conn;
$controller = new AppointmentController($conn);

// Handle form submission FIRST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reschedule_appointment'])) {
    $appointmentId = $_POST['appointment_id'];
    $newDate = $_POST['new_date'];
    $newTime = $_POST['new_time'];
    
    $result = $controller->rescheduleAppointment($appointmentId, $newDate, $newTime);
    
    if ($result['success']) {
        header('Location: Admin_Dashboard1.php?success=' . urlencode($result['message']));
        exit();
    } else {
        $error = $result['message'];
    }
}

// Then get appointment data for display
$appointment = null;
$appointmentId = $_GET['appointment_id'] ?? null;

if ($appointmentId) {
    $appointment = $controller->showRescheduleForm($appointmentId);
}

if (!$appointment) {
    header('Location: Admin_Dashboard1.php?error=Appointment not found');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Appointment - CareSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .calendar-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .time-slots {
            max-height: 300px;
            overflow-y: auto;
        }
        .time-slot {
            padding: 10px;
            margin: 5px 0;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .time-slot:hover {
            border-color: #2E603D;
            background-color: #f8f9fa;
        }
        .time-slot.selected {
            border-color: #2E8949;
            background-color: #2E8949;
            color: white;
        }
        .calendar-header {
            background: linear-gradient(135deg, #AD5057 0%,  #2E8949 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../../views/admin/partials/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Reschedule Appointment</h1>
                    <a href="Admin_Dashboard1.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Appointment Details -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Appointment Details</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Appointment ID:</strong> #<?php echo htmlspecialchars($appointment['appointment_id']); ?></p>
                                <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
                                <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                                <p><strong>Current Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                                <p><strong>Current Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-warning"><?php echo htmlspecialchars($appointment['status']); ?></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Reschedule Form -->
                    <div class="col-md-8">
                        <div class="calendar-container">
                            <form id="rescheduleForm" method="POST" action="Reschedule_Appointment.php">
                                <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['appointment_id']); ?>">
                                
                                <div class="calendar-header text-center mb-4">
                                    <h4>Select New Date & Time</h4>
                                </div>

                                <!-- Date Selection -->
                                <div class="mb-4">
                                    <label for="new_date" class="form-label fw-bold">Select Date:</label>
                                    <input type="date" 
                                           class="form-control form-control-lg" 
                                           id="new_date" 
                                           name="new_date" 
                                           min="<?php echo date('Y-m-d'); ?>" 
                                           required>
                                </div>

                                <!-- Time Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Select Time:</label>
                                    <div class="time-slots">
                                        <div class="row">
                                            <?php
                                            // Generate time slots (9 AM to 5 PM)
                                            $startHour = 9;
                                            $endHour = 17;
                                            for ($hour = $startHour; $hour <= $endHour; $hour++):
                                                for ($minute = 0; $minute < 60; $minute += 30):
                                                    $time = sprintf('%02d:%02d:00', $hour, $minute);
                                                    ?>
                                                    <div class="col-md-3 mb-2">
                                                        <div class="time-slot text-center" data-time="<?php echo $time; ?>">
                                                            <?php echo date('g:i A', strtotime($time)); ?>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <input type="hidden" id="selected_time" name="new_time" required>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="Admin_Dashboard1.php" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" name="reschedule_appointment" class="btn btn-primary">
                                        <i class="bi bi-calendar-check"></i> Reschedule Appointment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Time slot selection
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.addEventListener('click', function() {
                // Remove selected class from all slots
                document.querySelectorAll('.time-slot').forEach(s => {
                    s.classList.remove('selected');
                });
                
                // Add selected class to clicked slot
                this.classList.add('selected');
                
                // Set hidden input value
                document.getElementById('selected_time').value = this.dataset.time;
            });
        });

        // Form validation
        document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
            const selectedDate = document.getElementById('new_date').value;
            const selectedTime = document.getElementById('selected_time').value;
            
            if (!selectedDate || !selectedTime) {
                e.preventDefault();
                alert('Please select both date and time');
                return false;
            }
            
            // Confirm reschedule
            if (!confirm('Are you sure you want to reschedule this appointment?')) {
                e.preventDefault();
                return false;
            }
        });

        // Set minimum date to today
        document.getElementById('new_date').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>