<?php
// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit;
}

// Get current page name for active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-shield-alt me-2"></i>Admin Panel
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>" 
                       href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'contacts.php') ? 'active' : ''; ?>" 
                       href="contacts.php">
                        <i class="fas fa-envelope me-1"></i>Contact Submissions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.html" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>View Website
                    </a>
                </li>
            </ul>
            
            <div class="navbar-nav">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="contacts.php">
                                <i class="fas fa-envelope me-2"></i>Manage Contacts
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="handlers/auth_handler.php?action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar-nav .nav-link.active {
    background-color: rgba(255,255,255,0.2);
    border-radius: 4px;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    border-radius: 4px;
}
</style>
