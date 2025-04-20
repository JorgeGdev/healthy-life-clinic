<?php
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID.");
}
$appointment_id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete_sql = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment deleted successfully!'); window.location.href = 'list_appointments.php';</script>";
    } else {
        echo "Error deleting appointment: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Appointment - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css" title="style" />
</head>
<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Delete Confirmation</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li><a href="home.php">Home</a></li>
          <li class="selected"><a href="list_appointments.php">Appointments</a></li>
          <li><a href="make_appointment.php">Book</a></li>
          <li><a href="privacy.php">Privacy</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>

    <div id="site_content">
      <div class="sidebar">
        <h3>Notice</h3>
        <p>This action is permanent. Once deleted, the appointment cannot be recovered.</p>
        <ul>
          <li><a href="list_appointments.php">Return to Appointment List</a></li>
        </ul>
      </div>

      <div id="content">
        <h1>Delete Appointment</h1>
        <p>Are you sure you want to delete this appointment?</p>

        <form action="" method="POST" style="padding-top: 15px;">
          <input class="submit" type="submit" value="Yes, Delete">
          <a href="list_appointments.php" class="submit" style="margin-left: 10px; background: #777;">Cancel</a>
        </form>
      </div>
    </div>

    <div id="footer">
      Copyright &copy; Healthy Life Clinic |
      <a href="http://validator.w3.org/check?uri=referer">HTML5</a> |
      <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> |
      <a href="http://www.html5webtemplates.co.uk">Free CSS Templates</a>
    </div>
  </div>
</body>
</html>
