<?php

// NOTE: isLoggedIn() and requireAuth() definitions are assumed to be in includes/session.php
// to prevent the "Cannot redeclare" error.
// This file assumes dbConnection.php has been included upstream and defines the global $pdo object.


// --- 1. Authentication and Validation Functions ---

/**
 * Validates password complexity.
 * @param string $password
 * @return bool
 */
function validatePassword(string $password): bool
{
    // Simple length check for demonstration
    if (strlen($password) < 8) {
        return false;
    }
    return true;
}

/**
 * Checks if a username already exists in the 'users' table (MySQL).
 * Assumes 'id' is the primary key.
 * @param string $username
 * @return bool
 */
function isUsernameTaken(string $username): bool
{
    global $pdo;
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetchColumn() !== false;
}

/**
 * Checks if an email already exists in the 'users' table (MySQL).
 * Assumes 'id' is the primary key.
 * @param string $email
 * @return bool
 */
function isEmailTaken(string $email): bool
{
    global $pdo;
    $sql = "SELECT id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetchColumn() !== false;
}

/**
 * Authenticates a user against the 'users' table (MySQL).
 * FIX: Uses u.id AS uid.
 * @param string $username
 * @param string $password
 * @return int|false The user's UID (from the 'id' column) on success, false on failure.
 */
function authenticateUser(string $username, string $password): int|false
{
    global $pdo;
    $sql = "SELECT id AS uid, password_hash FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        return (int)$user['uid'];
    }
    return false;
}

// --- 2. User/Profile Creation and Retrieval Functions ---

/**
 * Registers a new user and creates their profile (MySQL transaction).
 * @param string $username
 * @param string $email
 * @param string $password
 * @param string $firstName
 * @param string $lastName
 * @return int|false The new user's ID (from auto-increment) on success, false on failure.
 */
