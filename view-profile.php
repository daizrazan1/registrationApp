<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // <--- NEW: Required for MySQL functions
require_once 'includes/functions.php';

requireAuth();

// Get the user ID from the URL (now an integer ID)
$viewUid = $_GET['uid'] ?? '';

if (empty($viewUid)) {
    header('Location: index.php');
    exit;
}

// Prevent viewing own profile through this page
// $_SESSION['uid'] is the integer ID of the logged-in user
if ((int)$viewUid === (int)$_SESSION['uid']) { 
    header('Location: profile.php');
    exit;
}

// This now calls the MySQL-based getUserProfile function
$viewProfile = getUserProfile($viewUid);

if (!$viewProfile) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App - View Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .profile-header {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 0.5rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        .profile-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            border-radius: 0.5rem 0.5rem 0 0;
            z-index: 0;
        }
        .profile-content {
            position: relative;
            z-index: 1;
            padding-top: 120px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .profile-details {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            padding: 1rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            font-size: 1.1rem;
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

<div class="container profile-container">
    <div class="profile-header">
        <?php 
        $bgType = $viewProfile['backgroundType'] ?? 'color';
        $bgColor = $viewProfile['backgroundColor'] ?? '#3498db';
        $bgImage = $viewProfile['backgroundImage'] ?? '';
        
        if ($bgType === 'image' && !empty($bgImage)) {
            echo '<div class="profile-background" style="background-image: url(\'' . htmlspecialchars($bgImage) . '\'); background-size: cover; background-position: center;"></div>';
        } else {
            echo '<div class="profile-background" style="background-color: ' . htmlspecialchars($bgColor) . ';"></div>';
        }
        
        // This now calls the MySQL-based isFollowing function
        $isFollowing = isFollowing($_SESSION['uid'], $viewProfile['id']);
        ?>
        
        <div class="profile-content">
            <img src="<?php echo htmlspecialchars($viewProfile['imgPath']); ?>" alt="Profile Picture" class="profile-image" onerror="this.src='https://via.placeholder.com/150'">
            <h2><?php echo htmlspecialchars($viewProfile['firstName'] . ' ' . $viewProfile['lastName']); ?></h2>
            <p class="text-muted">@<?php echo htmlspecialchars($viewProfile['username']); ?></p>
            
            <div class="d-flex justify-content-center gap-4 mt-3 mb-3">
                <div><strong><?php echo count(json_decode($viewProfile['followers'] ?? '[]')); ?></strong> <span class="text-muted">Followers</span></div>
                <div><strong><?php echo count(json_decode($viewProfile['following'] ?? '[]')); ?></strong> <span class="text-muted">Following</span></div>
            </div>
            
            <form action="follow-action.php" method="POST" class="d-inline">
                <input type="hidden" name="target_uid" value="<?php echo htmlspecialchars($viewProfile['id']); ?>"> 
                <input type="hidden" name="action" value="<?php echo $isFollowing ? 'unfollow' : 'follow'; ?>">
                <button type="submit" class="btn btn-<?php echo $isFollowing ? 'outline-primary' : 'primary'; ?>">
                    <?php echo $isFollowing ? 'Unfollow' : 'Follow'; ?>
                </button>
            </form>
        </div>
    </div>

    <div class="profile-details">
        <h4 class="mb-3">Profile Information</h4>
        
        <div class="detail-row">
            <div class="detail-label">Username</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewProfile['username']); ?></div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">First Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewProfile['firstName']); ?></div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Last Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($viewProfile['lastName']); ?></div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>