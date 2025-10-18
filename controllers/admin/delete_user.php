<?php
include "../../config/db_connect.php";


//THIS CODE SAY THAT IF THE DOCTOR DO NOT HAVE A ID, IT IS GOING TO DIE AND RETURN INVALID 
if (!isset($_GET['id'])) {
    die("Invalid request. No user ID provided.");
}

$id = intval($_GET['id']); //INTVAL() KEYWORD 

// Prepare delete statement
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect to admin dashboard after delete
    header("Location: /Caresync-System/dashboard/admin_dashboard.php?msg=deleted");
    exit;
} else {
    die("Error deleting user: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
