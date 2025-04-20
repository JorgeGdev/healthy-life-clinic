<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user is not logged in
function checkLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Check if the current user is an admin
function isAdmin() {
    return (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin');
}

// Check if the current user is a patient
function isPatient() {
    return (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'patient');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


// Destroy session and redirect to login (logout helper)
function logout() {
    $_SESSION = array();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
