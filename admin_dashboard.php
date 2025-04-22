<?php
require 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security check: only admin users allowed
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['user_id'];

// Get admin full name
$name_query = "SELECT first_name, last_name FROM admins WHERE admin_id = ?";
$name_stmt = $conn->prepare($name_query);
$name_stmt->bind_param("i", $admin_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
$admin = $name_result->fetch_assoc();
$admin_name = $admin ? $admin['first_name'] . ' ' . $admin['last_name'] : 'Admin';

$name_stmt->close();

// Get patients, providers, appointments
$query_patients = "SELECT * FROM patients";
$result_patients = mysqli_query($conn, $query_patients);

$query_providers = "SELECT * FROM providers";
$result_providers = mysqli_query($conn, $query_providers);

$query_appointments = "SELECT appointments.*, 
    patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, 
    providers.first_name AS provider_first_name, providers.last_name AS provider_last_name, 
    providers.specialization 
FROM appointments 
JOIN patients ON appointments.patient_id = patients.patient_id 
JOIN providers ON appointments.provider_id = providers.provider_id";

$result_appointments = mysqli_query($conn, $query_appointments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css" title="style" />
  <link rel="stylesheet" type="text/css" href="style/responsive.css" media="screen and (max-width: 768px)">
</head>
<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Admin Panel</h2>
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
        <h3>Welcome</h3>
        <p>Hello, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!</p>
        <p>You are logged in as <strong>Admin</strong>.</p>
      </div>

      <div id="content">
        <h1>Admin Dashboard</h1>

        <h2>Patients</h2>
        <table>
          <tr>
            <th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th>
          </tr>
          <?php while ($row = mysqli_fetch_assoc($result_patients)) { ?>
            <tr>
              <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
              <td><?php echo htmlspecialchars($row['first_name']); ?></td>
              <td><?php echo htmlspecialchars($row['last_name']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td><?php echo htmlspecialchars($row['phone']); ?></td>
            </tr>
          <?php } ?>
        </table>

        <h2>Providers</h2>
        <table>
          <tr>
            <th>ID</th><th>First Name</th><th>Last Name</th><th>Specialization</th>
          </tr>
          <?php while ($row = mysqli_fetch_assoc($result_providers)) { ?>
            <tr>
              <td><?php echo htmlspecialchars($row['provider_id']); ?></td>
              <td><?php echo htmlspecialchars($row['first_name']); ?></td>
              <td><?php echo htmlspecialchars($row['last_name']); ?></td>
              <td><?php echo htmlspecialchars($row['specialization']); ?></td>
            </tr>
          <?php } ?>
        </table>

        <h2>Appointments</h2>
        <table>
          <tr>
            <th>ID</th><th>Patient</th><th>Provider</th><th>Specialization</th>
            <th>Date</th><th>Time</th><th>Reason</th><th>Status</th>
          </tr>
          <?php while ($row = mysqli_fetch_assoc($result_appointments)) { ?>
            <tr>
              <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
              <td><?php echo htmlspecialchars($row['patient_first_name'] . ' ' . $row['patient_last_name']); ?></td>
              <td><?php echo htmlspecialchars($row['provider_first_name'] . ' ' . $row['provider_last_name']); ?></td>
              <td><?php echo htmlspecialchars($row['specialization']); ?></td>
              <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
              <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
              <td><?php echo htmlspecialchars($row['reason']); ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>

    <div id="footer">
      Copyright &copy; Healthy Life Clinic |
      <a href="http://validator.w3.org/check?uri=referer">HTML5</a> |
      <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> |
      <a href="http://www.html5webtemplates.co.uk">Free CSS Templates</a>
    </div>
  </div>
  <script src="style/script.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
