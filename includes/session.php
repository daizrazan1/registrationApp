<?php
session_start();

function requireAuth() {
    if (!isset($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['uid']);
}
?>
