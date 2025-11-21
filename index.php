<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // Include the DB connection
require_once 'includes/functions.php';

// Redirect to login if not authenticated
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// User ID is now an integer ID from the database
$userId = (int)$_SESSION['uid']; 

// Fetch user profile and all other users using the MySQL functions
$userProfile = getUserProfile($userId); 
$allUsers = getAllUsers($userId); // Get all users except current user
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-content {
            padding-top: 20px;
        }
        .welcome-container {
            text-align: center;
            margin-top: 2rem;
            margin-bottom: 3rem;
        }
        .user-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            height: 100%;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
        }
        .users-grid {
            margin-top: 2rem;
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

<div class="container">
    <div class="welcome-container">
        <h1>Welcome back, <?php echo htmlspecialchars($userProfile['firstName'] ?? $userProfile['username'] ?? 'User'); ?>!</h1>
        <p class="lead">You are successfully logged in to your account.</p>
        <div class="mt-4">
            <a href="profile.php" class="btn btn-primary me-2">View My Profile</a>
            <a href="edit-profile.php" class="btn btn-outline-secondary">Edit Profile</a>
        </div>
    </div>

    <div class="users-section">
        <h3 class="mb-4">All Users</h3>
        <div class="row users-grid">
            <?php if (empty($allUsers)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No other users found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($allUsers as $user): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <a href="view-profile.php?uid=<?php echo urlencode($user['uid']); ?>" class="text-decoration-none">
                            <div class="card user-card">
                                <div class="card-body text-center">
                                    <img src="<?php echo htmlspecialchars($user['imgPath']); ?>" 
                                            alt="<?php echo htmlspecialchars($user['firstName']); ?>" 
                                            class="user-avatar mb-3"
                                            onerror="this.src='https://via.placeholder.com/80'">
                                    <h5 class="card-title mb-1">
                                        <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?>
                                    </h5>
                                    <p class="card-text text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>