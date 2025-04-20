<?php
require 'checksession.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if (isAdmin()) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
