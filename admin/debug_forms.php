<?php
require_once __DIR__ . '/handlers/auth_handler.php';
requireLogin();

// Include database configuration
require_once __DIR__ . '/config/database.php';

// Get recent form submissions for debugging
try {
    $pdo = getDBConnection();
    
    // Get all submissions with form source
    $stmt = $pdo->prepare("SELECT * FROM contact_submissions ORDER BY submitted_at DESC LIMIT 20");
    $stmt->execute();
    $submissions = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $submissions = [];
    error_log("Debug forms error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission Debug - Abhiverdhya Industries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .source-home { background-color: #e3f2fd; }
        .source-about { background-color: #f3e5f5; }
        .source-contact_page { background-color: #e8f5e8; }
        .source-popup { background-color: #fff3e0; }
        .source-unknown { background-color: #ffebee; }
    </style>
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacts.php">
                                <i class="fas fa-envelope me-2"></i>Contact Submissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="debug_forms.php">
                                <i class="fas fa-bug me-2"></i>Form Debug
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
                    <h1 class="h2">Form Submission Debug</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Form Source Legend -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Form Source Legend</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="p-2 source-home rounded text-center mb-2">
                                            <strong>home</strong><br>
                                            <small>Index page form</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="p-2 source-about rounded text-center mb-2">
                                            <strong>about</strong><br>
                                            <small>About page form</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="p-2 source-contact_page rounded text-center mb-2">
                                            <strong>contact_page</strong><br>
                                            <small>Contact page form</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="p-2 source-popup rounded text-center mb-2">
                                            <strong>popup</strong><br>
                                            <small>Popup form</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="p-2 source-unknown rounded text-center mb-2">
                                            <strong>unknown</strong><br>
                                            <small>Unidentified source</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Form Submissions (Last 20)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($submissions)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Source</th>
                                            <th>Product Type</th>
                                            <th>Submitted</th>
                                            <th>Message Preview</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr class="source-<?php echo htmlspecialchars($submission['form_source']); ?>">
                                                <td><?php echo $submission['id']; ?></td>
                                                <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                                <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?php echo htmlspecialchars($submission['form_source']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($submission['product_type'] ?: 'N/A'); ?></td>
                                                <td><?php echo date('M j, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $message = htmlspecialchars($submission['message']);
                                                    echo strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No form submissions found. Try submitting a test form.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Test Form -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Test Form Submission</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Use this form to test the submission handler:</p>
                        <form id="testForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="testName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="testName" value="Test User" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="testEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="testEmail" value="test@example.com" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="testPhone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="testPhone" value="1234567890" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="testProductType" class="form-label">Product Type</label>
                                        <select class="form-select" id="testProductType">
                                            <option value="custom">Custom Manufacturing</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="testMessage" class="form-label">Message</label>
                                <textarea class="form-control" id="testMessage" rows="3" required>This is a test message from the admin debug page.</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="testSource" class="form-label">Test Source</label>
                                <select class="form-select" id="testSource" required>
                                    <option value="debug_test">Debug Test</option>
                                    <option value="home">Home Page</option>
                                    <option value="about">About Page</option>
                                    <option value="contact_page">Contact Page</option>
                                    <option value="popup">Popup Form</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Test</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('name', document.getElementById('testName').value);
            formData.append('email', document.getElementById('testEmail').value);
            formData.append('phone', document.getElementById('testPhone').value);
            formData.append('productType', document.getElementById('testProductType').value);
            formData.append('message', document.getElementById('testMessage').value);
            formData.append('formSource', document.getElementById('testSource').value);
            
            fetch('handlers/contact_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Test submission successful! Check the table above.');
                    location.reload();
                } else {
                    alert('Test failed: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        });
    </script>
</body>
</html>
