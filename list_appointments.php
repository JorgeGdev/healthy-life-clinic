<?php
require 'config.php';
require 'checksession.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Load appointments depending on user type
if (isPatient()) {
    $patient_id = $_SESSION['user_id'];
    $sql = "SELECT a.appointment_id, 
                   p.first_name AS patient_first_name, p.last_name AS patient_last_name, 
                   pr.first_name AS provider_first_name, pr.last_name AS provider_last_name, pr.specialization, 
                   a.appointment_date, a.appointment_time, a.status 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            JOIN providers pr ON a.provider_id = pr.provider_id
            WHERE a.patient_id = ?
            ORDER BY a.appointment_date, a.appointment_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Admin sees all
    $sql = "SELECT a.appointment_id, 
                   p.first_name AS patient_first_name, p.last_name AS patient_last_name, 
                   pr.first_name AS provider_first_name, pr.last_name AS provider_last_name, pr.specialization, 
                   a.appointment_date, a.appointment_time, a.status 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            JOIN providers pr ON a.provider_id = pr.provider_id
            ORDER BY a.appointment_date, a.appointment_time";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointments - Healthy Life Clinic</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css" title="style" />
    <link rel="stylesheet" type="text/css" href="style/responsive.css" media="screen and (max-width: 768px)">
</head>

<body>
    <div id="main">
        <div id="header">
            <div id="logo">
                <div id="logo_text">
                    <h1><a href="home.php">Healthy<span class="logo_colour"> Life Clinic</span></a></h1>
                    <h2>Appointment Management</h2>
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
                <h3>Shortcuts</h3>
                <ul>
                    <li><a href="make_appointment.php">Make New Appointment</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <h3>Info</h3>
                <p>
                    <?php if (isPatient()) : ?>
                        As a patient, you can <strong>view</strong> and <strong>cancel</strong> your own appointments that are still pending.
                        If you need to reschedule or edit a confirmed appointment, please contact the clinic directly.
                    <?php else : ?>
                        As an admin, you can view, edit, or delete all appointments from the system.
                    <?php endif; ?>
                </p>
            </div>

            <div id="content">
                <h1>Appointment List</h1>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Provider</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['appointment_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['patient_first_name'] . ' ' . $row['patient_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['provider_first_name'] . ' ' . $row['provider_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="appointment_details.php?id=<?php echo $row['appointment_id']; ?>">View</a>
                                <?php if (isAdmin()) : ?>
                                    | <a href="edit_appointment.php?id=<?php echo $row['appointment_id']; ?>">Edit</a>
                                    | <a href="delete_appointment.php?id=<?php echo $row['appointment_id']; ?>" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                                <?php elseif ($row['status'] === 'Pending') : ?>
                                    | <a href="cancel_appointment.php?id=<?php echo $row['appointment_id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>

                <br>
                <form action="home.php" method="get" style="padding-top: 15px;">
                    <button type="submit" class="submit">Back to Home</button>
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
    <script src="style/script.js"></script>
</body>

</html>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
?>