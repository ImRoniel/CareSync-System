<?php
// This view should be included in doctor_dashboard.php - typically in appointment management modal
require_once __DIR__ . '/../../controllers/auth/session.php';
require_once __DIR__ . '/../../config/db_connect.php';
require_once __DIR__ . '/../../controllers/prescription/PrescriptionController.php';

$prescriptionController = new PrescriptionController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_prescription'])) {
    $result = $prescriptionController->addPrescription(
        $_POST['appointment_id'],
        $_SESSION['doctor_id'],
        $_POST['patient_id'],
        [
            'medicine_name' => $_POST['medicine_name'],
            'dosage' => $_POST['dosage'],
            'frequency' => $_POST['frequency'],
            'duration' => $_POST['duration'],
            'instructions' => $_POST['instructions']
        ]
    );
    
    if ($result['success']) {
        echo '<div class="alert alert-success">' . $result['message'] . '</div>';
    } else {
        echo '<div class="alert alert-danger">' . $result['message'] . '</div>';
    }
}

$appointment_id = $_GET['appointment_id'] ?? $_POST['appointment_id'] ?? null;
$prescriptions = $appointment_id ? $prescriptionController->getAppointmentPrescriptions($appointment_id) : null;
?>

<?php if ($appointment_id): ?>
<div class="prescription-section">
    <h4>Prescribe Medication</h4>
    
    <!-- Add Prescription Form -->
    <form method="POST" action="">
        <input type="hidden" name="add_prescription" value="1">
        <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
        <input type="hidden" name="patient_id" value="<?= $_GET['patient_id'] ?? '' ?>">
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="medicine_name">Medicine Name *</label>
                <input type="text" id="medicine_name" name="medicine_name" class="form-control" required>
            </div>
            <div class="form-group col-md-3">
                <label for="dosage">Dosage *</label>
                <input type="text" id="dosage" name="dosage" class="form-control" placeholder="e.g., 500mg" required>
            </div>
            <div class="form-group col-md-3">
                <label for="frequency">Frequency *</label>
                <input type="text" id="frequency" name="frequency" class="form-control" placeholder="e.g., 3 times daily" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="duration">Duration *</label>
                <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g., 7 days" required>
            </div>
            <div class="form-group col-md-6">
                <label for="instructions">Special Instructions</label>
                <input type="text" id="instructions" name="instructions" class="form-control" placeholder="e.g., After meals">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Prescription</button>
    </form>

    <!-- Existing Prescriptions -->
    <?php if ($prescriptions && $prescriptions->num_rows > 0): ?>
    <div class="mt-4">
        <h5>Current Prescriptions</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prescription = $prescriptions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($prescription['medicine_name']) ?></td>
                        <td><?= htmlspecialchars($prescription['dosage']) ?></td>
                        <td><?= htmlspecialchars($prescription['frequency']) ?></td>
                        <td><?= htmlspecialchars($prescription['duration']) ?></td>
                        <td><?= htmlspecialchars($prescription['instructions'] ?? 'None') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>