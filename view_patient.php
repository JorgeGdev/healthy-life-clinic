<?php
require 'config.php';
require 'checksession.php';

if (!isAdmin()) {
  header('Location: login.php');
  exit();
}

// Validar ID del paciente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid patient ID.");
}

$patient_id = intval($_GET['id']);

// Obtener los datos del paciente
$sql = "SELECT first_name, last_name, email, phone FROM patients WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Patient not found.");
}

$patient = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>View Patient - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" type="text/css" href="style/responsive.css" media="screen and (max-width: 768px)">
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Your Healthcare Partner</h2>
        </div>
      </div>
      <div id="menubar">
        <div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>
        <ul id="menu">
          <li class="selected"><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="manage_patients.php">Manage Patients</a></li>
          <li><a href="manage_providers.php">Manage Providers</a></li>
          <li><a href="list_appointments.php">Appointments</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>

    <div id="site_content">
      <div class="sidebar">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="manage_patients.php">Manage Patients</a></li>
          <li><a href="list_appointments.php">Appointments</a></li>
          <li><a href="make_appointment.php">New Appointment</a></li>
        </ul>
      </div>

      <div id="content">
        <h1>Patient Details</h1>

        <p><strong>First Name:</strong> <?php echo htmlspecialchars($patient['first_name']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($patient['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>

        <p><em>Password is hidden for security reasons.</em></p>

        <form action="manage_patients.php" method="get">
          <p class="form-submit">
            <button type="submit" class="submit">Back to Patients</button>
          </p>

        </form>
      </div>
    </div>

    <div id="footer">
      &copy; 2025 Healthy Life Clinic |
      <a href="privacy.php">Privacy</a>
    </div>
  </div>
  <script src="style/script.js"></script>
</body>

</html>

<?php $conn->close(); ?>