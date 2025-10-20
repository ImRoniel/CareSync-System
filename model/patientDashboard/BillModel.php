<?php
class BillModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Get pending bills for a patient
    public function getPendingBills($patient_id) {
        $stmt = $this->conn->prepare("
            SELECT bill_id, invoice_number, service, bill_date, amount, due_date, status
            FROM bills
            WHERE patient_id = ? AND status = 'pending'
            ORDER BY due_date ASC
        ");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $bills = [];
        while ($row = $result->fetch_assoc()) {
            $bills[] = $row;
        }

        return $bills;
    }
}
