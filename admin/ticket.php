<?php
// Standard admin bootstrap: config -> conn -> secure_bootstrap -> require_admin
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Provide a fallback for mb_strtolower if the mbstring extension is not enabled
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string, $encoding = null) {
        return strtolower($string);
    }
}

// Handle actions (server-side fallback when JS is disabled)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('verify_csrf_post')) { verify_csrf_post(); }
    try {
        $redirectTab = null; // decide which tab to show after action
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
                // Resolve admin profile_id for reviewer_id (admin_profiles.profile_id)
                $adminProfileId = null;
                try {
                    $profStmt = $pdo->prepare("SELECT profile_id FROM admin_profiles WHERE user_id = ? ORDER BY profile_id ASC LIMIT 1");
                    if ($profStmt && $profStmt->execute([ (int)($_SESSION['user_id'] ?? 0) ])) {
                        $val = $profStmt->fetchColumn();
                        if ($val !== false) { $adminProfileId = (int)$val; }
                    }
                } catch (Throwable $e) { $adminProfileId = null; }
                $sql = "UPDATE submissions
                        SET status = 'approved',
                            remarks = COALESCE(NULLIF(:remark, ''), remarks),
                            reviewer_id = :rid,
                            reviewed_at = NOW(),
                            status_updated_at = NOW()
                        WHERE $col = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':remark' => $remark,
                    ':rid' => $adminProfileId,
                    ':id' => ctype_digit($approveId) ? (int)$approveId : $approveId,
                ]);
                $redirectTab = 'approved';
            }
        } elseif (!empty($_POST['complete_ticket_id'])) {
            $completeId = trim((string)$_POST['complete_ticket_id']);
            $remark = trim((string)($_POST['remark'] ?? ''));
            if (strlen($remark) > 1000) { $remark = substr($remark, 0, 1000); }
            if ($completeId !== '') {
                $col = $resolveIdColumn($completeId);
                $sql = "UPDATE submissions
                        SET status = 'completed',
                            remarks = COALESCE(NULLIF(:remark, ''), remarks),
                            status_updated_at = NOW()
                        WHERE $col = :id"; // completed_by/at removed from schema
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':remark' => $remark,
                    ':id' => ctype_digit($completeId) ? (int)$completeId : $completeId,
                ]);
                $redirectTab = 'completed';
            }
        }
    } catch (Throwable $e) {
        if (function_exists('log_event')) { log_event('DB_ERROR', 'Ticket action failed', ['err' => $e->getMessage()]); }
    }
    // Post/Redirect/Get
    $qs = $redirectTab ? ('?tab=' . urlencode($redirectTab)) : '';
    redirect('admin/ticket.php' . $qs);
}

