<?php
// Standard admin bootstrap: config -> conn -> secure_bootstrap -> require_admin
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Handle actions (server-side fallback when JS is disabled)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('verify_csrf_post')) { verify_csrf_post(); }
    try {
        // Helper to resolve identifier column
        $resolveIdColumn = function (string $id): string {
            $id = trim($id);
            return ctype_digit($id) ? 'submission_id' : 'submission_code';
        };

        if (!empty($_POST['approve_ticket_id'])) {
            $approveId = trim((string)$_POST['approve_ticket_id']);
            $remark = trim((string)($_POST['remark'] ?? ''));
            if (strlen($remark) > 1000) { $remark = substr($remark, 0, 1000); }
            if ($approveId !== '') {
                $col = $resolveIdColumn($approveId);
                $sql = "UPDATE submissions
                        SET status = 'approved',
                            remarks = CASE WHEN :remark <> '' THEN :remark ELSE remarks END,
                            reviewer_id = :rid,
                            reviewed_at = NOW(),
                            status_updated_at = NOW()
                        WHERE $col = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':remark' => $remark,
                    ':rid' => (int)($_SESSION['user_id'] ?? 0),
                    ':id' => ctype_digit($approveId) ? (int)$approveId : $approveId,
                ]);
            }
        } elseif (!empty($_POST['complete_ticket_id'])) {
            $completeId = trim((string)$_POST['complete_ticket_id']);
            $remark = trim((string)($_POST['remark'] ?? ''));
            if (strlen($remark) > 1000) { $remark = substr($remark, 0, 1000); }
            if ($completeId !== '') {
                $col = $resolveIdColumn($completeId);
                $sql = "UPDATE submissions
                        SET status = 'completed',
                            remarks = CASE WHEN :remark <> '' THEN :remark ELSE remarks END,
                            status_updated_at = NOW()
                        WHERE $col = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':remark' => $remark,
                    ':id' => ctype_digit($completeId) ? (int)$completeId : $completeId,
                ]);
            }
        }
    } catch (Throwable $e) {
        if (function_exists('log_event')) { log_event('DB_ERROR', 'Ticket action failed', ['err' => $e->getMessage()]); }
    }
    // Post/Redirect/Get
    redirect('admin/ticket.php');
}

// Fetch admin data
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch() ?: [];
    $admin = [
        'email' => $row['email'] ?? '',
        'username' => ''
    ];
} else { echo "User ID not set in session."; exit(); }

// Search filter
$search_query = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
// Active tab (preserve across filter submits)
$active_tab = isset($_GET['tab']) ? strtolower(trim((string)$_GET['tab'])) : 'pending';
if (!in_array($active_tab, ['pending','approved','completed'], true)) { $active_tab = 'pending'; }
// Has Notes filter
$has_notes = isset($_GET['has_notes']) ? (int)$_GET['has_notes'] : 0;
$where = '';
$params = [];
if ($search_query !== '') {
    // Search by student name, student number, submission code, or title
    $where = " WHERE (
        CONCAT(s.first_name, ' ', COALESCE(s.middle_name,''), ' ', s.last_name) LIKE :q
        OR s.student_number LIKE :q
        OR s.submission_code LIKE :q
        OR s.title LIKE :q
    )";
    $params[':q'] = "%$search_query%";
}
if ($has_notes === 1) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' COALESCE(sn.note_count,0) > 0 ';
}

