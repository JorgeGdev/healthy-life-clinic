<?php
require 'config.php';
require 'checksession.php';

// Must be logged in
if (!isLoggedIn() || !isPatient()) {
    header('Location: login.php');
    exit();
}

// Check if ID is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID.");
}

$appointment_id = (int) $_GET['id'];
$patient_id = $_SESSION['user_id'];

// Verify the appointment belongs to the logged-in patient and is pending
$sql_check = "SELECT * FROM appointments WHERE appointment_id = ? AND patient_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You are not authorized to cancel this appointment, or it is not pending.");
}

// Update status to Cancelled
$sql_update = "UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ?";
$update_stmt = $conn->prepare($sql_update);
$update_stmt->bind_param("i", $appointment_id);

if ($update_stmt->execute()) {
    echo "<script>alert('Appointment cancelled successfully.'); window.location.href='list_appointments.php';</script>";
} else {
    echo "Error cancelling appointment: " . $conn->error;
}

$stmt->close();
$update_stmt->close();
$conn->close();
?>
