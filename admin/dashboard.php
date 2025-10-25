<?php
require_once __DIR__ . '/handlers/auth_handler.php';
requireLogin();

// Include database configuration
require_once __DIR__ . '/config/database.php';

// Get dashboard statistics
function getDashboardStats() {
    try {
        $pdo = getDBConnection();
        
        $stats = [];
        
        // Total contacts
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM contact_submissions");
        $stats['total_contacts'] = $stmt->fetch()['total'];
        
        // New contacts (today)
        $stmt = $pdo->query("SELECT COUNT(*) as new_today FROM contact_submissions WHERE DATE(submitted_at) = CURDATE()");
        $stats['new_today'] = $stmt->fetch()['new_today'];
        
        // Pending contacts (new status)
        $stmt = $pdo->query("SELECT COUNT(*) as pending FROM contact_submissions WHERE status = 'new'");
        $stats['pending'] = $stmt->fetch()['pending'];
        
        // This month's contacts
        $stmt = $pdo->query("SELECT COUNT(*) as month FROM contact_submissions WHERE YEAR(submitted_at) = YEAR(CURDATE()) AND MONTH(submitted_at) = MONTH(CURDATE())");
        $stats['this_month'] = $stmt->fetch()['month'];
        
        // Form source breakdown
        $stmt = $pdo->query("SELECT form_source, COUNT(*) as count FROM contact_submissions GROUP BY form_source ORDER BY count DESC");
        $stats['form_sources'] = $stmt->fetchAll();
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        return [];
    }
}

// Get recent contacts
function getRecentContacts($limit = 10) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM contact_submissions ORDER BY submitted_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Recent contacts error: " . $e->getMessage());
        return [];
    }
}

$stats = getDashboardStats();
$recentContacts = getRecentContacts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Abhiverdhya Industries</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>Admin Panel
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="contacts.php">
                            <i class="fas fa-envelope me-2"></i>Manage Contacts
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="handlers/auth_handler.php?action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacts.php">
                                <i class="fas fa-envelope me-2"></i>Contact Submissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.html" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>View Website
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Total Contacts
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            <?php echo number_format($stats['total_contacts'] ?? 0); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-address-book fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="contacts.php">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Pending Responses
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            <?php echo number_format($stats['pending'] ?? 0); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="contacts.php?status=new">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            New Today
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            <?php echo number_format($stats['new_today'] ?? 0); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="contacts.php?filter=today">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            This Month
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold">
                                            <?php echo number_format($stats['this_month'] ?? 0); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="contacts.php?filter=month">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Recent Activity -->
                <div class="row">
                    <!-- Form Sources Chart -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie me-1"></i>Contact Sources
                            </div>
                            <div class="card-body">
                                <canvas id="formSourceChart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Contacts -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-list me-1"></i>Recent Contact Submissions
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Source</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recentContacts)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No contacts found</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($recentContacts as $contact): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                                        <td class="text-truncate" style="max-width: 150px;">
                                                            <?php echo htmlspecialchars($contact['email']); ?>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php 
                                                                $source = str_replace('_', ' ', $contact['form_source']);
                                                                echo ucwords($source);
                                                                ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $statusClass = [
                                                                'new' => 'badge bg-warning',
                                                                'read' => 'badge bg-info',
                                                                'replied' => 'badge bg-success',
                                                                'archived' => 'badge bg-secondary'
                                                            ];
                                                            $class = $statusClass[$contact['status']] ?? 'badge bg-secondary';
                                                            ?>
                                                            <span class="<?php echo $class; ?>">
                                                                <?php echo ucfirst($contact['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo date('M j, Y', strtotime($contact['submitted_at'])); ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="contacts.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View All Contacts
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form Sources Chart
        const formSourceData = <?php echo json_encode($stats['form_sources'] ?? []); ?>;
        
        if (formSourceData.length > 0) {
            const ctx = document.getElementById('formSourceChart').getContext('2d');
            const formSourceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: formSourceData.map(item => item.form_source.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                    datasets: [{
                        data: formSourceData.map(item => item.count),
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
                            '#fd7e14'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            document.getElementById('formSourceChart').innerHTML = '<p class="text-center text-muted">No data available</p>';
        }
    </script>
</body>
</html>
