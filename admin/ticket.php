<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();

function table_exists(mysqli $conn, string $table): bool {
    if (!$conn) return false;
    if ($stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1")) {
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = (bool)$res->fetch_row();
        $stmt->close();
        return $exists;
    }
    return false;
}

// Check if a specific column exists in a given table
function column_exists(mysqli $conn, string $table, string $column): bool {
    if ($stmt = $conn->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1")) {
        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = (bool)$res->fetch_row();
        $stmt->close();
        return $exists;
    }
    return false;
}

// Build a safe SELECT clause that aliases missing columns to NULL, so queries won't fail
function build_safe_select_clause(mysqli $conn, string $table, array $columns): string {
    $parts = [];
    foreach ($columns as $col) {
        if (column_exists($conn, $table, $col)) {
            // Use backticks to avoid reserved word conflicts
            $parts[] = "`$col`";
        } else {
            // Alias a literal NULL to the expected column name
            $parts[] = "NULL AS `$col`";
        }
    }
    return implode(', ', $parts);
}

$requests_table_exists = table_exists($conn, 'rental_requests');
$search_query = "";
if ($requests_table_exists) {
    // Columns we expect to display; missing ones will be aliased
    $expected_columns = [
        'request_id', 'student_id', 'student_name', 'user_classification',
        'program', 'request_date', 'status', 'remark',
        'approved_timestamp', 'completed_timestamp'
    ];
    $select_clause = build_safe_select_clause($conn, 'rental_requests', $expected_columns);

    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        $query_all = "SELECT $select_clause FROM `rental_requests` WHERE `student_name` LIKE ?";
        if ($stmt_search = $conn->prepare($query_all)) {
            $search_param = "%" . $search_query . "%";
            $stmt_search->bind_param("s", $search_param);
            if ($stmt_search->execute()) {
                $result_all = $stmt_search->get_result();
            } else {
                log_event('DB_ERROR', 'Ticket search execute failed', ['error' => $stmt_search->error]);
            }
        } else {
            log_event('DB_ERROR', 'Ticket search prepare failed', ['error' => $conn->error]);
        }
    } else {
        $query_all = "SELECT $select_clause FROM `rental_requests`";
        $result_all = $conn->query($query_all);
        if (!$result_all) { log_event('DB_ERROR', 'Ticket list query failed', ['error' => $conn->error]); }
    }
    if (!isset($result_all) || !$result_all) {
        echo "An error occurred loading requests.";
        $result_all = new class { public function fetch_assoc(){ return null; } public function data_seek($n){} };
    }
} else {
    log_event('DB_WARN', 'rental_requests table missing in ticket.php');
    $result_all = new class { public function fetch_assoc(){ return null; } public function data_seek($n){} };
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if (!$admin) { echo "Admin not found."; exit(); }
} else { echo "User ID not set in session."; exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/ticket.css?v=5">
    <link rel="stylesheet" href="../css/admin-navbar.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
    <title>Manage Requests</title>
</head>
<body>
     <header class="bg-light border-bottom py-3 shadow-sm" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>" data-admin-email="<?php echo htmlspecialchars($admin['email']); ?>">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="<?php echo asset_url('images/puplogo.png'); ?>" alt="Logo" class="center-img" style="height: 30px; margin-right: 10px;">
                        <span class="fw-bold">PUP e-IPMO [Admin.]</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center w-100">
                            <li class="nav-item ms-auto"><a class="nav-link" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" aria-current="page" href="ticket.php">Applications</a></li>
                            <li class="nav-item d-flex align-items-center header-actions ms-lg-3 mt-2 mt-lg-0">
                                <button type="button" class="btn btn-outline-secondary btn-profile" data-bs-toggle="modal" data-bs-target="#adminProfileModal">My Profile</button>
                                <form method="POST" action="../logout.php" class="d-inline ms-2">
                                    <?php csrf_input(); ?>
                                    <button type="submit" class="btn btn-logout btn-logout-nav">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminProfileLabel">My Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="adminProfileBody">
                    <div class="border rounded p-3 mb-4 bg-light-subtle" style="border-color:#ddd!important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">Account Information</h6>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" id="profileEditBtn">Edit</button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="profileCancelBtn">Cancel</button>
                            </div>
                        </div>
                        <form id="profileInfoForm">
                            <div class="mb-3">
                                <label for="profileAdminName" class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control" id="profileAdminName" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="profileAdminEmail" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="profileAdminEmail" required disabled>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary d-none" id="profileSaveBtn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Change Password</h6>
                    <form id="profileChangePasswordForm">
                        <div class="mb-3">
                            <label for="profileCurrentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="profileCurrentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="profileNewPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="profileNewPassword" minlength="8" required>
                            <div class="form-text">At least 8 characters.</div>
                        </div>
                        <div class="mb-2">
                            <label for="profileConfirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="profileConfirmPassword" minlength="8" required>
                        </div>
                        <div id="profileChangePassAlert" class="alert d-none mt-3" role="alert"></div>
                        <div class="modal-footer px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="font-size:2rem;">Manage Applications</h1>
        <div class="d-flex justify-content-center mb-3">
            <div class="input-group search-bar" style="max-width:400px;">
                <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                <span class="input-group-text bg-white border-0" style="border-radius: 50px; margin-left:-40px;">
                    <i class="bi bi-search"></i>
                </span>
            </div>
        </div>
        <div class="d-flex justify-content-center mb-3 gap-2">
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Ethics Clearance</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Patent</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Industrial Design</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Utility Model</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Trademark</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Copyright</button></a>
        </div>
        <ul class="nav nav-tabs mb-3" id="requestTabs">
            <li class="nav-item"><a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#pending" style="color:#222;">Pending</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold" data-bs-toggle="tab" href="#approved" style="color:#222;">Approved</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold" data-bs-toggle="tab" href="#completed" style="color:#222;">Complete</a></li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="pending">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Remarks</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SRID-2025-0808-001</td>
                                <td>2022-08960-MN-0</td>
                                <td>Dela Cruz, Juan P.</td>
                                <td>Student</td>
                                <td>CCIS</td>
                                <td>2025-08-08</td>
                                <td>For Evaluation</td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                        <form method="post" style="display:inline;">
                                            <?php csrf_input(); ?>
                                            <input type="hidden" name="approve_ticket_id" value="SRID-2025-0808-001">
                                            <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Approve</button>
                                        </form>
                                        <span class="btn btn-incomplete btn-sm rounded-pill px-3">Incomplete</span>
                                    </div>
                                </td>
                            </tr>
                            <?php while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Pending'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket['request_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['user_classification']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['program']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['remark'] ?? 'For Evaluation'); ?></td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                                <form method="post" style="display:inline;">
                                                    <?php csrf_input(); ?>
                                                    <input type="hidden" name="approve_ticket_id" value="<?php echo htmlspecialchars($ticket['request_id']); ?>">
                                                    <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Approve</button>
                                                </form>
                                                <span class="btn btn-incomplete btn-sm rounded-pill px-3">Incomplete</span>
                                            </div>
                                        </td>    
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="approved">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SRID-2025-0808-002</td>
                                <td>2022-08961-MN-0</td>
                                <td>Reyes, Maria L.</td>
                                <td>Student</td>
                                <td>COE</td>
                                <td>2025-08-09</td>
                                <td>In-review</td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                        <form method="post" style="display:inline;">
                                            <?php csrf_input(); ?>
                                            <input type="hidden" name="approve_ticket_id" value="SRID-2025-0808-002">
                                            <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Complete</button>
                                        </form>
                                        <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                                    </div>
                                </td>
                            </tr>
                            <?php $result_all->data_seek(0); while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Approved'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket['request_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['user_classification']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['program']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                        <td>In-review</td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                                <button class="btn btn-success btn-sm rounded-pill px-3 me-1 btn-complete" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>">Complete</button>
                                                <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="completed">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $result_all->data_seek(0); while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Completed'): ?>
                                    <tr>
                                        <td><span style="font-weight:600;">SRID-2025-0808-001</span></td>
                                        <td>2022-08960-MN-0</td>
                                        <td>Dela Cruz, Juan P.</td>
                                        <td>Student</td>
                                        <td>CCIS</td>
                                        <td>2025-08-08</td>
                                        <td>Completed</td>
                                        <td>Complete</td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-success btn-sm rounded-pill px-3 btn-view-certificate" data-cert-url="#">View Certificate</a>
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">Certificate of Copyright Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-certificate" data-bs-dismiss="modal">Close</button>
                    <a id="downloadCertificateBtn" href="#" class="btn btn-download-pdf" download>Download as PDF</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <?php csrf_input(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeModalLabel">Complete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="complete_comments" class="form-label mb-2">Comments:</label>
                        <textarea class="form-control" id="complete_comments" name="remark" rows="7" placeholder="Add your Comments here..." style="resize:none;"></textarea>
                        <input type="hidden" name="complete_ticket_id" id="complete_ticket_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background:#6c757d;">Close</button>
                        <button type="submit" class="btn" style="background:#7c3aed; color:#fff;">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="incompleteModal" tabindex="-1" aria-labelledby="incompleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteModalLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-remarks-modal dropdown-toggle" type="button" id="remarksDropdown" data-bs-toggle="dropdown" aria-expanded="false">Remarks</button>
                            <ul class="dropdown-menu" aria-labelledby="remarksDropdown">
                                <li><a class="dropdown-item remark-choice" href="#" data-remark="Incorrect Document/Upload">Incorrect Document/Upload</a></li>
                                <li><a class="dropdown-item remark-choice" href="#" data-remark="Error in Document/Upload">Error in Document/Upload</a></li>
                            </ul>
                        </div>
                        <br><span><strong>Comments:</strong></span>
                    </div>
                    <textarea id="incompleteTextarea" rows="7" class="form-control mb-2" style="resize:none; border:1px solid #555;"></textarea>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmIncompleteBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="incompleteActiveModal" tabindex="-1" aria-labelledby="incompleteActiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteActiveModalLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-remarks-modal dropdown-toggle" type="button" id="remarksDropdownActive" data-bs-toggle="dropdown" aria-expanded="false">Remarks</button>
                            <ul class="dropdown-menu" aria-labelledby="remarksDropdownActive">
                                <li><a class="dropdown-item remark-choice-active" href="#" data-remark="Missing Document">Missing Document</a></li>
                                <li><a class="dropdown-item remark-choice-active" href="#" data-remark="Error in Document">Error in Document</a></li>
                                <li><a class="dropdown-item remark-choice-active" href="#" data-remark="Documents don’t match">Documents don’t match</a></li>
                            </ul>
                        </div>
                        <span><strong>Comments:</strong></span>
                    </div>
                    <textarea id="incompleteTextareaActive" rows="7" class="form-control mb-2" style="resize:none; border:1px solid #555;"></textarea>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-confirm-modal" id="confirmIncompleteActiveBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content details-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title fw-bold mb-0" id="detailsModalLabel">Request Details</h5>
                        <button type="button" class="btn btn-sm btn-edit-modal" id="editDetailsBtn">Edit</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="m-0 mb-3">
                <div class="modal-body" id="detailsModalBody"></div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-save-modal" id="saveDetailsBtn" style="display:none;">Save</button>
                </div>
            </div>
        </div>
    </div>

<script src="../javascript/admin-ticket.js?v=2" defer></script>
<script src="../javascript/admin-profile.js?v=2" defer></script>

<div class="modal fade" id="authorInfoModal" tabindex="-1" aria-labelledby="authorInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content details-modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                <h5 class="modal-title fw-bold mb-0" id="authorInfoModalLabel">Author’s Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="authorInfoBody"></div>
            <div class="modal-footer border-0 pt-3">
                <button type="button" class="btn btn-edit-modal" id="authorEditBtn">EDIT</button>
                <button type="button" class="btn btn-save-modal" id="authorSaveBtn" style="display:none;">SAVE</button>
            </div>  
        </div>
    </div>
</div>

<div class="modal fade" id="approveCommentModal" tabindex="-1" aria-labelledby="approveCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveCommentModalLabel">Approve Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="approveRequestId" />
                <label for="approveCommentText" class="form-label">Comment (optional)</label>
                <textarea class="form-control" id="approveCommentText" rows="5" placeholder="Enter approval comment..." style="resize:none;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="approveCommentConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-success fw-bold mb-0">The request has been approved.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="comment_text" class="form-label">Comments:</label>
                <textarea readonly class="form-control" id="comment_text" rows="5" placeholder="Please reupload your documents" style="resize:none;"></textarea>
            </div>
        </div>
    </div>
</div>
  <!-- Footer -->
 <footer class="bg-white border-top py-3">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>
</body>
</html>
