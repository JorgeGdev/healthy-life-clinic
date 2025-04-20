<?php
require 'config.php';
require 'checksession.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Notificaciones
$success = '';
$error = '';

// Crear o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $specialization = trim($_POST['specialization']);

    if ($first_name && $last_name && $specialization) {
        if (!empty($_POST['id'])) {
            // Update
            $id = intval($_POST['id']);
            $sql = "UPDATE providers SET first_name=?, last_name=?, specialization=? WHERE provider_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $first_name, $last_name, $specialization, $id);
        } else {
            // Insert
            $sql = "INSERT INTO providers (first_name, last_name, specialization) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $first_name, $last_name, $specialization);
        }

        if ($stmt->execute()) {
            $success = "Provider saved successfully.";
        } else {
            $error = "Error saving provider: " . $conn->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill out all fields.";
    }
}

// Eliminar
if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $sql = "DELETE FROM providers WHERE provider_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Provider deleted.";
    } else {
        $error = "Error deleting provider: " . $conn->error;
    }

    $stmt->close();
}

// Obtener lista
$providers = $conn->query("SELECT * FROM providers ORDER BY last_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Providers - Healthy Life Clinic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style/style.css" />
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
          <li><a href="admin_dashboard.php">Dashboard</a></li>
          <li><a href="manage_patients.php">Manage Patients</a></li>
          <li><a href="list_appointments.php">Appointments</a></li>
          <li><a href="make_appointment.php">New Appointment</a></li>
        </ul>
      </div>

      <div id="content">
        <h1>Manage Providers</h1>

        <?php if ($success): ?>
          <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
          <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="manage_providers.php" method="post" class="form_settings">
          <input type="hidden" name="id" id="provider_id" />
          <p><span>First Name:</span><input type="text" name="first_name" id="first_name" required /></p>
          <p><span>Last Name:</span><input type="text" name="last_name" id="last_name" required /></p>
          <p><span>Specialization:</span><input type="text" name="specialization" id="specialization" required /></p>
          <p style="padding-top: 15px"><input class="submit" type="submit" value="Save Provider" /></p>
        </form>

        <h2>Existing Providers</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Specialization</th>
            <th>Actions</th>
          </tr>
          <?php while ($row = $providers->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['provider_id']; ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
            <td>
              <button onclick="editProvider('<?php echo $row['provider_id']; ?>', '<?php echo $row['first_name']; ?>', '<?php echo $row['last_name']; ?>', '<?php echo $row['specialization']; ?>')">Edit</button>
              <form method="post" action="manage_providers.php" style="display:inline;" onsubmit="return confirm('Delete this provider?');">
                <input type="hidden" name="delete" value="<?php echo $row['provider_id']; ?>">
                <button type="submit">Delete</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>

    <div id="footer">
      &copy; 2025 Healthy Life Clinic |
      <a href="privacy.php">Privacy</a>
    </div>
  </div>

  <script>
    function editProvider(id, first, last, spec) {
      document.getElementById('provider_id').value = id;
      document.getElementById('first_name').value = first;
      document.getElementById('last_name').value = last;
      document.getElementById('specialization').value = spec;
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>
