<?php
require 'config.php';

$error = '';
$success = '';
$submitted = false;

// Lógica cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $submitted = true;
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $query = "SELECT password FROM admins WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hash = $row['password'];

    if (password_verify($password, $hash)) {
      $success = "✅ Password is VALID for $email";
    } else {
      $error = "❌ Password is INVALID for $email";
    }
  } else {
    $error = "❌ No admin found with that email.";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Check Admin Password</title>
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
          <h2>Admin Password Checker</h2>
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
        <h3>Test Info</h3>
        <p>This tool lets you manually test hashed admin passwords.</p>
        <h3>Example Logins</h3>
        <ul>
          <li>admin@example.com</li>
          <li>mainadmin@example.com</li>
          <li>ppaladmin@example.com</li>
        </ul>
      </div>

      <div id="content">
        <h1>Check Admin Password</h1>

        <?php if ($error): ?>
          <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
          <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php elseif ($submitted): ?>
          <p>No result found.</p>
        <?php endif; ?>

        <form method="POST" class="form_settings">
          <p><span>Email:</span><input type="email" name="email" required></p>
          <p><span>Password:</span><input type="password" name="password" required></p>
          <p style="padding-top: 15px">
            <button type="submit" class="submit">Check Password</button>
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