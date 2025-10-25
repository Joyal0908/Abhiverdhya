<?php
require_once __DIR__ . '/handlers/auth_handler.php';
requireLogin();

// Include database configuration
require_once __DIR__ . '/config/database.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $pdo = getDBConnection();
        
        if ($_POST['action'] === 'update_status') {
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            
            $validStatuses = ['new', 'read', 'replied', 'archived'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Invalid status');
            }
            
            $stmt = $pdo->prepare("UPDATE contact_submissions SET status = :status, updated_at = NOW() WHERE id = :id");
            $result = $stmt->execute([':status' => $status, ':id' => $id]);
            
            echo json_encode(['success' => $result, 'message' => 'Status updated successfully']);
            exit;
        }
        
        if ($_POST['action'] === 'add_note') {
            $id = (int)$_POST['id'];
            $note = trim($_POST['note']);
            
            $stmt = $pdo->prepare("UPDATE contact_submissions SET admin_notes = :note, updated_at = NOW() WHERE id = :id");
            $result = $stmt->execute([':note' => $note, ':id' => $id]);
            
            echo json_encode(['success' => $result, 'message' => 'Note added successfully']);
            exit;
        }
        
        if ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            
            $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);
            
            echo json_encode(['success' => $result, 'message' => 'Contact deleted successfully']);
            exit;
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get contacts with pagination and filtering
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$statusFilter = $_GET['status'] ?? '';
$searchTerm = $_GET['search'] ?? '';
$dateFilter = $_GET['filter'] ?? '';

// Build where clause
$whereClauses = [];
$params = [];

if ($statusFilter) {
    $whereClauses[] = "status = :status";
    $params[':status'] = $statusFilter;
}

if ($searchTerm) {
    $whereClauses[] = "(name LIKE :search OR email LIKE :search OR message LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

if ($dateFilter === 'today') {
    $whereClauses[] = "DATE(submitted_at) = CURDATE()";
} elseif ($dateFilter === 'week') {
    $whereClauses[] = "submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($dateFilter === 'month') {
    $whereClauses[] = "submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

$whereClause = $whereClauses ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $pdo = getDBConnection();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM contact_submissions $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch()['total'];
    
    // Get contacts
    $sql = "SELECT * FROM contact_submissions $whereClause ORDER BY submitted_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $contacts = $stmt->fetchAll();
    
    $totalPages = ceil($totalRecords / $limit);
    
} catch (PDOException $e) {
    $contacts = [];
    $totalRecords = 0;
    $totalPages = 0;
    error_log("Contacts page error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management - Abhiverdhya Industries</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <li><a class="dropdown-item" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contacts.php">
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
                    <h1 class="h2">Contact Submissions</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <select name="filter" class="form-select">
                                    <option value="">All Time</option>
                                    <option value="today" <?php echo $dateFilter === 'today' ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo $dateFilter === 'week' ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo $dateFilter === 'month' ? 'selected' : ''; ?>>This Month</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or message..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="contacts.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Summary -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong><?php echo number_format($totalRecords); ?></strong> contact(s) found
                        <?php if ($statusFilter || $searchTerm || $dateFilter): ?>
                            <small class="text-muted">(filtered)</small>
                        <?php endif; ?>
                    </div>
                    <nav>
                        <small class="text-muted">
                            Page <?php echo $page; ?> of <?php echo max(1, $totalPages); ?>
                        </small>
                    </nav>
                </div>

                <!-- Contacts Table -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th>Product Type</th>
                                        <th>Source</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($contacts)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                No contacts found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($contacts as $contact): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <div><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($contact['email']); ?></div>
                                                        <div><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($contact['phone']); ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo $contact['product_type'] ? ucwords(str_replace('_', ' ', $contact['product_type'])) : 'N/A'; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo ucwords(str_replace('_', ' ', $contact['form_source'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $statusClass = [
                                                        'new' => 'badge bg-warning text-dark',
                                                        'read' => 'badge bg-info',
                                                        'replied' => 'badge bg-success',
                                                        'archived' => 'badge bg-secondary'
                                                    ];
                                                    $class = $statusClass[$contact['status']] ?? 'badge bg-secondary';
                                                    ?>
                                                    <span class="<?php echo $class; ?>" data-contact-id="<?php echo $contact['id']; ?>">
                                                        <?php echo ucfirst($contact['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('M j, Y g:i A', strtotime($contact['submitted_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="viewContact(<?php echo htmlspecialchars(json_encode($contact)); ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteContact(<?php echo $contact['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($_GET); ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($_GET); ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Contact Detail Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Contact details will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function viewContact(contact) {
            const modal = document.getElementById('contactModal');
            const modalBody = modal.querySelector('.modal-body');
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Contact Information</h6>
                        <p><strong>Name:</strong> ${contact.name}</p>
                        <p><strong>Email:</strong> <a href="mailto:${contact.email}">${contact.email}</a></p>
                        <p><strong>Phone:</strong> <a href="tel:${contact.phone}">${contact.phone}</a></p>
                        <p><strong>Product Type:</strong> ${contact.product_type || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Submission Details</h6>
                        <p><strong>Source:</strong> ${contact.form_source.replace('_', ' ')}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(contact.status)}">${contact.status}</span></p>
                        <p><strong>Submitted:</strong> ${new Date(contact.submitted_at).toLocaleString()}</p>
                        <p><strong>Last Updated:</strong> ${new Date(contact.updated_at).toLocaleString()}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Message</h6>
                        <div class="bg-light p-3 rounded">${contact.message}</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Update Status</label>
                        <select class="form-select" id="statusUpdate" onchange="updateStatus(${contact.id}, this.value)">
                            <option value="new" ${contact.status === 'new' ? 'selected' : ''}>New</option>
                            <option value="read" ${contact.status === 'read' ? 'selected' : ''}>Read</option>
                            <option value="replied" ${contact.status === 'replied' ? 'selected' : ''}>Replied</option>
                            <option value="archived" ${contact.status === 'archived' ? 'selected' : ''}>Archived</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add notes about this contact...">${contact.admin_notes || ''}</textarea>
                        <button class="btn btn-sm btn-primary mt-2" onclick="addNote(${contact.id})">Save Note</button>
                    </div>
                </div>
            `;
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Auto-mark as read when modal is closed (only if it's new)
            if (contact.status === 'new') {
                modal.addEventListener('hidden.bs.modal', function() {
                    updateStatus(contact.id, 'read');
                }, { once: true }); // Use 'once: true' to ensure the event listener is only triggered once
            }
        }
        
        function getStatusColor(status) {
            const colors = {
                'new': 'warning',
                'read': 'info',
                'replied': 'success',
                'archived': 'secondary'
            };
            return colors[status] || 'secondary';
        }
        
        function updateStatus(id, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('id', id);
            formData.append('status', status);
            
            fetch('contacts.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
            });
        }
        
        function addNote(id) {
            const note = document.getElementById('adminNotes').value;
            
            const formData = new FormData();
            formData.append('action', 'add_note');
            formData.append('id', id);
            formData.append('note', note);
            
            fetch('contacts.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Note saved successfully!');
                } else {
                    alert('Error saving note: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the note.');
            });
        }
        
        function deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                
                fetch('contacts.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting contact: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the contact.');
                });
            }
        }
        
        // Note: Contacts are now only marked as 'read' when explicitly viewed in detail modal
        // This prevents automatic status changes just from viewing the contacts list
    </script>
</body>
</html>
