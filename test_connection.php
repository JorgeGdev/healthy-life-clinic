<?php
include 'config.php'; // Import the database connection

// Test the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}

// Fetch and display sample data from the 'appointments' table
$sql = "SELECT * FROM appointments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Appointments Data:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "Appointment ID: " . $row["appointment_id"] . " - Date: " . $row["appointment_date"] . " - Time: " . $row["appointment_time"] . "<br>";
    }
} else {
    echo "No appointments found.";
}

$conn->close();
?>
