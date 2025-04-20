<?php
include 'config.php';

$id = $_GET['id'];

$query = "SELECT appointments.*, patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, 
          providers.first_name AS provider_first_name, providers.last_name AS provider_last_name, providers.specialization 
          FROM appointments 
          JOIN patients ON appointments.patient_id = patients.patient_id 
          JOIN providers ON appointments.provider_id = providers.provider_id 
          WHERE appointments.appointment_id = $id";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<h2>Appointment Details</h2>";
    echo "<p>Appointment ID: {$row['appointment_id']}</p>";
    echo "<p>Patient Name: {$row['patient_first_name']} {$row['patient_last_name']}</p>";
    echo "<p>Provider Name: {$row['provider_first_name']} {$row['provider_last_name']}</p>";
    echo "<p>Specialization: {$row['specialization']}</p>";
    echo "<p>Date: {$row['appointment_date']}</p>";
    echo "<p>Time: {$row['appointment_time']}</p>";
    echo "<p>Reason: {$row['reason']}</p>";
    echo "<p><a href='list_appointments.php'>Back to List</a></p>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
