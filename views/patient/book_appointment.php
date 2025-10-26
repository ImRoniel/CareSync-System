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
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2E8949 0%, #2E8949 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .booking-page {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            padding: 40px;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em;
            font-weight: 300;
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
            color: #555;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2E8949;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        select.form-control {
            appearance: none;
            /* background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); */
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 140px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2E8949 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e1e5e9;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .booking-page {
                padding: 25px;
                margin: 10px;
            }
            
            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="booking-page">
        <h1>Book an Appointment</h1>
        
        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'success' ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <form method="POST" action="/CareSync-System/controllers/ppointment/book_appointment_action.php">
            <!-- Doctor Selection -->
            <div class="form-group">
                <label for="doctor">Select Doctor *</label>
                <select id="doctor" name="doctor_id" class="form-control" required>
                    <option value="">-- Select Doctor --</option>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= htmlspecialchars($doctor['doctor_id']) ?>">
                                Dr. <?= htmlspecialchars($doctor['name']) ?> — <?= htmlspecialchars($doctor['specialization']) ?> 
                                <?= isset($doctor['clinic_room']) ? '(Room: ' . htmlspecialchars($doctor['clinic_room']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No doctors available</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Date and Time -->
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

            <!-- Reason -->
            <div class="form-group">
                <label for="reason">Reason for Visit *</label>
                <textarea id="reason" name="reason" class="form-control" rows="4" 
                          placeholder="Please describe your symptoms or reason for the appointment..." required></textarea>
            </div>

            <div class="form-actions">
                <a href="/CareSync-System/views/patient/dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </div>
        </form>
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
    </script>
</body>
</html>