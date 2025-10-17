<?php
// Connect to database
require_once "../../config/db_connect.php";

// ✅ Check if doctor ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No doctor ID provided.");
}

$doctor_id = intval($_GET['id']);

// ✅ Fetch doctor information
$doctorSql = "
    SELECT u.name AS doctor_name, u.email, d.specialization
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    WHERE d.doctor_id = ?
";
$stmt = $conn->prepare($doctorSql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctorResult = $stmt->get_result();
$doctor = $doctorResult->fetch_assoc();
$stmt->close();

if (!$doctor) {
    die("Doctor not found.");
}

// ✅ Fetch appointments for this doctor
$apptSql = "
    SELECT 
        a.appointment_id,
        a.patient_name,
        a.appointment_date,
        a.status
    FROM appointments a
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC
";
$stmt = $conn->prepare($apptSql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$apptResult = $stmt->get_result();
$appointments = $apptResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule - <?= htmlspecialchars($doctor['doctor_name']) ?></title>
    <link rel="stylesheet" href="/CareSync-System/assets/css/bootstrap.min.css">
    <style>
        body { background-color: #f9f9f9; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { border-radius: 12px; }
        .status-badge {
            padding: 3px 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            color: #fff;
        }
        .status-pending { background-color: #ffc107; }
        .status-approved { background-color: #28a745; }
        .status-cancelled { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>Dr. <?= htmlspecialchars($doctor['doctor_name']) ?></h3>
            <p class="text-muted mb-1"><?= htmlspecialchars($doctor['specialization']) ?></p>
            <p class="text-secondary"><?= htmlspecialchars($doctor['email']) ?></p>
            <a href="/CareSync-System/dashboard/admin_dashboard.php" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Appointments</h4>

            <?php if (!empty($appointments)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($appt['status']);
                                    $badgeClass = match($status) {
                                        'pending' => 'status-pending',
                                        'approved' => 'status-approved',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center">No appointments found for this doctor.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
