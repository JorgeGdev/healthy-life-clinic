<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user is not logged in or not a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header('Location: login.php');
    exit();
}

// Include database connection
include 'config.php';

// Fetch the logged-in patient's details
$patient_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name FROM patients WHERE patient_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Healthy Life Clinic - Book medical appointments with expert healthcare providers.">
  <meta name="keywords" content="clinic, healthcare, medical appointments, doctors, patients">
  <meta name="author" content="Healthy Life Clinic">
  <title>Healthy Life Clinic - Your Healthcare Partner</title>
  <link rel="stylesheet" type="text/css" href="style/style.css" title="style" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Welcome, <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>!</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li class="selected"><a href="index.php">Home</a></li>
          <li><a href="list_appointments.php">Appointments</a></li>
          <li><a href="make_appointment.php">Book Appointment</a></li>
          <li><a href="privacy.php">Privacy Policy</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>

    <div id="site_content">
      <div class="sidebar">
        <h3>Latest News</h3>
        <h4>Website Redesigned</h4>
        <h5><?php echo date("F j, Y"); ?></h5>
        <p>We're proud to present the new look for our Healthy Life Clinic app.<br /><a href="#">Read more</a></p>

        <h3>Navigation</h3>
        <ul>
          <li><a href="list_appointments.php">Your Appointments</a></li>
          <li><a href="make_appointment.php">Book a Visit</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>

      <div id="content">
        <h1>Welcome to Healthy Life Clinic</h1>
        <p>This web application allows you to manage your healthcare appointments with our expert medical providers. Easily book, view, and manage your visits online.</p>
        <h2>What You Can Do</h2>
        <ul>
          <li>View your upcoming appointments</li>
          <li>Book new appointments</li>
          <li>Cancel or reschedule visits</li>
        </ul>

        <div class="separator"></div>

        <h3>Need Help?</h3>
        <p>If you experience any issues while using the system, please contact our support team.</p>
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

<?php mysqli_close($conn); ?>
