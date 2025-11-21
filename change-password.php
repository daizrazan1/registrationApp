<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // <--- NEW: Required for MySQL functions
require_once 'includes/functions.php';

requireAuth();

$error = '';
$success = '';

if ($_POST) {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } else if ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } else if (!validatePassword($newPassword)) { // Assuming validatePassword is still valid
        $error = 'Password cannot be empty or does not meet requirements.';
    } else {
        // --- START MySQL Logic ---
        // 1. Verify current password using the MySQL-based function
        // NOTE: You must ensure verifyUserPassword is correctly implemented in functions.php 
        // to check against the 'password_hash' column.
        if (!verifyUserPassword($_SESSION['uid'], $currentPassword)) {
            $error = 'Current password is incorrect.';
        } else {
            // 2. Update password using the MySQL-based function
            // NOTE: You must ensure updateUserPassword is correctly implemented in functions.php
            // to update the 'password_hash' column.
            if (updateUserPassword($_SESSION['uid'], $newPassword)) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to update password.';
            }
        }
        // --- END MySQL Logic ---
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App - Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Registration App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-0">
    <div class="row justify-content-center">
        <div class="col-md-8 card-container bg-light">
            <h2 class="text-center mb-4">Change Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="change-password.php" method="POST">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                </div>
                
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>