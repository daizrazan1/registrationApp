<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // <--- NEW: Required for MySQL functions
require_once 'includes/functions.php';

requireAuth();

// This now calls the MySQL-based getUserProfile function
$userProfile = getUserProfile($_SESSION['uid']);

if (!$userProfile) {
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App - Profile</title>
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
        $bgType = $userProfile['backgroundType'] ?? 'color';
        $bgColor = $userProfile['backgroundColor'] ?? '#3498db';
        $bgImage = $userProfile['backgroundImage'] ?? '';
        
        if ($bgType === 'image' && !empty($bgImage)) {
            echo '<div class="profile-background" style="background-image: url(\'' . htmlspecialchars($bgImage) . '\'); background-size: cover; background-position: center;"></div>';
        } else {
            echo '<div class="profile-background" style="background-color: ' . htmlspecialchars($bgColor) . ';"></div>';
        }
        ?>
        
        <div class="profile-content">
            <img src="<?php echo htmlspecialchars($userProfile['imgPath']); ?>" alt="Profile Picture" class="profile-image" onerror="this.src='https://via.placeholder.com/150'">
            <h2><?php echo htmlspecialchars($userProfile['firstName'] . ' ' . $userProfile['lastName']); ?></h2>
            <p class="text-muted">@<?php echo htmlspecialchars($userProfile['username']); ?></p>
            <div class="d-flex justify-content-center gap-4 mt-3">
                <div><strong><?php echo count(json_decode($userProfile['followers'] ?? '[]')); ?></strong> <span class="text-muted">Followers</span></div>
                <div><strong><?php echo count(json_decode($userProfile['following'] ?? '[]')); ?></strong> <span class="text-muted">Following</span></div>
            </div>
        </div>
    </div>

    <div class="profile-details">
        <h4 class="mb-3">Profile Information</h4>
        
        <div class="detail-row">
            <div class="detail-label">Username</div>
            <div class="detail-value"><?php echo htmlspecialchars($userProfile['username']); ?></div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Email Address</div>
            <div class="detail-value d-flex align-items-center justify-content-between">
                <span id="emailValue" class="sensitive-data">••••••••••••</span>
                <button class="btn btn-sm btn-link" onclick="toggleVisibility('email')" type="button">
                    <svg id="emailEyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">First Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($userProfile['firstName']); ?></div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">Last Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($userProfile['lastName']); ?></div>
        </div>
        
        <div class="detail-row">
            <div class="detail-label">User ID</div>
            <div class="detail-value d-flex align-items-center justify-content-between">
                <span id="uidValue" class="sensitive-data">••••••••</span>
                <button class="btn btn-sm btn-link" onclick="toggleVisibility('uid')" type="button">
                    <svg id="uidEyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="edit-profile.php" class="btn btn-primary me-2">Edit Profile</a>
        <a href="change-password.php" class="btn btn-outline-secondary">Change Password</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sensitiveData = {
        email: {
            hidden: true,
            value: '<?php echo htmlspecialchars($userProfile['email']); ?>',
            placeholder: '••••••••••••'
        },
        // We use the 'id' field from the database, but keep the name 'uid' for backwards compatibility with the JS
        uid: { 
            hidden: true,
            value: '<?php echo htmlspecialchars($userProfile['id'] ?? $userProfile['uid']); ?>', // Use 'id' (MySQL) or fallback to 'uid' (JSON)
            placeholder: '••••••••'
        }
    };

    function toggleVisibility(field) {
        const valueElement = document.getElementById(field + 'Value');
        const eyeIcon = document.getElementById(field + 'EyeIcon');
        
        sensitiveData[field].hidden = !sensitiveData[field].hidden;
        
        // Logic to toggle eye icon SVG
        if (sensitiveData[field].hidden) {
            valueElement.textContent = sensitiveData[field].placeholder;
            eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
        } else {
            valueElement.textContent = sensitiveData[field].value;
            eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
        }
    }
</script>

</body>
</html>