// Fetch admin data
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    if (!empty($_SESSION['admin_number'])) {
        $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
            INNER JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? AND ap.admin_number = ? LIMIT 1");
        $stmt->execute([$user_id, $_SESSION['admin_number']]);
    } else {
        $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
            LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? LIMIT 1");
        $stmt->execute([$user_id]);
    }
    $row = $stmt->fetch() ?: [];
    $admin = [
        'email' => $row['email'] ?? '',
        'username' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
    ];
    if (empty($admin['username'])) {
        $admin['username'] = 'Admin'; // Fallback if no name in profile
    }
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
    // Normalize internal whitespace in search term (e.g., multiple spaces) and lowercase
    $normalized = trim(preg_replace('/\s+/', ' ', $search_query));
    $lc = mb_strtolower($normalized, 'UTF-8');
    // Build a hyphen/space-stripped version for ID/number matching
    $stripped = preg_replace('/[\s\-]+/', '', $lc);

    // Prepare helpful expressions - get names from profile tables instead of submissions
    $nameExpr = "LOWER(CONCAT_WS(' ', 
        CASE WHEN u.role = 'student' THEN TRIM(sp.first_name) 
             WHEN u.role = 'employee' THEN TRIM(ep.first_name) 
             ELSE 'Unknown' END,
        CASE WHEN u.role = 'student' THEN NULLIF(TRIM(COALESCE(sp.middle_name,'')),'') 
             WHEN u.role = 'employee' THEN NULLIF(TRIM(COALESCE(ep.middle_name,'')),'') 
             ELSE NULL END,
        CASE WHEN u.role = 'student' THEN TRIM(sp.last_name) 
             WHEN u.role = 'employee' THEN TRIM(ep.last_name) 
             ELSE 'User' END
    ))";

    // Tokenized search for names: require all tokens to be present across first/middle/last (AND semantics)
    $tokens = array_values(array_filter(preg_split('/\s+/', $lc)));
    $tokenClauses = [];
    $i = 0;
    foreach ($tokens as $tok) {
        $key = ":t{$i}";
        // Use only the concatenated name expression to avoid duplicate placeholder usage across columns
        $tokenClauses[] = "( $nameExpr LIKE $key )";
        $params[$key] = '%' . $tok . '%';
        $i++;
    }
    $tokensAnd = !empty($tokenClauses) ? ('(' . implode(' AND ', $tokenClauses) . ')') : '';

    // Search by:
    //  - Name (full and tokenized)
    //  - Student number (case-insensitive; hyphen/space-insensitive)
    //  - Submission code / Request ID (case-insensitive; hyphen/space-insensitive)
    //  - Title (case-insensitive partial)
    $whereParts = [];
    // Use unique placeholders for PDO compatibility when emulation is disabled
    $whereParts[] = "$nameExpr LIKE :q_like1";        $params[':q_like1'] = '%' . $lc . '%';
    $whereParts[] = "CASE WHEN u.role = 'student' THEN LOWER(TRIM(sp.first_name)) 
                           WHEN u.role = 'employee' THEN LOWER(TRIM(ep.first_name)) 
                           ELSE 'unknown' END LIKE :q_like2"; $params[':q_like2'] = '%' . $lc . '%';
    $whereParts[] = "CASE WHEN u.role = 'student' THEN LOWER(TRIM(sp.last_name)) 
                           WHEN u.role = 'employee' THEN LOWER(TRIM(ep.last_name)) 
                           ELSE 'user' END LIKE :q_like3";  $params[':q_like3'] = '%' . $lc . '%';
    if ($tokensAnd !== '') { $whereParts[] = $tokensAnd; }
    $whereParts[] = "REPLACE(LOWER(COALESCE(sp.student_number, ep.employee_number, CONCAT('User-', s.user_id))), '-', '') LIKE :q_strip1";           $params[':q_strip1'] = '%' . $stripped . '%';
    $whereParts[] = "REPLACE(REPLACE(LOWER(s.submission_code), '-', ''), ' ', '') LIKE :q_strip2"; $params[':q_strip2'] = '%' . $stripped . '%';
    $whereParts[] = "LOWER(s.submission_code) LIKE :q_like4";  $params[':q_like4'] = '%' . $lc . '%';
    $whereParts[] = "LOWER(s.title) LIKE :q_like5";            $params[':q_like5'] = '%' . $lc . '%';

    $where = ' WHERE (' . implode("\n        OR ", $whereParts) . ')';
}
if ($has_notes === 1) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' COALESCE(sn.note_count,0) > 0 ';
}

