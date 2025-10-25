<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Function to check if admin is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Function to require admin login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Function to login admin
function loginAdmin($username, $password) {
    try {
        $pdo = getDBConnection();

        // Check if the table exists and username matches
        $sql = "SELECT id, username, password FROM admin_users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        // Debug: check if query returned anything
        if (!$admin) {
            error_log("No user found for username: " . $username);
            return false;
        }

        // ✅ Support both plain text and hashed passwords
        if ($password === $admin['password'] || password_verify($password, $admin['password'])) {
            // Start session for this admin
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            // Update last login time
            $updateSql = "UPDATE admin_users SET last_login = NOW() WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([':id' => $admin['id']]);

            return true;
        } else {
            error_log("Password mismatch for username: " . $username);
            return false;
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}


// Function to logout admin
function logoutAdmin() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: ../login.php');
    exit;
}

// Handle login request
// Debugging: show received POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/debug_log.txt', print_r($_POST, true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    header('Content-Type: application/json');
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
        exit;
    }
    
    if (loginAdmin($username, $password)) {
        echo json_encode(['success' => true, 'message' => 'Login successful!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
    exit;
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logoutAdmin();
}
?>
