<?php
// Database Connection Test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    // Test basic MySQL connection
    $pdo = new PDO("mysql:host=localhost", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "<p>✅ MySQL connection successful</p>";
    
    // Test database exists
    $pdo->exec("USE abhiverdhya_admin");
    echo "<p>✅ Database 'abhiverdhya_admin' exists</p>";
    
    // Test admin_users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $adminCount = $stmt->fetch()['count'];
    echo "<p>✅ Admin users table exists with $adminCount users</p>";
    
    // Test specific admin user
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ Admin user found: " . $admin['username'] . "</p>";
        echo "<p>Password hash: " . substr($admin['password'], 0, 20) . "...</p>";
        
        // Test password verification
        $testPassword = 'admin123';
        if (password_verify($testPassword, $admin['password'])) {
            echo "<p>✅ Password verification works correctly</p>";
        } else {
            echo "<p>❌ Password verification failed</p>";
            // Reset password
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
            $updateStmt->execute([$newHash]);
            echo "<p>✅ Password reset to 'admin123'</p>";
        }
    } else {
        echo "<p>❌ Admin user not found - creating now...</p>";
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@abhiverdhya.com']);
        echo "<p>✅ Admin user created</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Everything looks good!</h3>";
    echo "<p>You should now be able to login with:</p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php'>Try logging in now →</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure XAMPP MySQL is running!</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; }
h2 { color: #2c5aa0; }
h3 { color: #28a745; }
p { margin: 10px 0; }
</style>
