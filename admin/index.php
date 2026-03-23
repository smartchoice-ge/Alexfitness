<?php
// Admin Panel Entry Point
// Prevent direct script access
if (empty($_SERVER['HTTP_HOST'])) {
    die('Direct script access is not allowed.');
}

// Start session with enhanced security
session_start([
    'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Strict'  // Prevent CSRF
]);

// Check if user is already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // User is already logged in, redirect to users list (main admin page)
    header('Location: users_list.php');
    exit;
}

// If not logged in, redirect to main login page
header('Location: ../login.php');
exit;
?>
