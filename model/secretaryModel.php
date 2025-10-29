<?php
class SecretaryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllSecretary() {
        $sql = "SELECT 
                secretaries.*, 
                users.*, 
                du.name AS doctor_name
            FROM users
            CROSS JOIN secretaries
                ON users.id = secretaries.user_id
            LEFT JOIN doctors AS d
                ON secretaries.assigned_doctor_id = d.doctor_id
            LEFT JOIN users AS du
                ON d.user_id = du.id";
            $stmt = $this->conn->query($sql);
        $result = $stmt;
        return $result;
    }

    public function getSecretaryByUserId($user_id) {
        $sql = "
             SELECT 
                u.id, u.name, u.email, u.role,
                s.secretary_id, s.phone, s.address, s.department, s.employment_date, s.assigned_doctor_id,
                du.name AS doctor_name
            FROM users u
            JOIN secretaries s ON u.id = s.user_id
            LEFT JOIN doctors d ON s.assigned_doctor_id = d.doctor_id
            LEFT JOIN users du ON d.user_id = du.id
            WHERE u.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    //method for updating a secretary profile method
    public function updateSecretaryProfile($user_id, $name, $email, $phone, $address, $department) {
    $sql = "
        UPDATE users 
        SET name = ?, email = ?
        WHERE id = ?;
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // update secretary table
    $sql2 = "
        UPDATE secretaries
        SET phone = ?, address = ?, department = ?
        WHERE user_id = ?;
    ";

    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->bind_param("sssi", $phone, $address, $department, $user_id);
    return $stmt2->execute(); // returns true or false
    }

     public function getAppointmentsForAssignedDoctorByUserId(int $secretaryUserId) {
        // Explicit column list (no a.*) using caresync_db schema
        $sql = "
            SELECT 
                a.appointment_id,
                a.patient_id,
                a.doctor_id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.reason,
                a.diagnosis,
                a.notes,
                a.queue_number,
                a.created_at AS appointment_created_at,
                a.updated_at AS appointment_updated_at,
                p.patient_id AS patient_table_id,
                u.name AS patient_name,
                u.email AS patient_email,
                du.name AS doctor_name
            FROM secretaries s
            JOIN doctors d ON s.assigned_doctor_id = d.doctor_id
            JOIN appointments a ON a.doctor_id = d.doctor_id
            LEFT JOIN patients p ON a.patient_id = p.patient_id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN users du ON d.user_id = du.id
            WHERE s.user_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("SecretaryModel::getAppointmentsForAssignedDoctorByUserId prepare failed: " . $this->conn->error);
            return []; // always return array
        }

        $stmt->bind_param("i", $secretaryUserId);
        if (!$stmt->execute()) {
            error_log("SecretaryModel::getAppointmentsForAssignedDoctorByUserId execute failed: " . $stmt->error);
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        // Normalize keys so view can safely use $AppointmentRequest['name'], etc.
        foreach ($rows as &$r) {
            $r['name'] = $r['patient_name'] ?? '';
            $r['patient_name'] = $r['patient_name'] ?? '';
            $r['patient_email'] = $r['patient_email'] ?? '';
            $r['doctor_name'] = $r['doctor_name'] ?? '';
            $r['appointment_id'] = isset($r['appointment_id']) ? (int)$r['appointment_id'] : null;
            $r['appointment_date'] = $r['appointment_date'] ?? '';
            $r['appointment_time'] = $r['appointment_time'] ?? '';
            $r['status'] = $r['status'] ?? '';
            $r['reason'] = $r['reason'] ?? '';
            $r['queue_number'] = $r['queue_number'] ?? null;
        }

        return $rows; // always an array (possibly empty)
    }

    public function checkInPatienToDoctor($Appointment){
        
    }

    

}
?>
