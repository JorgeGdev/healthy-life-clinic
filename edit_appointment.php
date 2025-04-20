<?php
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID.");
}
$appointment_id = (int) $_GET['id'];

$sql = "SELECT appointment_id, appointment_date, appointment_time, reason, status FROM appointments WHERE appointment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Appointment not found.");
}

$appointment = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['appointment_date']) || empty($_POST['appointment_date']) || strtotime($_POST['appointment_date']) === false) {
        die("Invalid appointment date.");
    }

    if (!isset($_POST['appointment_time']) || empty($_POST['appointment_time'])) {
        die("Invalid appointment time.");
    }

    if (!isset($_POST['reason']) || strlen(trim($_POST['reason'])) < 10) {
        die("Reason must be at least 10 characters.");
    }

    $valid_statuses = ['Pending', 'Completed', 'Cancelled'];
    if (!isset($_POST['status']) || !in_array($_POST['status'], $valid_statuses)) {
        die("Invalid status value.");
    }

    $new_date = $_POST['appointment_date'];
    $new_time = $_POST['appointment_time'];
    $new_reason = htmlspecialchars(trim($_POST['reason']));
    $new_status = $_POST['status'];

    $update_sql = "UPDATE appointments SET appointment_date = ?, appointment_time = ?, reason = ?, status = ? WHERE appointment_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $new_date, $new_time, $new_reason, $new_status, $appointment_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Appointment updated successfully!'); window.location.href = 'list_appointments.php';</script>";
    } else {
        echo "Error updating appointment: " . $conn->error;
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Appointment - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css" title="style" />
</head>
<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Edit an Existing Appointment</h2>
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
        <h3>Options</h3>
        <ul>
          <li><a href="list_appointments.php">Back to List</a></li>
        </ul>
        <p>Modify appointment details and click "Update" to save changes.</p>
      </div>

      <div id="content">
        <h1>Edit Appointment</h1>
        <form action="" method="POST" class="form_settings">
          <p><span>Date:</span>
            <input type="date" name="appointment_date" required value="<?php echo $appointment['appointment_date']; ?>">
          </p>

          <p><span>Time:</span>
            <input type="time" name="appointment_time" required value="<?php echo $appointment['appointment_time']; ?>">
          </p>

          <p><span>Reason:</span>
            <textarea name="reason" required rows="4"><?php echo htmlspecialchars($appointment['reason']); ?></textarea>
          </p>

          <p><span>Status:</span>
            <select name="status" required>
              <option value="Pending" <?php if ($appointment['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
              <option value="Completed" <?php if ($appointment['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
              <option value="Cancelled" <?php if ($appointment['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
          </p>

          <p style="padding-top: 15px">
            <input class="submit" type="submit" value="Update Now">
          </p>
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
