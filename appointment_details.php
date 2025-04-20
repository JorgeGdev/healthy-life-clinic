<?php
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID.");
}
$appointment_id = (int) $_GET['id'];

$sql = "SELECT a.appointment_id, 
               p.first_name AS patient_first_name, p.last_name AS patient_last_name, 
               pr.first_name AS provider_first_name, pr.last_name AS provider_last_name, 
               pr.specialization, a.appointment_date, a.appointment_time, a.reason 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN providers pr ON a.provider_id = pr.provider_id
        WHERE a.appointment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Appointment Details - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css" title="style" />
</head>
<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Appointment Details</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li><a href="index.php">Home</a></li>
          <li class="selected"><a href="list_appointments.php">Appointments</a></li>
          <li><a href="make_appointment.php">Book</a></li>
          <li><a href="privacy.php">Privacy</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>

    <div id="site_content">
      <div class="sidebar">
        <h3>Quick Info</h3>
        <ul>
          <li><a href="list_appointments.php">Back to List</a></li>
        </ul>
        <p>Here you can see detailed information about the selected appointment.</p>
      </div>

      <div id="content">
        <h1>Appointment Information</h1>
        <p><strong>Appointment ID:</strong> <?php echo $appointment['appointment_id']; ?></p>
        <p><strong>Patient Name:</strong> <?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></p>
        <p><strong>Provider Name:</strong> <?php echo htmlspecialchars($appointment['provider_first_name'] . ' ' . $appointment['provider_last_name']); ?></p>
        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($appointment['specialization']); ?></p>
        <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
        <p><strong>Time:</strong> <?php echo date("h:i A", strtotime($appointment['appointment_time'])); ?></p>
        <p><strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason']); ?></p>

        <form action="list_appointments.php" method="get" style="padding-top: 15px;">
          <input class="submit" type="submit" value="Back to Appointment List">
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

<?php $stmt->close(); $conn->close(); ?>
