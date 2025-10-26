<?php
// views/patient/book_appointment.php - Security check

// Prevent direct access to view files
// Check if data was passed from controller
if (!isset($doctors) || !isset($patient)) {
    header("Location: /CareSync-System/book_appointment.php");
    exit;
}

// Additional security: verify user is patient
if ($_SESSION['user_role'] !== 'patient') {
    header("Location: /CareSync-System/login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - CareSync</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .booking-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            min-height: 700px;
        }
        
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2E8949 0%, #1e6c3e 100%);
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .sidebar-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .sidebar-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .step-indicator {
            display: flex;
            flex-direction: column;
            gap: 25px;
            flex: 1;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: rgba(255,255,255,0.15);
        }
        
        .step-number {
            width: 28px;
            height: 28px;
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }
        
        .step.active .step-number {
            background: white;
            color: #2E8949;
            border-color: white;
        }
        
        .step-text {
            font-size: 14px;
            font-weight: 500;
        }
        
        .main-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        
        .content-header {
            margin-bottom: 40px;
        }
        
        .content-header h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .content-header p {
            color: #7f8c8d;
            font-size: 16px;
        }
        
        .booking-form {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-section {
            margin-bottom: 35px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fafbfc;
            color: #2c3e50;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2E8949;
            background: white;
            box-shadow: 0 0 0 3px rgba(46, 137, 73, 0.1);
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%237f8c8d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 50px;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            line-height: 1.5;
        }
        
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 140px;
            gap: 8px;
        }
        
        .btn-primary {
            background: #2E8949;
            color: white;
            border: 2px solid #2E8949;
        }
        
        .btn-primary:hover {
            background: #247a3d;
            border-color: #247a3d;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 137, 73, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #7f8c8d;
            border: 2px solid #e1e8ed;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #2E8949;
            color: #2E8949;
        }
        
        .alert {
            padding: 16px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border-left: 4px solid;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #f0f9f4;
            border-color: #2E8949;
            color: #1e6c3e;
        }
        
        .alert-error {
            background-color: #fef0f0;
            border-color: #e74c3c;
            color: #c0392b;
        }
        
        small {
            color: #7f8c8d;
            font-size: 13px;
            display: block;
            margin-top: 6px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: auto;
            padding-top: 30px;
            border-top: 1px solid #ecf0f1;
        }
        
        .doctor-info {
            background: #f8f9fa;
            border: 1px solid #e1e8ed;
            border-radius: 8px;
            padding: 15px;
            margin-top: 8px;
        }
        
        .doctor-info small {
            color: #2E8949;
            font-weight: 500;
        }
        
        @media (max-width: 968px) {
            .booking-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 30px 25px;
            }
            
            .step-indicator {
                flex-direction: row;
                justify-content: space-between;
                gap: 15px;
            }
            
            .step {
                flex: 1;
                justify-content: center;
                text-align: center;
                flex-direction: column;
                gap: 8px;
            }
            
            .step-text {
                font-size: 12px;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .main-content {
                padding: 25px;
            }
            
            .content-header h1 {
                font-size: 24px;
            }
            
            .form-actions {
                flex-direction: column-reverse;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <!-- Sidebar with steps -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>CareSync</h2>
                <p>Medical Appointment System</p>
            </div>
            <div class="step-indicator">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-text">Select Doctor</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">Choose Date & Time</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Confirm Details</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Appointment Booked</div>
                </div>
            </div>
        </div>
        
        <!-- Main content area -->
        <div class="main-content">
            <div class="content-header">
                <h1>Book an Appointment</h1>
                <p>Schedule your medical consultation with our specialists</p>
            </div>
            
            <!-- Display messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?? 'success' ?>">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <form method="POST" action="/CareSync-System/controllers/appointment/book_appointment_action.php" class="booking-form">
                <!-- Doctor Selection Section -->
                <div class="form-section">
                    <h3 class="section-title">Select Your Doctor</h3>
                    <div class="form-group">
                        <label for="doctor">Preferred Specialist *</label>
                        <select id="doctor" name="doctor_id" class="form-control" required>
                            <option value="">-- Choose a Doctor --</option>
                            <?php if (!empty($doctors)): ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= htmlspecialchars($doctor['doctor_id']) ?>">
                                        Dr. <?= htmlspecialchars($doctor['name']) ?> — <?= htmlspecialchars($doctor['specialization']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No doctors available at the moment</option>
                            <?php endif; ?>
                        </select>
                        <div class="doctor-info">
                            <small>✓ Board Certified Specialist</small>
                        </div>
                    </div>
                </div>

                <!-- Date and Time Section -->
                <div class="form-section">
                    <h3 class="section-title">Schedule Your Visit</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="appointment-date">Preferred Date *</label>
                            <input type="date" id="appointment-date" name="appointment_date" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment-time">Preferred Time *</label>
                            <input type="time" id="appointment-time" name="appointment_time" class="form-control" 
                                   min="08:00" max="17:00" required>
                            <small>Clinic hours: 8:00 AM - 5:00 PM</small>
                        </div>
                    </div>
                </div>

                <!-- Reason Section -->
                <div class="form-section">
                    <h3 class="section-title">Medical Information</h3>
                    <div class="form-group">
                        <label for="reason">Reason for Visit *</label>
                        <textarea id="reason" name="reason" class="form-control" rows="4" 
                                  placeholder="Please describe your symptoms, concerns, or reason for scheduling this appointment. Include any relevant medical history or current medications..." required></textarea>
                        <small>This information helps us prepare for your visit</small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <a href="/CareSync-System/views/patient/Patient_Dashboard1.php" class="btn btn-secondary">
                        ← Cancel
                    </a>
                    <button type="submit" class="btn btn-primary"  >
                        Book Appointment →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set minimum time for today's appointments
        document.getElementById('appointment-date').addEventListener('change', function() {
            const timeInput = document.getElementById('appointment-time');
            const selectedDate = this.value;
            const today = new Date().toISOString().split('T')[0];
            
            if (selectedDate === today) {
                const now = new Date();
                now.setMinutes(now.getMinutes() + 30); // Add 30 minutes buffer
                const currentHour = now.getHours().toString().padStart(2, '0');
                const currentMinute = now.getMinutes().toString().padStart(2, '0');
                timeInput.min = `${currentHour}:${currentMinute}`;
            } else {
                timeInput.min = '08:00';
            }
        });

        // Initialize time min value on page load
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('appointment-date');
            const timeInput = document.getElementById('appointment-time');
            const today = new Date().toISOString().split('T')[0];
            
            if (dateInput.value === today) {
                const now = new Date();
                now.setMinutes(now.getMinutes() + 30);
                const currentHour = now.getHours().toString().padStart(2, '0');
                const currentMinute = now.getMinutes().toString().padStart(2, '0');
                timeInput.min = `${currentHour}:${currentMinute}`;
            }
        });

        // Update step indicator based on form interaction
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.step');
            const formInputs = document.querySelectorAll('input, select, textarea');
            
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Simple logic to mark step 2 as active when date/time is filled
                    if ((input.name === 'appointment_date' || input.name === 'appointment_time') && input.value) {
                        steps[1].classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>