// Load requests from submissions (ipmo_users.sql)
$rows = [];
// Debug helpers
$__dbgMainQueryError = null; $__dbgFilteredCount = null;
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
    $adminId = (int)($_SESSION['user_id'] ?? 0);
    $sql = "SELECT 
                s.submission_id,
                s.submission_code AS request_id,
                CASE 
                    WHEN u.role = 'student' THEN sp.student_number
                    WHEN u.role = 'employee' THEN ep.employee_number  
                    ELSE CONCAT('User-', s.user_id)
                END as student_id,
                CASE 
                    WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
                    WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
                    ELSE 'Unknown User'
                END AS student_name,
                u.role AS user_role,
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
            LEFT JOIN users u ON u.user_id = s.user_id
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
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
                      ON v.submission_id = n.submission_id AND v.admin_id = $adminId
                GROUP BY n.submission_id
            ) un ON un.submission_id = s.submission_id
            LEFT JOIN submission_incomplete_meta mp ON mp.submission_id = s.submission_id AND mp.scope = 'pending'
            LEFT JOIN submission_incomplete_meta ma ON ma.submission_id = s.submission_id AND ma.scope = 'approved'
            $where
            ORDER BY s.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Query submissions failed', ['err' => $e->getMessage()]); }
    $rows = [];
    if (isset($_GET['debug']) && (string)$_GET['debug'] === '1') {
        $__dbgMainQueryError = $e->getMessage();
    }
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
// Debug info: quick DB sanity checks
$__dbgTotalSubs = null; $__dbgSridCount = null; $__dbgEridCount = null; $__dbgSamples = [];
// Extra debug fields to surface effective WHERE and param values
$__dbgWhere = null; $__dbgParams = null; $__dbgProbe = ['code_like'=>null,'code_stripped_like'=>null];
try {
    if (isset($_GET['debug']) && (string)$_GET['debug'] === '1') {
        $__dbgTotalSubs = (int)$pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
        $__dbgSridCount = (int)$pdo->query("SELECT COUNT(*) FROM submissions WHERE submission_code LIKE 'SRID%'")->fetchColumn();
        $__dbgEridCount = (int)$pdo->query("SELECT COUNT(*) FROM submissions WHERE submission_code LIKE 'ERID%'")->fetchColumn();
        $stmtDbg = $pdo->query("SELECT submission_code AS code, status, LOWER(CONCAT_WS(' ', TRIM(first_name), NULLIF(TRIM(COALESCE(middle_name,'')),''), TRIM(last_name))) AS nm
                                 FROM submissions ORDER BY created_at DESC LIMIT 5");
        $__dbgSamples = $stmtDbg ? $stmtDbg->fetchAll() : [];
        // Record the effective WHERE and params used
        $__dbgWhere = $where;
        $__dbgParams = $params;
        // Probe: would the simple code LIKE matches return anything with current search?
        if (!empty($search_query)) {
            $tmpLc = isset($lc) ? $lc : mb_strtolower(trim($search_query), 'UTF-8');
            $tmpStripped = isset($stripped) ? $stripped : preg_replace('/[\s\-]+/', '', $tmpLc);
            try {
                $p1 = $pdo->prepare("SELECT COUNT(*) FROM submissions s WHERE LOWER(s.submission_code) LIKE :q_like");
                $p1->execute([':q_like' => '%'.$tmpLc.'%']);
                $__dbgProbe['code_like'] = (int)$p1->fetchColumn();
            } catch (Throwable $e1) { $__dbgProbe['code_like'] = 'err'; }
            try {
                $p2 = $pdo->prepare("SELECT COUNT(*) FROM submissions s WHERE REPLACE(REPLACE(LOWER(s.submission_code), '-', ''), ' ', '') LIKE :q_stripped_like");
                $p2->execute([':q_stripped_like' => '%'.$tmpStripped.'%']);
                $__dbgProbe['code_stripped_like'] = (int)$p2->fetchColumn();
            } catch (Throwable $e2) { $__dbgProbe['code_stripped_like'] = 'err'; }
        }
        // Also compute filtered count using the exact same WHERE/params but without joins
        try {
            if (!empty($where)) {
                $stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM submissions s $where");
                // Bind only params actually used in $where to avoid HY093
                $usedParams = [];
                foreach ($params as $k => $v) {
                    if (strpos($where, $k) !== false) { $usedParams[$k] = $v; }
                }
                $stmtCnt->execute($usedParams);
                $__dbgFilteredCount = (int)$stmtCnt->fetchColumn();
            } else {
                $__dbgFilteredCount = $__dbgTotalSubs;
            }
        } catch (Throwable $eCnt) {
            $__dbgFilteredCount = 'err';
        }
    }
} catch (Throwable $e) { /* ignore debug failures */ }
// If this is a search and the current tab is empty, auto-pick the first tab with results (server-side)
if ($search_query !== '') {
    $byTab = [
        'pending' => $pending,
        'approved' => $approved,
        'completed' => $completed,
    ];
    if (!isset($byTab[$active_tab]) || empty($byTab[$active_tab])) {
        foreach (['pending','approved','completed'] as $tabName) {
            if (!empty($byTab[$tabName])) { $active_tab = $tabName; break; }
        }
    }
}

// Pagination (10 per page) for each tab
$perPage = 10;
$pending_total   = count($pending);
$approved_total  = count($approved);
$completed_total = count($completed);

// Helper to derive requested page for a given tab (active tab uses ?page=N, others default to 1)
$requested_page = function(string $tab) use ($active_tab): int {
    if ($active_tab === $tab) {
        $p = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        return $p > 0 ? $p : 1;
    }
    return 1;
};

// Compute per-tab pages, clamp, and slice arrays
$pending_pages  = max(1, (int)ceil($pending_total / $perPage));
$approved_pages = max(1, (int)ceil($approved_total / $perPage));
$completed_pages= max(1, (int)ceil($completed_total / $perPage));

$pending_page   = min($pending_pages,  $requested_page('pending'));
$approved_page  = min($approved_pages, $requested_page('approved'));
$completed_page = min($completed_pages,$requested_page('completed'));

$pending_items   = array_slice($pending,   ($pending_page   - 1) * $perPage, $perPage);
$approved_items  = array_slice($approved,  ($approved_page  - 1) * $perPage, $perPage);
$completed_items = array_slice($completed, ($completed_page - 1) * $perPage, $perPage);

// Helper to build pagination URL preserving filters
function build_page_url(string $tab, int $page): string {
    $params = $_GET;
    $params['tab'] = $tab;
    $params['page'] = $page;
    // Normalize has_notes checkbox param (present only when checked)
    if (!empty($params['has_notes'])) { $params['has_notes'] = 1; }
    else { unset($params['has_notes']); }
    $qs = http_build_query($params);
    return htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $qs);
}

