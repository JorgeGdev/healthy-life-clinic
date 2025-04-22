<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Include DB connection
require 'config.php';

// Variable para errores
$error = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = mysqli_real_escape_string($conn, trim($_POST['email']));
  $password = trim($_POST['password']);
  $user_type = $_POST['user_type'];

  // Selección de tabla
  $query = ($user_type == 'admin')
    ? "SELECT admin_id AS id, password FROM admins WHERE email = ?"
    : "SELECT patient_id AS id, password FROM patients WHERE email = ?";

  // Verificación
  if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user_type;

        // Redirección según el tipo de usuario
        if ($user_type === 'admin') {
          header('Location: admin_dashboard.php');
        } else {
          header('Location: index.php');
        }
        exit();
      } else {
        $error = "Invalid email or password.";
      }
    } else {
      $error = "No user found with that email.";
    }

    $stmt->close();
  } else {
    $error = "Database error. Please try again later.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login - Healthy Life Clinic</title>
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
          <h2>Your Healthcare Partner</h2>
        </div>
      </div>
      <div id="menubar">
        <div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>
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
        <h3>Need Help?</h3>
        <p>If you're having trouble logging in, please contact support or reset your password.</p>
        <h3>Quick Links</h3>
        <ul>
          <li><a href="index.php">Back to Home</a></li>
          <li><a href="privacy.php">Privacy Policy</a></li>
        </ul>
      </div>

      <div id="content">
        <h1>Login</h1>

        <?php if (!empty($error)) : ?>
          <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="form_settings">
          <p><span>Email:</span><input type="email" name="email" required></p>
          <p><span>Password:</span><input type="password" name="password" required></p>
          <p><span>Login as:</span>
            <select name="user_type" required>
              <option value="patient">Patient</option>
              <option value="admin">Admin</option>
            </select>
          </p>
          <p class="form-submit">
            <button type="submit" class="submit">Login</button>
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
  <script src="style/script.js"></script>
</body>

</html>

<?php mysqli_close($conn); ?>