<?php
// Configure session settings before starting
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// Set session timeout to 30 minutes
ini_set('session.gc_maxlifetime', 1800);

// Start the session
session_start();

// Regenerate session ID on login for security (helps prevent session fixation)
if (!isset($_SESSION['_session_started'])) {
    $_SESSION['_session_started'] = true;
}

// Prevent caching of sensitive pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

function requireAuth() {
    if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['uid']) && !empty($_SESSION['uid']);
}
?>