function render_pagination_controls(string $tab, int $page, int $pages): void {
    if ($pages <= 1) { return; }
    echo '<nav aria-label="' . htmlspecialchars(ucfirst($tab)) . ' pagination" class="mt-2">';
    echo '<ul class="pagination justify-content-center">';
    // Prev
    $prevDisabled = $page <= 1 ? ' disabled' : '';
    $prevUrl = build_page_url($tab, max(1, $page - 1));
    echo '<li class="page-item' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '">Previous</a></li>';
    // Pages (simple: show all; optimize with ellipsis if large if needed later)
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $page ? ' active' : '';
        $url = build_page_url($tab, $i);
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
    }
    // Next
    $nextDisabled = $page >= $pages ? ' disabled' : '';
    $nextUrl = build_page_url($tab, min($pages, $page + 1));
    echo '<li class="page-item' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '">Next</a></li>';
    echo '</ul>';
    echo '</nav>';
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
    <script src="../javascript/admin-filters.js?v=3" defer></script>
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
                            <input type="text" class="form-control" id="profileAdminName" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="profileAdminEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="profileAdminEmail" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required disabled>
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
        <?php $__debug = isset($_GET['debug']) && (string)$_GET['debug'] === '1'; if ($__debug): ?>
        <div class="mx-auto mb-3" style="max-width:920px;">
            <div class="border rounded p-3" style="background:#fff8e1;border-color:#f0e1a5;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Debug: Search Diagnostics</strong>
                    <a class="small text-decoration-underline" href="<?php echo htmlspecialchars(preg_replace('/([&?])debug=1(&|$)/','${1}', $_SERVER['REQUEST_URI'])); ?>">hide</a>
                </div>
                <pre class="mb-2" style="white-space:pre-wrap; font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace; font-size:12px; line-height:1.35;">