// Load requests from submissions (ipmo_users.sql)
$rows = [];
try {
    // Ensure meta table exists (lean persistence) so LEFT JOIN never errors on fresh DB
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS submission_incomplete_meta (
            submission_id INT NOT NULL,
            scope ENUM('pending','approved') NOT NULL,
            issue_label VARCHAR(150) DEFAULT NULL,
            admin_comment TEXT NULL,
            affected_doc_types TEXT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (submission_id, scope),
            CONSTRAINT fk_sim_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        // Ensure notes tables exist for joins below
        $pdo->exec("CREATE TABLE IF NOT EXISTS submission_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            submission_id INT NOT NULL,
            user_id INT NOT NULL,
            note TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_sn_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        $pdo->exec("CREATE TABLE IF NOT EXISTS submission_notes_admin_views (
            id INT AUTO_INCREMENT PRIMARY KEY,
            submission_id INT NOT NULL,
            admin_id INT NOT NULL,
            last_viewed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_view (submission_id, admin_id),
            INDEX idx_submission_admin (submission_id, admin_id),
            CONSTRAINT fk_snav_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
            CONSTRAINT fk_snav_admin FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } catch (Throwable $eCreate) {
        if (function_exists('log_event')) { log_event('DB_ERROR', 'Failed ensuring submission_incomplete_meta', ['err' => $eCreate->getMessage()]); }
        // continue; LEFT JOIN will fail only if table truly absent, but we attempted creation.
    }
    $sql = "SELECT 
                s.submission_id,
                s.submission_code AS request_id,
                s.student_number AS student_id,
                CONCAT(s.first_name, ' ', COALESCE(s.middle_name,''), ' ', s.last_name) AS student_name,
                s.academic_level AS user_classification,
                s.program,
                s.created_at AS request_date,
                s.status,
                s.remarks AS remark,
                s.reviewed_at AS approved_timestamp,
                s.status_updated_at AS completed_timestamp,
                COALESCE(sn.note_count,0) AS note_count,
                COALESCE(un.unread_count,0) AS unread_count,
                mp.issue_label   AS pending_issue_label,
                mp.admin_comment AS pending_admin_comment,
                mp.affected_doc_types AS pending_affected_doc_types,
                ma.issue_label   AS approved_issue_label,
                ma.admin_comment AS approved_admin_comment,
                ma.affected_doc_types AS approved_affected_doc_types
            FROM submissions s
            LEFT JOIN (
                SELECT submission_id, COUNT(*) AS note_count, MAX(created_at) AS last_note
                FROM submission_notes
                GROUP BY submission_id
            ) sn ON sn.submission_id = s.submission_id
            LEFT JOIN (
                SELECT n.submission_id,
                       SUM(CASE WHEN v.last_viewed_at IS NULL OR n.created_at > v.last_viewed_at THEN 1 ELSE 0 END) AS unread_count
                FROM submission_notes n
                LEFT JOIN submission_notes_admin_views v
                      ON v.submission_id = n.submission_id AND v.admin_id = :admin_id
                GROUP BY n.submission_id
            ) un ON un.submission_id = s.submission_id
            LEFT JOIN submission_incomplete_meta mp ON mp.submission_id = s.submission_id AND mp.scope = 'pending'
            LEFT JOIN submission_incomplete_meta ma ON ma.submission_id = s.submission_id AND ma.scope = 'approved'
            $where
            ORDER BY s.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $params[':admin_id'] = (int)($_SESSION['user_id'] ?? 0);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Query submissions failed', ['err' => $e->getMessage()]); }
    $rows = [];
}

// Split by status for tabs
$pending = [];
$approved = [];
$completed = [];
foreach ($rows as $r) {
    $st = strtolower((string)($r['status'] ?? ''));
    // Normalize statuses from submissions table
    if ($st === 'pending' || $st === 'pending_review') { $pending[] = $r; }
    elseif ($st === 'approved') { $approved[] = $r; }
    elseif ($st === 'completed') { $completed[] = $r; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/ticket.css">
    <link rel="stylesheet" href="../css/admin-navbar.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
    <title>Manage Requests</title>
    <script src="../javascript/admin-filters.js?v=2" defer></script>
</head>
<body>
<header class="bg-light border-bottom py-3 shadow-sm" data-admin-name="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" data-admin-email="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>">
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
                    <!-- Notes Modal -->
                    <div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title" id="notesModalLabel">User Notes</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="notesContainer">Loading…</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="../javascript/admin-notes.js?v=1" defer></script>
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

    <main class="page-wrapper">
    <div class="container mt-5">
        <h1 class="text-center mb-4" style="font-size:2rem;">Manage Applications</h1>
        <div class="d-flex justify-content-center mb-3">
            <form method="get" class="input-group search-bar" style="max-width:540px; gap:10px;">
                <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                <input type="hidden" name="tab" id="activeTabInput" value="<?php echo htmlspecialchars($active_tab); ?>">
                <div class="form-check form-switch ms-2 d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="hasNotesSwitch" name="has_notes" value="1" <?php echo $has_notes ? 'checked' : ''; ?>>
                    <label class="form-check-label ms-2 small" for="hasNotesSwitch">Has Notes</label>
                </div>
            </form>
        </div>
        <div class="d-flex justify-content-center mb-3 gap-2">
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Ethics Clearance</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Patent</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Industrial Design</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Utility Model</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Trademark</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" type="button">Copyright</button></a>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <ul class="nav nav-tabs" id="requestTabs">
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='pending' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#pending" style="color:#222;">Pending</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='approved' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#approved" style="color:#222;">Approved</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='completed' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#completed" style="color:#222;">Complete</a></li>
            </ul>
            <div></div>
        </div>
        <div class="tab-content mt-3">
            <div class="tab-pane fade <?php echo $active_tab==='pending' ? 'show active' : ''; ?>" id="pending">
                <div class="table-responsive">
                    <table class="table align-middle table-sticky-first">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="request">Request ID</th>
                                <th class="sortable" data-sort="name">Name</th>
                                <th class="sortable" data-sort="user">User</th>
                                <th class="col-date sortable" data-sort="date">Request Date</th>
                                <th>Remarks</th>
                                <th>Notes</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending)): ?>
                                <tr><td colspan="8" class="text-center">No pending requests.</td></tr>
                            <?php else: foreach ($pending as $ticket): ?>
                                <?php
                                    $pIssue = trim((string)($ticket['pending_issue_label'] ?? ''));
                                    $pComment = trim((string)($ticket['pending_admin_comment'] ?? ''));
                                    $pAffected = trim((string)($ticket['pending_affected_doc_types'] ?? ''));
                                    $pendingAttrs = '';
                                    if($pIssue !== '') { $pendingAttrs .= ' data-incomplete-remark="'.htmlspecialchars($pIssue, ENT_QUOTES).'"'; }
                                    if($pComment !== '') { $pendingAttrs .= ' data-admin-comment="'.htmlspecialchars($pComment, ENT_QUOTES).'"'; }
                                    if($pAffected !== '') { $pendingAttrs .= ' data-resubmit-files="'.htmlspecialchars($pAffected, ENT_QUOTES).'"'; }
                                    // Remarks rules for Pending tab:
                                    // - Default: "For Evaluation"
                                    // - If admin marked Incomplete and selected files to resubmit (pAffected non-empty): "Awaiting Review"
                                    // - After user successfully resubmits all requested files (pAffected becomes empty): back to "For Evaluation"
                                    $pendingDisplayRemark = ($pAffected !== '') ? 'Awaiting Review' : 'For Evaluation';
                                ?>
                                <tr<?php echo $pendingAttrs; ?>>
                                    <td><a href="#" class="open-details text-decoration-underline" title="Open details" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>"><?php echo htmlspecialchars($ticket['request_id']); ?></a></td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
                                            <div class="student-subtext text-muted small"><?php echo htmlspecialchars($ticket['student_id']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $userRaw = trim((string)($ticket['user_classification'] ?? ''));
                                            $userLabel = (stripos($userRaw, 'employee') !== false) ? 'Employee' : 'Student';
                                            echo htmlspecialchars($userLabel);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                    <td>
                                        <?php if (strtolower($pendingDisplayRemark)==='awaiting review'): ?>
                                            <span class="status-badge status-awaiting">Awaiting Review</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">For Evaluation</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int)($ticket['note_count'] ?? 0) > 0): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm view-notes position-relative" style="z-index:10;" data-sub-id="<?php echo (int)$ticket['submission_id']; ?>">
                                                View (<?php echo (int)$ticket['note_count']; ?>)
                                                <?php if ((int)($ticket['unread_count'] ?? 0) > 0): ?>
                                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="pointer-events:none;"><span class="visually-hidden">Unread</span></span>
                                                <?php endif; ?>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            
                                            <?php if($pIssue !== '' || $pComment !== '' || $pAffected !== ''): ?>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            <?php endif; ?>
                                            
                                            <form method="post" style="display:inline;">
                                                <?php csrf_input(); ?>
                                                <input type="hidden" name="approve_ticket_id" value="<?php echo htmlspecialchars($ticket['request_id']); ?>">
                                                <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Approve</button>
                                            </form>
                                            <span class="btn btn-incomplete btn-sm rounded-pill px-3">Incomplete</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade <?php echo $active_tab==='approved' ? 'show active' : ''; ?>" id="approved">
                <div class="table-responsive">
                    <table class="table align-middle table-sticky-first">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="request">Request ID</th>
                                <th class="sortable" data-sort="name">Name</th>
                                <th class="sortable" data-sort="user">User</th>
                                <th class="col-date sortable" data-sort="date">Request Date</th>
                                <th class="sortable" data-sort="status">Status</th>
                                <th>Notes</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($approved)): ?>
                                <tr><td colspan="8" class="text-center">No approved requests.</td></tr>
                            <?php else: foreach ($approved as $ticket): ?>
                                <?php
                                    $aIssue = trim((string)($ticket['approved_issue_label'] ?? ''));
                                    $aComment = trim((string)($ticket['approved_admin_comment'] ?? ''));
                                    $aAffected = trim((string)($ticket['approved_affected_doc_types'] ?? ''));
                                    // Status logic for Approved tab:
                                    // - Default: In-Review
                                    // - If admin flagged an issue OR selected files to resubmit (approved scope), show Awaiting Review
                                    $approvedStatusLabel = ($aIssue !== '' || $aAffected !== '') ? 'Awaiting Review' : 'In-Review';
                                    $approvedAttrs = '';
                                    if($aIssue !== '') { $approvedAttrs .= ' data-incomplete-remark="'.htmlspecialchars($aIssue, ENT_QUOTES).'"'; }
                                    if($aComment !== '') { $approvedAttrs .= ' data-admin-comment="'.htmlspecialchars($aComment, ENT_QUOTES).'"'; }
                                    if($aAffected !== '') { $approvedAttrs .= ' data-resubmit-files="'.htmlspecialchars($aAffected, ENT_QUOTES).'"'; }
                                    $showComments = ($aIssue !== '' || $aComment !== '' || $aAffected !== '');
                                ?>
                                <tr<?php echo $approvedAttrs; ?>>
                                    <td><a href="#" class="open-details text-decoration-underline" title="Open details" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>"><?php echo htmlspecialchars($ticket['request_id']); ?></a></td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
                                            <div class="student-subtext text-muted small"><?php echo htmlspecialchars($ticket['student_id']); ?></div>
                                        </div>
                                        </td>
                                        <td>
                                            <?php
                                                $userRaw = trim((string)($ticket['user_classification'] ?? ''));
                                                $userLabel = (stripos($userRaw, 'employee') !== false) ? 'Employee' : 'Student';
                                                echo htmlspecialchars($userLabel);
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                    <td>
                                        <?php if (stripos($approvedStatusLabel,'await')!==false): ?>
                                            <span class="status-badge status-awaiting">Awaiting Review</span>
                                        <?php elseif (stripos($approvedStatusLabel,'review')!==false): ?>
                                            <span class="status-badge status-inreview">In-Review</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inreview">In-Review</span>
                                        <?php endif; ?>
                                    </td>
                                        <td>
                                            <?php if ((int)($ticket['note_count'] ?? 0) > 0): ?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm view-notes position-relative" style="z-index:10;" data-sub-id="<?php echo (int)$ticket['submission_id']; ?>">
                                                    View (<?php echo (int)$ticket['note_count']; ?>)
                                                    <?php if ((int)($ticket['unread_count'] ?? 0) > 0): ?>
                                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="pointer-events:none;"><span class="visually-hidden">Unread</span></span>
                                                    <?php endif; ?>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">None</span>
                                            <?php endif; ?>
                                        </td>
                                    <td>
                                        <div class="action-btn-group">
                                            
                                            <?php if($showComments): ?>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-success btn-sm rounded-pill px-3 me-1 btn-complete" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>">Complete</button>
                                            <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade <?php echo $active_tab==='completed' ? 'show active' : ''; ?>" id="completed">
                <div class="table-responsive">
                    <table class="table align-middle table-sticky-first">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="request">Request ID</th>
                                <th class="sortable" data-sort="name">Name</th>
                                <th class="sortable" data-sort="user">User</th>
                                <th class="col-date sortable" data-sort="date">Request Date</th>
                                <th class="sortable" data-sort="status">Status</th>
                                <th>Notes</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($completed)): ?>
                                <tr><td colspan="8" class="text-center">No completed requests.</td></tr>
                            <?php else: foreach ($completed as $ticket): ?>
                                <tr<?php if(!empty($ticket['approved_admin_comment'])) echo ' data-admin-comment="'.htmlspecialchars($ticket['approved_admin_comment'], ENT_QUOTES).'"'; ?>>
                                    <td><a href="#" class="open-details text-decoration-underline" title="Open details" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>"><span style="font-weight:600;">&nbsp;<?php echo htmlspecialchars($ticket['request_id']); ?></span></a></td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
                                            <div class="student-subtext text-muted small"><?php echo htmlspecialchars($ticket['student_id']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $userRaw = trim((string)($ticket['user_classification'] ?? ''));
                                            $userLabel = (stripos($userRaw, 'employee') !== false) ? 'Employee' : 'Student';
                                            echo htmlspecialchars($userLabel);
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                    <td><span class="status-badge status-completed">Completed</span></td>
                                    <td>
                                        <?php if ((int)($ticket['note_count'] ?? 0) > 0): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm view-notes position-relative" style="z-index:10;" data-sub-id="<?php echo (int)$ticket['submission_id']; ?>">
                                                View (<?php echo (int)$ticket['note_count']; ?>)
                                                <?php if ((int)($ticket['unread_count'] ?? 0) > 0): ?>
                                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="pointer-events:none;"><span class="visually-hidden">Unread</span></span>
                                                <?php endif; ?>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="#" class="btn btn-success btn-sm rounded-pill px-3 btn-view-certificate" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>">View Certificate</a>
                                            
                                            <?php if(!empty($ticket['approved_admin_comment'])): ?>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </main>

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
                    <div id="incompletePendingFilesSection" class="mt-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small mb-0">Select affected document(s)</strong>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="incompletePendingSelectAll">
                                <label class="form-check-label small" for="incompletePendingSelectAll">All</label>
                            </div>
                        </div>
                        <div id="incompletePendingFileList" class="border rounded p-2 small" style="max-height:180px; overflow:auto;">
                            <div class="text-muted fst-italic">Loading documents...</div>
                        </div>
                    </div>
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
                    <div id="incompleteActiveFilesSection" class="mt-3 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small mb-0">Select affected document(s)</strong>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="incompleteActiveSelectAll">
                                <label class="form-check-label small" for="incompleteActiveSelectAll">All</label>
                            </div>
                        </div>
                        <div id="incompleteActiveFileList" class="border rounded p-2 small" style="max-height:180px; overflow:auto;">
                            <div class="text-muted fst-italic">Loading documents...</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-confirm-modal" id="confirmIncompleteActiveBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: File Selection Modal (choose which files to unlock) -->
    <div class="modal fade" id="incompleteFileSelectModal" tabindex="-1" aria-labelledby="incompleteFileSelectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteFileSelectModalLabel">Select Files to Unlock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="incompleteFileListWrapper">
                    <div class="small text-muted mb-2">Select the file(s) that the user will be allowed to re-upload.</div>
                    <div id="incompleteFileList" class="mb-2">
                        <div class="text-center py-3" id="incompleteFileLoading">Loading files...</div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" value="" id="incompleteSelectAll">
                        <label class="form-check-label" for="incompleteSelectAll">Select / Deselect All</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm" id="incompleteFileSelectNextBtn">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Confirmation Modal (save changes) -->
    <div class="modal fade" id="incompleteSaveConfirmModal" tabindex="-1" aria-labelledby="incompleteSaveConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteSaveConfirmModalLabel">Confirm Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">You are about to mark this request as <strong>Incomplete</strong>.</p>
                    <p class="mb-2">The following file(s) will be unlocked for resubmission:</p>
                    <ul id="incompleteSummaryFiles" class="mb-3 small"></ul>
                    <div class="mb-2"><strong>Remarks:</strong> <span id="incompleteSummaryRemark" class="small"></span></div>
                    <div class="mb-2"><strong>Comments:</strong> <span id="incompleteSummaryComment" class="small"></span></div>
                    <div class="alert alert-warning py-2 px-3 small mb-0">Proceed and save these changes?</div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary btn-sm" id="incompleteSaveConfirmBtn">Save Changes</button>
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

<script src="../javascript/admin-ticket.js?v=6" defer></script>
<script src="../javascript/admin-profile.js?v=2" defer></script>
 <script src="../javascript/admin-notifications.js?v=1" defer></script>

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

    <?php include __DIR__ . '/../partials/standard_footer.php'; ?>
</body>
</html>
