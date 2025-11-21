<?php
// Include the database connection file. 
$pdo = require 'dbConnection.php';

// SQL query to select all users from the reg1_users table
// --- CORRECTION MADE HERE: Changed 'id' to 'uid' ---
$sql = "SELECT uid, username, email, created_at, updated_at FROM reg1_users";

try {
    // 1. Prepare the SQL statement
    $stmt = $pdo->prepare($sql);
    
    // 2. Execute the statement
    $stmt->execute();
    
    // 3. Fetch all results
    $users = $stmt->fetchAll();

} catch (\PDOException $e) {
    // Basic error handling for the query
    echo "Query Error: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Data Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>✅ User Data from `reg1_users` Table</h2>
    
    <?php if ($users): ?>
        <table>
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['uid']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td><?= htmlspecialchars($user['updated_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found in the database.</p>
    <?php endif; ?>

    <p>This successful retrieval confirms your database connection is working!</p>
</body>
</html>