function createUser(string $username, string $email, string $password, string $firstName, string $lastName): int|false
{
    global $pdo;
    $pdo->beginTransaction();

    try {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // 1. Insert into users table
        $sqlUser = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);

        $newUserId = (int)$pdo->lastInsertId();

        // 2. Insert into profiles table using correct column names (user_id, camelCase columns)
        $sqlProfile = "INSERT INTO profiles (user_id, first_name, last_name, img_path, backgroundType, backgroundColor) 
                       VALUES (:user_id, :firstName, :lastName, :imgPath, :backgroundType, :backgroundColor)";
        $stmtProfile = $pdo->prepare($sqlProfile);
        $stmtProfile->execute([
            ':user_id' => $newUserId,
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':imgPath' => 'https://via.placeholder.com/120',
            ':backgroundType' => 'color',
            ':backgroundColor' => '#3498db'
        ]);

        $pdo->commit();
        return $newUserId;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Registration failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetches combined user and profile data for a given UID (MySQL JOIN).
 * @param int $uid
 * @return array|false The profile array on success, false otherwise.
 */
function getUserProfile(int $uid): array|false
{
    global $pdo;
    $sql = "SELECT 
                u.id AS uid, u.username, u.email,
                p.first_name AS firstName, p.last_name AS lastName, 
                p.img_path AS imgPath, p.backgroundType, 
                p.backgroundColor, p.backgroundImage
            FROM users u
            JOIN profiles p ON u.id = p.user_id
            WHERE u.id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetches all user profiles, excluding the current user (MySQL).
 * @param int $currentUid
 * @return array A list of user profile arrays.
 */
function getAllUsers(int $currentUid): array
{
    global $pdo;
    $sql = "SELECT 
                u.id AS uid, u.username, 
                p.first_name AS firstName, p.last_name AS lastName, 
                p.img_path AS imgPath
            FROM users u
            JOIN profiles p ON u.id = p.user_id
            WHERE u.id != :currentUid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':currentUid', $currentUid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- 3. Profile Update Functions ---

/**
 * Updates user information across the 'users' (email) and 'profiles' (names, image) tables (MySQL).
 * @param int $uid
 * @param string $firstName
 * @param string $lastName
 * @param string $email
 * @param string $profileImageUrl
 * @return bool True on success, false on failure.
 */
function updateUserProfile(int $uid, string $firstName, string $lastName, string $email, string $profileImageUrl): bool
{
    global $pdo;
    $pdo->beginTransaction();

    try {
        // 1. Update users table (using 'id' as primary key)
        $sqlUser = "UPDATE users SET email = :email WHERE id = :uid";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([':email' => $email, ':uid' => $uid]);

        // 2. Update profiles table with correct column names
        $sqlProfile = "UPDATE profiles SET 
                       first_name = :firstName, 
                       last_name = :lastName, 
                       img_path = :imgPath 
                       WHERE user_id = :uid";
        $stmtProfile = $pdo->prepare($sqlProfile);
        $stmtProfile->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':imgPath' => $profileImageUrl,
            ':uid' => $uid
        ]);

        $pdo->commit();
        return true;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Profile update failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Updates the profile background settings for a specific user (MySQL).
 * @param int $uid The user ID.
 * @param string $backgroundType 'color' or 'image'.
 * @param string $backgroundColor The hex color code.
 * @param string $backgroundImageUrl The URL or path to the background image.
 * @return bool True on success, false on failure.
 */
function updateProfileBackground(int $uid, string $backgroundType, string $backgroundColor, string $backgroundImageUrl): bool
{
    global $pdo;

    if (!$uid) {
        return false;
    }

    $sql = "UPDATE profiles SET 
                backgroundType = :backgroundType, 
                backgroundColor = :backgroundColor, 
                backgroundImage = :backgroundImage 
            WHERE user_id = :uid";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':backgroundType', $backgroundType);
        $stmt->bindParam(':backgroundColor', $backgroundColor);
        $stmt->bindParam(':backgroundImage', $backgroundImageUrl);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database error in updateProfileBackground: " . $e->getMessage());
        return false;
    }
}

/**
 * Changes a user's password in the 'users' table (MySQL).
 * @param int $uid
 * @param string $newPassword
 * @return bool True on success, false on failure.
 */
function changeUserPassword(int $uid, string $newPassword): bool
{
    global $pdo;
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Must use the correct primary key column, assumed 'id'
    $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :uid";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        // error_log("Password change failed: " . $e->getMessage());
        return false;
    }
}

// --- 4. Following/Follower Functions ---

/**
 * Checks if one user is following another (MySQL).
 * @param int $followerUid The UID of the user doing the following.
 * @param int $targetUid The UID of the user being followed.
 * @return bool
 */
function isFollowing(int $followerUid, int $targetUid): bool
{
    global $pdo;
    $sql = "SELECT COUNT(*) FROM followers WHERE follower_uid = :followerUid AND following_uid = :targetUid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':followerUid', $followerUid, PDO::PARAM_INT);
    $stmt->bindParam(':targetUid', $targetUid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

/**
 * Creates a new follow relationship (MySQL).
 * @param int $followerUid The UID of the user doing the following.
 * @param int $targetUid The UID of the user being followed.
 * @return bool
 */
function followUser(int $followerUid, int $targetUid): bool
{
    global $pdo;
    // Check if not already following
    if ($followerUid === $targetUid || isFollowing($followerUid, $targetUid)) {
        return false;
    }

    $sql = "INSERT INTO followers (follower_uid, following_uid) VALUES (:followerUid, :targetUid)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->bindParam(':followerUid', $followerUid, PDO::PARAM_INT);
        $stmt->bindParam(':targetUid', $targetUid, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        // error_log("Follow action failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Removes a follow relationship (MySQL).
 * @param int $followerUid The UID of the user doing the unfollowing.
 * @param int $targetUid The UID of the user being unfollowed.
 * @return bool
 */
function unfollowUser(int $followerUid, int $targetUid): bool
{
    global $pdo;
    $sql = "DELETE FROM followers WHERE follower_uid = :followerUid AND following_uid = :targetUid";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->bindParam(':followerUid', $followerUid, PDO::PARAM_INT);
        $stmt->bindParam(':targetUid', $targetUid, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        // error_log("Unfollow action failed: " . $e->getMessage());
        return false;
    }
}

// --- 5. Helper Functions ---

/**
 * Placeholder for image upload logic (Out of Scope for DB migration).
 * @param array $files The $_FILES array.
 * @param string $inputName The name of the file input field.
 * @return string|false The path/URL to the uploaded file, or false on failure.
 */
function handleImageUpload(array $files, string $inputName): string|false
{
    // Simplified: return a placeholder path
    if (isset($files[$inputName]) && $files[$inputName]['error'] === UPLOAD_ERR_OK) {
        return 'assets/images/uploaded/' . $files[$inputName]['name'];
    }
    return false;
}