GET params: <?php echo htmlspecialchars(json_encode([
    'search' => $search_query,
    'tab' => $active_tab,
    'has_notes' => $has_notes,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); ?>
Normalized: <?php echo htmlspecialchars(json_encode([
    'lower' => isset($lc) ? $lc : null,
    'stripped' => isset($stripped) ? $stripped : null,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); ?>
SQL where: <?php echo htmlspecialchars((string)$__dbgWhere); ?>
Params: <?php echo htmlspecialchars(json_encode($__dbgParams, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); ?>
Counts: <?php echo htmlspecialchars(json_encode([
    'pending' => isset($pending) ? count($pending) : 0,
    'approved' => isset($approved) ? count($approved) : 0,
    'completed' => isset($completed) ? count($completed) : 0,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); ?>
Active tab (server): <?php echo htmlspecialchars($active_tab); ?>
Main query error: <?php echo htmlspecialchars((string)($__dbgMainQueryError ?? '')); ?>
                </pre>
                <div class="small text-muted">Runtime file info:</div>
                <pre class="mb-2" style="white-space:pre-wrap; font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace; font-size:12px; line-height:1.35;">
File path: <?php echo htmlspecialchars(__FILE__); ?>
File mtime: <?php $mt = @filemtime(__FILE__); echo $mt ? htmlspecialchars(date('c', $mt)) : 'n/a'; ?>
                </pre>
                <div class="small text-muted">DB quick-checks:</div>
                <pre class="mb-0" style="white-space:pre-wrap; font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace; font-size:12px; line-height:1.35;">
Total submissions: <?php echo htmlspecialchars((string)($__dbgTotalSubs ?? 'n/a')); ?>
SRID count: <?php echo htmlspecialchars((string)($__dbgSridCount ?? 'n/a')); ?>, ERID count: <?php echo htmlspecialchars((string)($__dbgEridCount ?? 'n/a')); ?>
Probe: code_like=<?php echo htmlspecialchars((string)($__dbgProbe['code_like'] ?? 'n/a')); ?>, code_stripped_like=<?php echo htmlspecialchars((string)($__dbgProbe['code_stripped_like'] ?? 'n/a')); ?>
Filtered count (WHERE only): <?php echo htmlspecialchars((string)($__dbgFilteredCount ?? 'n/a')); ?>
Samples: <?php echo htmlspecialchars(json_encode($__dbgSamples, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); ?>
                </pre>
            </div>
        </div>
        <?php endif; ?>
        <div class="d-flex justify-content-center mb-3">
            <form id="ticketSearchForm" method="get" class="input-group search-bar" style="max-width:540px; gap:10px;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search by Request ID, Name, Student Number, or Title" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                <input type="hidden" name="tab" id="activeTabInput" value="<?php echo htmlspecialchars($active_tab); ?>">
                <button id="ticketSearchBtn" class="btn btn-outline-secondary rounded-pill ms-2 px-3" type="submit">Search</button>
                <div class="form-check form-switch ms-2 d-flex align-items-center" style="white-space:nowrap;">
                    <input class="form-check-input" type="checkbox" id="hasNotesSwitch" name="has_notes" value="1" <?php echo $has_notes ? 'checked' : ''; ?>>
                    <label class="form-check-label ms-2 small" for="hasNotesSwitch">Has Notes</label>
                </div>
                <!-- Hidden submit ensures Enter key submits even with multiple inputs in the group -->
                <button type="submit" class="visually-hidden" aria-hidden="true" tabindex="-1">Search</button>
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
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='pending' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#pending" style="color:#222;">Pending<?php if($search_query!==''){ echo ' ('.count($pending).')'; } ?></a></li>
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='approved' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#approved" style="color:#222;">Approved<?php if($search_query!==''){ echo ' ('.count($approved).')'; } ?></a></li>
                <li class="nav-item"><a class="nav-link fw-semibold <?php echo $active_tab==='completed' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#completed" style="color:#222;">Complete<?php if($search_query!==''){ echo ' ('.count($completed).')'; } ?></a></li>
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
                            <?php if (empty($pending_items)): ?>
                                <tr><td colspan="8" class="text-center">No pending requests.</td></tr>
                            <?php else: foreach ($pending_items as $ticket): ?>
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
                                            $role = strtolower(trim((string)($ticket['user_role'] ?? 'student')));
                                            echo htmlspecialchars($role === 'employee' ? 'Employee' : 'Student');
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
                <?php render_pagination_controls('pending', $pending_page, $pending_pages); ?>
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
                            <?php if (empty($approved_items)): ?>
                                <tr><td colspan="8" class="text-center">No approved requests.</td></tr>
                            <?php else: foreach ($approved_items as $ticket): ?>
                                <?php
                                    // Determine status label purely from approved-scope flags
                                    $aIssue    = trim((string)($ticket['approved_issue_label'] ?? ''));
                                    $aAffected = trim((string)($ticket['approved_affected_doc_types'] ?? ''));
                                    $approvedStatusLabel = ($aIssue !== '' || $aAffected !== '') ? 'Awaiting Review' : 'In-Review';

                                    // Approved tab Comments should reflect ONLY the admin comment captured when marking Incomplete in Approved scope
                                    // or the approval-time comment (stored in approved meta), NOT the generic submissions.remarks field.
                                    $approveComment = trim((string)($ticket['approved_admin_comment'] ?? ''));
                                    $approvedAttrs = '';
                                    if ($approveComment !== '') {
                                        $approvedAttrs .= ' data-admin-comment="'.htmlspecialchars($approveComment, ENT_QUOTES).'"';
                                    }
                                    // Do NOT attach data-incomplete-remark or data-resubmit-files here to avoid surfacing Pending meta.
                                    $showComments = ($approveComment !== '');
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
                                                $role = strtolower(trim((string)($ticket['user_role'] ?? 'student')));
                                                echo htmlspecialchars($role === 'employee' ? 'Employee' : 'Student');
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
                <?php render_pagination_controls('approved', $approved_page, $approved_pages); ?>
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
                            <?php if (empty($completed_items)): ?>
                                <tr><td colspan="8" class="text-center">No completed requests.</td></tr>
                            <?php else: foreach ($completed_items as $ticket): ?>
                                <?php
                                    // Completed tab: show Comments button ONLY if a comment was provided
                                    // in the Complete action (completion-time remark stored in submissions.remarks).
                                    $completionRemark = trim((string)($ticket['remark'] ?? ''));
                                    $completedAttrs = '';
                                    if ($completionRemark !== '') {
                                        $completedAttrs .= ' data-admin-comment="'.htmlspecialchars($completionRemark, ENT_QUOTES).'"';
                                    }
                                    // Do not surface issue/affected meta in Completed tab per requirement.
                                    $showCompletedComments = ($completionRemark !== '');
                                ?>
                                <tr<?php echo $completedAttrs; ?>>
                                    <td><a href="#" class="open-details text-decoration-underline" title="Open details" data-request-id="<?php echo htmlspecialchars($ticket['request_id']); ?>"><span style="font-weight:600;">&nbsp;<?php echo htmlspecialchars($ticket['request_id']); ?></span></a></td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['student_name']); ?></span>
                                            <div class="student-subtext text-muted small"><?php echo htmlspecialchars($ticket['student_id']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $role = strtolower(trim((string)($ticket['user_role'] ?? 'student')));
                                            echo htmlspecialchars($role === 'employee' ? 'Employee' : 'Student');
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
                                            <?php if($showCompletedComments): ?>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php render_pagination_controls('completed', $completed_page, $completed_pages); ?>
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

<script src="../javascript/admin-ticket.js?v=13" defer></script>
<script src="../javascript/admin-profile.js?v=5" defer></script>
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
