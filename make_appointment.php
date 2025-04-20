<?php
require 'config.php';

$patients_sql = "SELECT patient_id, first_name, last_name FROM patients";
$patients_result = $conn->query($patients_sql);

$providers_sql = "SELECT provider_id, first_name, last_name, specialization FROM providers";
$providers_result = $conn->query($providers_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['patient_id']) || !is_numeric($_POST['patient_id'])) {
        die("Invalid patient ID.");
    }
    if (!isset($_POST['provider_id']) || !is_numeric($_POST['provider_id'])) {
        die("Invalid provider ID.");
    }
    if (!isset($_POST['appointment_date']) || strtotime($_POST['appointment_date']) === false) {
        die("Invalid appointment date.");
    }
    if (!isset($_POST['appointment_time']) || empty($_POST['appointment_time'])) {
        die("Appointment time is required.");
    }
    if (!isset($_POST['reason']) || strlen(trim($_POST['reason'])) < 10) {
        die("Please enter a valid reason (min 10 characters).");
    }

    $patient_id = (int) $_POST['patient_id'];
    $provider_id = (int) $_POST['provider_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = htmlspecialchars(trim($_POST['reason']));

    $insert_sql = "INSERT INTO appointments (patient_id, provider_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisss", $patient_id, $provider_id, $appointment_date, $appointment_time, $reason);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href = 'list_appointments.php';</script>";
    } else {
        echo "Error booking appointment: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Appointment - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/style.css" title="style" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
          <h2>Book a Medical Appointment</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li><a href="index.php">Home</a></li>
          <li><a href="list_appointments.php">Appointments</a></li>
          <li class="selected"><a href="make_appointment.php">Book</a></li>
          <li><a href="privacy.php">Privacy Policy</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>

    <div id="site_content">
      <div class="sidebar">
        <h3>Quick Access</h3>
        <ul>
          <li><a href="list_appointments.php">Your Appointments</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <h3>Help</h3>
        <p>Select a patient and provider, choose a date, and check availability for booking.</p>
      </div>

      <div id="content">
        <h1>Make an Appointment</h1>
        <form action="" method="POST" class="form_settings">
          <p><span>Patient:</span>
            <select name="patient_id" required>
              <option value="" disabled selected>Select a patient</option>
              <?php while ($row = $patients_result->fetch_assoc()): ?>
                <option value="<?php echo $row['patient_id']; ?>">
                  <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </p>

          <p><span>Provider:</span>
            <select id="provider" name="provider_id" required>
              <option value="" disabled selected>Select a provider</option>
              <?php while ($row = $providers_result->fetch_assoc()): ?>
                <option value="<?php echo $row['provider_id']; ?>">
                  <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . " - " . htmlspecialchars($row['specialization']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </p>

          <p><span>Date:</span>
            <input type="date" id="appointment_date" name="appointment_date" required>
          </p>

          <p><span>Available Time:</span>
            <select id="appointment_time" name="appointment_time" required>
              <option value="" disabled selected>Select a time</option>
            </select>
          </p>

          <p><span>Reason:</span>
            <textarea name="reason" rows="4" required minlength="10" maxlength="500"></textarea>
          </p>

          <p style="padding-top: 15px"><input class="submit" type="submit" value="Book Now"></p>
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

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      flatpickr("#appointment_date", {
        dateFormat: "Y-m-d",
        minDate: "today"
      });
    });

    $(document).ready(function () {
      $("#provider, #appointment_date").change(function () {
        var provider_id = $("#provider").val();
        var appointment_date = $("#appointment_date").val();

        if (provider_id && appointment_date) {
          $.ajax({
            url: "check_availability.php",
            type: "POST",
            data: {
              provider_id: provider_id,
              appointment_date: appointment_date
            },
            dataType: "json",
            success: function (data) {
              $("#appointment_time").html('<option value="" disabled selected>Select a time</option>');
              data.forEach(function (time) {
                $("#appointment_time").append('<option value="' + time + '">' + time + '</option>');
              });
            }
          });
        }
      });
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>
