<?php
// Database Setup Script for Abhiverdhya Admin Panel
// Run this file once to set up the database and admin user

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$database = 'abhiverdhya_admin';

echo "<h2>Abhiverdhya Admin Panel Database Setup</h2>";

try {
    // First, connect without database to create it
    $pdo = new PDO("mysql:host=localhost;port=3307", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p>✅ Connected to MySQL successfully</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "<p>✅ Database '$database' created/verified</p>";
    
    // Now connect to the specific database
    $pdo = new PDO("mysql:host=localhost;port=3307;dbname=$database", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create admin_users table
    $adminUsersSQL = "
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        )
    ";
    
    $pdo->exec($adminUsersSQL);
    echo "<p>✅ Admin users table created</p>";
    
    // Create contact_submissions table
    $contactSubmissionsSQL = "
        CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            product_type VARCHAR(50) DEFAULT NULL,
            message TEXT NOT NULL,
            form_source VARCHAR(50) NOT NULL,
            status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            admin_notes TEXT DEFAULT NULL
        )
    ";
    
    $pdo->exec($contactSubmissionsSQL);
    echo "<p>✅ Contact submissions table created</p>";
    
    // Create indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_form_source ON contact_submissions(form_source)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status ON contact_submissions(status)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_submitted_at ON contact_submissions(submitted_at)");
    echo "<p>✅ Database indexes created</p>";
    
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch()['count'] > 0;
    
    if (!$adminExists) {
        // Create admin user with hashed password
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@abhiverdhya.com']);
        echo "<p>✅ Admin user created successfully</p>";
        echo "<p><strong>Login credentials:</strong></p>";
        echo "<p>Username: <code>admin</code></p>";
        echo "<p>Password: <code>admin123</code></p>";
    } else {
        echo "<p>⚠️ Admin user already exists</p>";
        
        // Update the password in case it was corrupted
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$hashedPassword]);
        echo "<p>✅ Admin password updated to ensure it works</p>";
        echo "<p><strong>Login credentials:</strong></p>";
        echo "<p>Username: <code>admin</code></p>";
        echo "<p>Password: <code>admin123</code></p>";
    }
    
    // Insert sample contact data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contact_submissions");
    $stmt->execute();
    $contactsExist = $stmt->fetch()['count'] > 0;
    
    if (!$contactsExist) {
        $sampleData = [
            ['John Doe', 'john@example.com', '+91 9876543210', 'packaging', 'Interested in packaging solutions for food industry', 'contact_page', 'new'],
            ['Jane Smith', 'jane@example.com', '+91 9876543211', 'industrial', 'Need industrial components for automotive', 'popup_form', 'read'],
            ['Mike Johnson', 'mike@example.com', '+91 9876543212', 'medical', 'Medical grade plastics required', 'about_page', 'new']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, product_type, message, form_source, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleData as $data) {
            $stmt->execute($data);
        }
        
        echo "<p>✅ Sample contact data inserted</p>";
    } else {
        echo "<p>ℹ️ Contact submissions table already has data</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 Setup Complete!</h3>";
    echo "<p><strong>You can now:</strong></p>";
    echo "<ul>";
    echo "<li><a href='login.php' target='_blank'>Login to Admin Panel</a></li>";
    echo "<li><a href='../index.html' target='_blank'>Visit Your Website</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Admin Panel URLs:</strong></p>";
    echo "<ul>";
    echo "<li>Login: <a href='login.php'>login.php</a></li>";
    echo "<li>Dashboard: <a href='dashboard.php'>dashboard.php</a></li>";
    echo "<li>Contacts: <a href='contacts.php'>contacts.php</a></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check if phpMyAdmin is accessible at <a href='http://localhost/phpmyadmin'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Verify database connection settings in config/database.php</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h2 {
    color: #2c5aa0;
    border-bottom: 2px solid #2c5aa0;
    padding-bottom: 10px;
}

h3 {
    color: #28a745;
}

p {
    margin: 10px 0;
}

code {
    background-color: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: monospace;
}

ul li {
    margin: 5px 0;
}

a {
    color: #2c5aa0;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
