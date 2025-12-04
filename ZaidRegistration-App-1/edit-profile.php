<?php
require_once 'includes/session.php';
require_once 'dbConnection.php'; // Included for MySQL connection
require_once 'includes/functions.php';

requireAuth();

// Fetch user profile using the MySQL-based function
$userProfile = getUserProfile($_SESSION['uid']);
$error = '';
$success = '';

if ($_POST) {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profileImageUrl = trim($_POST['profileImageUrl'] ?? '');
    $backgroundType = $_POST['backgroundType'] ?? 'color';
    $backgroundColor = trim($_POST['backgroundColor'] ?? '#3498db');
    $backgroundImageUrl = trim($_POST['backgroundImageUrl'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email)) {
        $error = 'First name, last name, and email are required.';
    } else {
        // Check if email is taken by another user (MySQL-based check)
        if ($email !== $userProfile['email'] && isEmailTaken($email)) {
            $error = 'Email already exists.';
        } else {
            // NOTE: File upload handling (handleImageUpload) is outside the scope of the database migration
            // and is assumed to return a file path or URL on success.

            // Handle profile image upload
            if (isset($_FILES['profileImageFile']) && $_FILES['profileImageFile']['error'] === UPLOAD_ERR_OK) {
                $uploadedImage = handleImageUpload($_FILES, 'profileImageFile');
                if ($uploadedImage !== false && $uploadedImage !== null) {
                    $profileImageUrl = $uploadedImage;
                }
            }

            // Handle background image upload
            $finalBackgroundImage = $backgroundImageUrl;
            if (isset($_FILES['backgroundImageFile']) && $_FILES['backgroundImageFile']['error'] === UPLOAD_ERR_OK) {
                $uploadedBg = handleImageUpload($_FILES, 'backgroundImageFile');
                if ($uploadedBg !== false && $uploadedBg !== null) {
                    $finalBackgroundImage = $uploadedBg;
                }
            }

            // Update user's core info (users table: email) and profile info (profiles table: first/last name, img_path)
            $profileUpdateResult = updateUserProfile($_SESSION['uid'], $firstName, $lastName, $email, $profileImageUrl);

            if ($profileUpdateResult) {
                // Update profile background (profiles table: backgroundType, backgroundColor, backgroundImage)
                // This function definition must be present in includes/functions.php
                updateProfileBackground($_SESSION['uid'], $backgroundType, $backgroundColor, $finalBackgroundImage);

                $success = 'Profile updated successfully!';
                // Refresh profile data from the database
                $userProfile = getUserProfile($_SESSION['uid']);
            } else {
                $error = 'Failed to update profile. (Database error or no changes made).';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App - Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .profile-preview {
            text-align: center;
            margin-bottom: 2rem;
        }
        .profile-preview img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
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
            <h2 class="text-center mb-4">Edit Profile</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="profile-preview">
                <img id="profilePreview" src="<?php echo htmlspecialchars($userProfile['imgPath']); ?>" alt="Profile Picture" onerror="this.src='https://via.placeholder.com/120'">
            </div>

            <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userProfile['firstName']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userProfile['lastName']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="url" class="form-control mb-2" id="profileImageUrl" name="profileImageUrl" value="<?php echo htmlspecialchars($userProfile['imgPath']); ?>" placeholder="https://example.com/image.jpg">
                    <input type="file" class="form-control" id="profileImageFile" name="profileImageFile" accept="image/*">
                    <div class="form-text">Enter a URL or upload an image from your device</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Background</label>
                    <div class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backgroundType" id="bgTypeColor" value="color" <?php echo ($userProfile['backgroundType'] ?? 'color') === 'color' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bgTypeColor">Solid Color</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="backgroundType" id="bgTypeImage" value="image" <?php echo ($userProfile['backgroundType'] ?? 'color') === 'image' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bgTypeImage">Background Image</label>
                        </div>
                    </div>

                    <div id="colorOptions" style="display: <?php echo ($userProfile['backgroundType'] ?? 'color') === 'color' ? 'block' : 'none'; ?>;">
                        <label for="backgroundColor" class="form-label">Choose Color</label>
                        <input type="color" class="form-control form-control-color" id="backgroundColor" name="backgroundColor" value="<?php echo htmlspecialchars($userProfile['backgroundColor'] ?? '#3498db'); ?>">
                    </div>

                    <div id="imageOptions" style="display: <?php echo ($userProfile['backgroundType'] ?? 'color') === 'image' ? 'block' : 'none'; ?>;">
                        <input type="url" class="form-control mb-2" id="backgroundImageUrl" name="backgroundImageUrl" value="<?php echo htmlspecialchars($userProfile['backgroundImage'] ?? ''); ?>" placeholder="https://example.com/background.jpg">
                        <input type="file" class="form-control" id="backgroundImageFile" name="backgroundImageFile" accept="image/*">
                        <div class="form-text">Enter a URL or upload a background image</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username (cannot be changed)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['username']); ?>" disabled>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview profile image when URL changes
    document.getElementById('profileImageUrl').addEventListener('input', function(e) {
        const preview = document.getElementById('profilePreview');
        const url = e.target.value;
        if (url) {
            preview.src = url;
        }
    });

    // Toggle background options
    document.querySelectorAll('input[name="backgroundType"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'color') {
                document.getElementById('colorOptions').style.display = 'block';
                document.getElementById('imageOptions').style.display = 'none';
            } else {
                document.getElementById('colorOptions').style.display = 'none';
                document.getElementById('imageOptions').style.display = 'block';
            }
        });
    });
</script>

</body>
</html>