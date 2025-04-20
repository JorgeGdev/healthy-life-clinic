<?php
// Database connection
require 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['provider_id']) || !is_numeric($_POST['provider_id'])) {
        die(json_encode(["error" => "Invalid provider ID."]));
    }

    if (!isset($_POST['appointment_date']) || strtotime($_POST['appointment_date']) === false) {
        die(json_encode(["error" => "Invalid appointment date."]));
    }

    $provider_id = (int) $_POST['provider_id'];
    $appointment_date = $_POST['appointment_date'];

    // Fetch existing appointments for the selected provider on the given date
    $sql = "SELECT appointment_time FROM appointments WHERE provider_id = ? AND appointment_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $provider_id, $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Collect booked times
    $booked_times = [];
    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['appointment_time'];
    }

    // Define available time slots (08:00 - 18:00, every 30 minutes)
    $available_slots = [];
    for ($hour = 8; $hour < 18; $hour++) {
        foreach (["00", "30"] as $minute) {
            $time_slot = sprintf("%02d:%s:00", $hour, $minute);
            if (!in_array($time_slot, $booked_times)) {
                $available_slots[] = $time_slot;
            }
        }
    }

    // Return available slots as JSON
    echo json_encode($available_slots);
}

$conn->close();
