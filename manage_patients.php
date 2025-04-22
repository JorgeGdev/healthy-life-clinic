<?php
require 'config.php';
require 'checksession.php'; // Asegura que isAdmin() esté disponible

// Solo admin accede
if (!isAdmin()) {
  header('Location: login.php');
  exit;
}

$success = '';
$error = '';

// Manejo de formulario POST (crear, editar o eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM patients WHERE patient_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success = "Patient deleted successfully.";
  } else {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    } else {
      $query = "SELECT password FROM patients WHERE patient_id = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $_POST['id']);
      $stmt->execute();
      $stmt->bind_result($hashed_password);
      $stmt->fetch();
      $stmt->close();
    }

    if (isset($_POST['id']) && $_POST['id'] !== '') {
      $id = intval($_POST['id']);
      $query = "UPDATE patients SET first_name=?, last_name=?, email=?, phone=?, password=? WHERE patient_id=?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $hashed_password, $id);
    } else {
      $query = "INSERT INTO patients (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hashed_password);
    }

    if ($stmt->execute()) {
      $success = "Patient saved successfully.";
    } else {
      $error = "Error saving patient: " . mysqli_error($conn);
    }

    $stmt->close();
  }
}

$query = "SELECT * FROM patients";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Patients - Healthy Life Clinic</title>
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
          <h2>Manage Patients</h2>
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
        <h3>Quick Tips</h3>
        <p>Use this form to add, edit, or delete patients from the system.</p>
      </div>

      <div id="content">
        <h1>Patient Management</h1>

        <?php if ($success) echo '<p style="color:green;">' . $success . '</p>'; ?>
        <?php if ($error) echo '<p style="color:red;">' . $error . '</p>'; ?>

        <h2>Add / Edit Patient</h2>
        <form method="POST" class="form_settings">
          <input type="hidden" name="id" id="patient_id">
          <p><span>First Name:</span><input type="text" name="first_name" id="first_name" required></p>
          <p><span>Last Name:</span><input type="text" name="last_name" id="last_name" required></p>
          <p><span>Email:</span><input type="email" name="email" id="email" required></p>
          <p><span>Phone:</span><input type="text" name="phone" id="phone"></p>
          <p><span>Password:</span><input type="password" name="password" id="password" placeholder="Leave blank to keep current"></p>
          <p style="padding-top: 15px">
            <button type="submit" class="submit">Save Patient</button>
          </p>
        </form>

        <h2>Search Patients</h2>
        <input type="text" id="searchInput" placeholder="Search by name..." onkeyup="filterPatients()" style="width: 100%; padding: 8px; margin-bottom: 15px;">



        <h2>Existing Patients</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $row['patient_id']; ?></td>
              <td><?php echo htmlspecialchars($row['first_name']); ?></td>
              <td><?php echo htmlspecialchars($row['last_name']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td><?php echo htmlspecialchars($row['phone']); ?></td>
              <td>
                <button class="btn-green"
                  onclick="editPatient(<?php echo $row['patient_id']; ?>, '<?php echo addslashes($row['first_name']); ?>', '<?php echo addslashes($row['last_name']); ?>', '<?php echo $row['email']; ?>', '<?php echo $row['phone']; ?>')">
                  Edit
                </button>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                  <input type="hidden" name="id" value="<?php echo $row['patient_id']; ?>">
                  <button type="submit" name="delete" class="btn-green">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>

    <div id="footer">
      &copy; 2024 Healthy Life Clinic | <a href="http://validator.w3.org/check?uri=referer">HTML5</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a>
    </div>
  </div>
  <script src="style/script.js"></script>
  <script>
    function editPatient(id, first, last, email, phone) {
      document.getElementById('patient_id').value = id;
      document.getElementById('first_name').value = first;
      document.getElementById('last_name').value = last;
      document.getElementById('email').value = email;
      document.getElementById('phone').value = phone;
    }
  </script>

</body>

</html>

<?php mysqli_close($conn); ?>