<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // <--- NEW: Required for MySQL functions
require_once 'includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetUid = $_POST['target_uid'] ?? '';
    $action = $_POST['action'] ?? '';
    
    // Ensure UIDs are treated as integers when interacting with the database
    $currentUid = (int)$_SESSION['uid']; 
    $targetUid = (int)$targetUid;
    
    if (!empty($targetUid) && in_array($action, ['follow', 'unfollow'])) {
        // The followUser and unfollowUser functions are now assumed to handle the 
        // MySQL INSERT/DELETE operations for the followers table.
        if ($action === 'follow') {
            followUser($currentUid, $targetUid);
        } else {
            unfollowUser($currentUid, $targetUid);
        }
    }
    
    // Redirect back to the referring page
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: ' . $referer);
    exit;
}

header('Location: index.php');
exit;
?>