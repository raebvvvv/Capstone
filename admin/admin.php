<?php
// Admin dashboard (moved under /admin)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Helper: Check if table exists (returns bool)
function table_exists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
    $stmt->execute([$table]);
    return (bool)$stmt->fetchColumn();
}

// Prefer cached dashboard summary to avoid heavy live queries
$useSummary = false;
$summary_last_updated = '';
$summary_last_updated_iso = '';
$total_users = 0; $total_applications = 0; $pending_applications = 0; $approved_applications = 0; $completed_applications = 0;
$undergrad = 0; $grad = 0; $open = 0; $not_studying = 0; $total_applications_chart = 0;
$collegeLabels = []; $collegeValues = [];
$campusLabels = []; $campusValues = [];
$wcLabels = []; $wcValues = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'dashboard_summary' LIMIT 1");
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $row = $pdo->query("SELECT * FROM dashboard_summary WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $useSummary = true;
            $summary_last_updated = (string)$row['last_updated'];
            try {
                if ($summary_last_updated !== '') {
                    $dt = new DateTime($summary_last_updated, new DateTimeZone('Asia/Manila'));
                    $summary_last_updated_iso = $dt->format(DateTime::ATOM);
                }
            } catch (Throwable $e) { $summary_last_updated_iso = ''; }
            $total_users = (int)$row['total_users'];
            $total_applications = (int)$row['total_apps'];
            $pending_applications = (int)$row['pending_apps'];
            $approved_applications = (int)$row['approved_apps'];
            $completed_applications = (int)$row['completed_apps'];
            $undergrad = (int)$row['overview_undergrad'];
            $grad = (int)$row['overview_grad'];
            $open = (int)$row['overview_open'];
            // Total for chart will be recalculated after we compute Not Studying (which isn't cached yet)
            $total_applications_chart = $undergrad + $grad + $open;
            $bc = json_decode($row['by_college_json'] ?? '{}', true) ?: ['labels'=>[], 'values'=>[]];
            $collegeLabels = $bc['labels']; $collegeValues = $bc['values'];
            // Normalize cached labels to abbreviations (e.g., "College of Science (CS)" -> "CS")
            if (is_array($collegeLabels) && is_array($collegeValues)) {
                [$collegeLabels, $collegeValues] = collapse_college_series_to_codes($collegeLabels, $collegeValues);
            }
            $bp = json_decode($row['by_campus_json'] ?? '{}', true) ?: ['labels'=>[], 'values'=>[]];
            $campusLabels = $bp['labels']; $campusValues = $bp['values'];
            $wcj = json_decode($row['work_class_json'] ?? '{}', true) ?: ['labels'=>[], 'values'=>[]];
            $wcLabels = $wcj['labels']; $wcValues = $wcj['values'];
        }
    }
} catch (Throwable $e) { /* ignore, fallback to live */ }

// Helper: Secure count query with graceful fallback if table dropped
function getCount($pdo, $table, $where = '', $params = []) {
    if (!table_exists($pdo, $table)) {
        if (function_exists('log_event')) { log_event('DB_WARN', 'Table missing for count', ['table' => $table]); }
        return 0;
    }
    $sql = "SELECT COUNT(*) as count FROM `$table`";
    if ($where) { $sql .= " WHERE $where"; }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ? (int)$row['count'] : 0;
}

// Dashboard counts (live only when no cached summary)
if (!$useSummary) {
    $total_users = getCount($pdo, 'users', "role IN ('student','employee') AND status = ?", ['active']);
    $total_applications = getCount($pdo, 'submissions');
    // Pending should include both brand-new (status='pending') and legacy/alternate flag (status='pending_review')
    $pending_applications = getCount($pdo, 'submissions', "status IN ('pending','pending_review')");
    $approved_applications = getCount($pdo, 'submissions', "status = ?", ['approved']);
    $completed_applications = getCount($pdo, 'submissions', "status = ?", ['completed']);
}

// Overview counts (live only when no cached summary)
if (!$useSummary) {
    // Robust classification using case-insensitive patterns
    $undergrad = getCount(
        $pdo,
        'submissions',
        "(LOWER(academic_level) LIKE ? OR LOWER(academic_level) = ? OR LOWER(academic_level) LIKE ?)",
        ['undergrad%', 'undergraduate', 'bachelor%']
    );
    $grad = getCount(
        $pdo,
        'submissions',
        "(LOWER(academic_level) LIKE ? OR LOWER(academic_level) LIKE ? OR LOWER(academic_level) LIKE ? OR LOWER(academic_level) LIKE ? OR LOWER(academic_level) LIKE ? OR LOWER(academic_level) LIKE ?)",
        ['master%', '%doctor%', '%doctoral%', '%doctorate%', '%phd%', '%postgrad%']
    );
    $open = getCount(
        $pdo,
        'submissions',
        "LOWER(academic_level) LIKE ?",
        ['%open%']
    );
    // Will include Not Studying after we compute it below
}

// Not Studying count (robust: recognize blank/N-A levels paired with N/A program or 'not stud' text)
$not_studying = getCount(
    $pdo,
    'submissions',
    "(TRIM(LOWER(COALESCE(academic_level,''))) IN ('not studying','n/a','na','-','') OR LOWER(academic_level) LIKE ?)
      AND (TRIM(LOWER(COALESCE(program,''))) IN ('n/a','na','') OR LOWER(program) LIKE ?)",
    ['%not stud%', '%not stud%']
);
// Recompute Undergrad to be disjoint: total - grad - open - not_studying
try {
    $total_all_subs = $total_applications ?: (int)$pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
} catch (Throwable $e) { $total_all_subs = (int)$total_applications; }
$undergrad = max(0, (int)$total_all_subs - (int)$grad - (int)$open - (int)$not_studying);
// Total applications for chart equals total submissions
$total_applications_chart = (int)$total_all_subs;

// Build real datasets for charts
// Optional filters: status and date range (created_at)
$statusParam = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
if (!in_array($statusParam, ['all','pending','approved','completed','pending_review'], true)) { $statusParam = 'all'; }
$startParam = isset($_GET['start']) ? trim($_GET['start']) : '';
$endParam   = isset($_GET['end']) ? trim($_GET['end']) : '';
$hasStart = preg_match('/^\d{4}-\d{2}-\d{2}$/', $startParam) === 1;
$hasEnd   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $endParam) === 1;

// Completed Applications-style filters (optional): college code, program, campus, type, group
$collegeParam = isset($_GET['college']) ? trim($_GET['college']) : 'All';
$programParam = isset($_GET['program']) ? trim($_GET['program']) : 'All';
$campusParam  = isset($_GET['campus'])  ? trim($_GET['campus'])  : 'All';
$typeParam    = isset($_GET['type'])    ? trim($_GET['type'])    : 'All';
$groupParam   = isset($_GET['group'])   ? trim($_GET['group'])   : 'All';

// WHERE builder
function build_where(array &$params, string $statusParam, bool $hasStart, string $startParam, bool $hasEnd, string $endParam): string {
    $clauses = [];
    if ($statusParam !== 'all') {
        if ($statusParam === 'pending') { $clauses[] = "status IN ('pending','pending_review')"; }
        else { $clauses[] = 'status = ?'; $params[] = $statusParam; }
    }
    if ($hasStart && $hasEnd) { $clauses[] = 'DATE(created_at) BETWEEN ? AND ?'; $params[] = $startParam; $params[] = $endParam; }
    elseif ($hasStart) { $clauses[] = 'DATE(created_at) >= ?'; $params[] = $startParam; }
    elseif ($hasEnd) { $clauses[] = 'DATE(created_at) <= ?'; $params[] = $endParam; }
    return $clauses ? (' WHERE '.implode(' AND ', $clauses)) : '';
}
function normalize_college_label(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return 'Unknown';
    // Prefer code before " - " if present (e.g., "CCIS - College of Computer and Information Sciences")
    if (strpos($raw, ' - ') !== false) {
        return trim(substr($raw, 0, strpos($raw, ' - ')));
    }
    // If acronym in parentheses at end, use that (e.g., "College of ... (CAF)")
    if (preg_match('/\(([^)]+)\)\s*$/', $raw, $m)) {
        return strtoupper(trim($m[1]));
    }
    return $raw;
}

// Turn an arbitrary college label into a short code (e.g., "College of Science (CS)" -> "CS").
function abbreviate_college_label(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') return 'Other';
    // Explicit mapping for Institute of Technology
    if (stripos($raw, 'institute of technology') !== false) {
        return 'ITech';
    }
    // Prefer prefix code before " - " (e.g., "CCIS - College of ...")
    if (strpos($raw, ' - ') !== false) {
        $code = trim(substr($raw, 0, strpos($raw, ' - ')));
        if ($code !== '') return strtoupper($code);
    }
    // Prefer code in parentheses at the end (e.g., "College of ... (CAF)")
    if (preg_match('/\(([^)]+)\)\s*$/', $raw, $m)) {
        $code = strtoupper(trim($m[1]));
        if ($code !== '') return $code;
    }
    // If the whole string already looks like a short uppercase code, keep it
    if (strlen($raw) <= 7 && strtoupper($raw) === $raw) {
        return strtoupper($raw);
    }
    // Derive an acronym from significant words
    $words = preg_split('/\s+/', $raw);
    $stop = ['of','and','the','in','for','college','school','institute','faculty'];
    $abbr = '';
    foreach ($words as $w) {
        $lw = strtolower(trim($w));
        if ($lw === '' || in_array($lw, $stop, true)) continue;
        $abbr .= strtoupper($w[0] ?? '');
    }
    return $abbr !== '' ? $abbr : 'Other';
}

// Collapse an existing [labels, values] series to use codes-only labels, merging duplicates.
function collapse_college_series_to_codes(array $labels, array $values): array {
    $agg = [];
    $order = [];
    foreach ($labels as $i => $label) {
        $code = abbreviate_college_label((string)$label);
        $val = isset($values[$i]) ? (int)$values[$i] : 0;
        if (!array_key_exists($code, $agg)) { $agg[$code] = 0; $order[] = $code; }
        $agg[$code] += $val;
    }
    $outLabels = [];
    $outValues = [];
    foreach ($order as $code) { $outLabels[] = $code; $outValues[] = (int)$agg[$code]; }
    return [$outLabels, $outValues];
}

function level_label(string $raw): string {
    $t = strtolower(trim($raw));
    if ($t === '') return 'Undergraduate';
    // Explicit Open University bucket
    if (strpos($t, 'open') !== false) return 'Open University';
    // Ensure 'undergraduate' isn't misclassified as 'graduate'
    if (strpos($t, 'undergrad') !== false || $t === 'undergraduate' || $t === 'ug' || strpos($t, 'bachelor') === 0) {
        return 'Undergraduate';
    }
    // Masters/Doctorate/PhD/Postgrad => Graduate School
    if (
        strpos($t, 'graduate school') !== false ||
        strpos($t, 'master') !== false ||
        strpos($t, 'doctor') !== false ||
        strpos($t, 'doctoral') !== false ||
        strpos($t, 'doctorate') !== false ||
        strpos($t, 'phd') !== false ||
        strpos($t, 'ph.d') !== false ||
        strpos($t, 'postgrad') !== false
    ) {
        return 'Graduate School';
    }
    return 'Undergraduate';
}

// Parse college string into [code, full] where possible
function parse_college(string $raw): array {
    $raw = trim($raw);
    if ($raw === '') return ['', ''];
    // Case 1: "CCIS - College of ..."
    if (strpos($raw, ' - ') !== false) {
        $code = trim(substr($raw, 0, strpos($raw, ' - ')));
        $full = trim(substr($raw, strpos($raw, ' - ') + 3));
        return [$code, $full];
    }
    // Case 2: "College of ... (CCIS)"
    if (preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $raw, $m)) {
        $full = trim($m[1]);
        $code = strtoupper(trim($m[2]));
        return [$code, $full];
    }
    // Case 3: already a code or just a name
    // Heuristic: short (<=6) and uppercase -> code
    if (strlen($raw) <= 6 && strtoupper($raw) === $raw) {
        return [$raw, ''];
    }
    return ['', $raw];
}

// Helper to fold tail into "Others" for scalability
function fold_others(array $labels, array $values, int $topN = 10): array {
    $n = count($labels);
    if ($n <= $topN) return [$labels, $values];
    $topLabels = array_slice($labels, 0, $topN);
    $topValues = array_slice($values, 0, $topN);
    $others = array_sum(array_slice($values, $topN));
    $topLabels[] = 'Others';
    $topValues[] = $others;
    return [$topLabels, $topValues];
}

// Refresh Academic level donut with filters
try {
    $p = [];
    $where = build_where($p, $statusParam, $hasStart, $startParam, $hasEnd, $endParam);
    $stmt = $pdo->prepare("SELECT academic_level, COUNT(*) c FROM submissions $where GROUP BY academic_level");
    $stmt->execute($p);
    $agg = ['Undergraduate'=>0,'Graduate School'=>0,'Open University'=>0];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lvl = level_label((string)($r['academic_level'] ?? ''));
        $agg[$lvl] = ($agg[$lvl] ?? 0) + (int)$r['c'];
    }
    $undergrad = (int)($agg['Undergraduate'] ?? 0);
    $grad = (int)($agg['Graduate School'] ?? 0);
    $open = (int)($agg['Open University'] ?? 0);
    // Compute Not Studying within same filter window and subtract from Undergrad
    $nsParams = $p; $nsWhere = $where;
    $nsClause = "(TRIM(LOWER(COALESCE(academic_level,''))) IN ('not studying','n/a','na','-','') OR LOWER(academic_level) LIKE ?) AND (TRIM(LOWER(COALESCE(program,''))) IN ('n/a','na','') OR LOWER(program) LIKE ?)";
    if ($nsWhere) { $nsWhere .= ' AND ' . $nsClause; } else { $nsWhere = ' WHERE ' . $nsClause; }
    $stmtNs = $pdo->prepare("SELECT COUNT(*) FROM submissions $nsWhere");
    $stmtNs->execute(array_merge($nsParams, ['%not stud%','%not stud%']));
    $nsLocal = (int)$stmtNs->fetchColumn();
    $undergrad = max(0, $undergrad - $nsLocal);
    $total_applications_chart = (int)($undergrad + $grad + $open + $nsLocal);
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'Overview chart query failed', ['err'=>$e->getMessage()]); }
}

// Applications by College (top 12) with N/A mapped to academic level labels and filters
if (!$useSummary) {
$collegeLabels = [];
$collegeValues = [];
try {
    $p = [];
    $where = build_where($p, $statusParam, $hasStart, $startParam, $hasEnd, $endParam);
    // Augment WHERE with Completed Applications-style filters when provided
    $adv = [];
    // College filter: accept code (e.g., CCIS). Match when college starts with "CCIS -" or equals code
    if (strcasecmp($collegeParam, 'All') !== 0 && $collegeParam !== '') {
        $adv[] = '(LOWER(college) LIKE ? OR LOWER(college) = ?)';
        $p[] = strtolower($collegeParam) . ' - %';
        $p[] = strtolower($collegeParam);
    }
    // Program filter: exact, case-insensitive match to stored program
    if (strcasecmp($programParam, 'All') !== 0 && $programParam !== '') {
        $adv[] = 'LOWER(program) = ?';
        $p[] = strtolower($programParam);
    }
    // Campus filter: exact, case-insensitive
    if (strcasecmp($campusParam, 'All') !== 0 && $campusParam !== '') {
        $adv[] = 'LOWER(campus) = ?';
        $p[] = strtolower($campusParam);
    }
    // Type filter: match by work_classification containing the keyword (e.g., "Copyright")
    if (strcasecmp($typeParam, 'All') !== 0 && $typeParam !== '') {
        $adv[] = 'LOWER(work_classification) LIKE ?';
        $p[] = '%' . strtolower($typeParam) . '%';
    }
    // Group filter: Employee -> academic_level contains 'employee'; Student -> does not contain 'employee'
    if (strcasecmp($groupParam, 'All') !== 0 && $groupParam !== '') {
        if (strcasecmp($groupParam, 'Employee') === 0) {
            $adv[] = "LOWER(academic_level) LIKE '%employee%'";
        } elseif (strcasecmp($groupParam, 'Student') === 0) {
            $adv[] = "LOWER(academic_level) NOT LIKE '%employee%'";
        }
    }
    if ($adv) {
        $where .= ($where ? ' AND ' : ' WHERE ') . implode(' AND ', $adv);
    }
    $stmt = $pdo->prepare("SELECT college, academic_level FROM submissions $where");
    $stmt->execute($p);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $agg = [];
    $unknownCount = 0; // track rows without a proper college so we can show a fallback bucket
    foreach ($rows as $r) {
        $rawCollege = trim((string)($r['college'] ?? ''));
        $isNA = ($rawCollege === '' || strcasecmp($rawCollege, 'n/a') === 0 || strcasecmp($rawCollege, 'na') === 0 || stripos($rawCollege, 'unknown') !== false);
        if ($isNA) {
            // Count unknown/blank college for possible 'Unspecified' fallback label
            $unknownCount++;
            continue;
        } else {
            [$code, $full] = parse_college($rawCollege);
            $code = strtoupper($code);
            // Build display label using code if present, else abbreviation helper
            $display = ($code !== '') ? $code : abbreviate_college_label($full !== '' ? $full : $rawCollege);

            // Grouping key: prefer code when present to consolidate variants
            $key = $code !== '' ? ('CODE:' . strtolower($code)) : ('FULL:' . strtolower($full));
            if (!isset($agg[$key])) { $agg[$key] = ['label' => $display, 'count' => 0]; }
            $agg[$key]['count']++;
        }
    }
    // Sort by count desc
    uasort($agg, function($a,$b){ return $b['count'] <=> $a['count']; });
    $lim = 12;
    foreach ($agg as $entry) {
        if ($lim-- <= 0) break;
        $collegeLabels[] = $entry['label'];
        $collegeValues[] = (int)$entry['count'];
    }
    // Fold others for scalability
    [$collegeLabels, $collegeValues] = fold_others($collegeLabels, $collegeValues, 10);
    // If all rows were unknown/blank and got skipped, show a single fallback bar
    if (empty($collegeLabels) && $unknownCount > 0) {
        $collegeLabels = ['Unspecified'];
        $collegeValues = [$unknownCount];
    }
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'College chart query failed', ['err' => $e->getMessage()]); }
}
}

// Applications by Campus (top 12) with filters and Others
if (!$useSummary) {
$campusLabels = [];
$campusValues = [];
try {
    $p = [];
    $where = build_where($p, $statusParam, $hasStart, $startParam, $hasEnd, $endParam);
    $stmt = $pdo->prepare("SELECT campus, COUNT(*) c FROM submissions $where GROUP BY campus ORDER BY c DESC");
    $stmt->execute($p);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $i => $r) {
        $label = trim((string)($r['campus'] ?? ''));
        if ($label === '') { $label = 'Unknown'; }
        $campusLabels[] = $label;
        $campusValues[] = (int)$r['c'];
    }
    // Limit via Others
    [$campusLabels, $campusValues] = fold_others($campusLabels, $campusValues, 10);
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'Campus chart query failed', ['err' => $e->getMessage()]); }
}
}

// Work Classification distribution (by code letter if available) with filters and Others
if (!$useSummary) {
$wcLabels = [];
$wcValues = [];
try {
    $p = [];
    $where = build_where($p, $statusParam, $hasStart, $startParam, $hasEnd, $endParam);
    $stmt = $pdo->prepare("SELECT work_classification, COUNT(*) c FROM submissions $where GROUP BY work_classification ORDER BY c DESC");
    $stmt->execute($p);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $agg = [];
    foreach ($rows as $r) {
        $wc = trim((string)($r['work_classification'] ?? ''));
        $count = (int)$r['c'];
        $code = '';
        if ($wc !== '' && preg_match('/\(([^)]+)\)/', $wc, $m)) { $code = strtolower(trim($m[1])); }
        $label = $code !== '' ? 'Type ' . strtoupper($code) : ($wc !== '' ? $wc : 'Unspecified');
        $agg[$label] = ($agg[$label] ?? 0) + $count;
    }
    // Sort by count desc and limit
    arsort($agg);
    $lim = 12;
    foreach ($agg as $label => $count) {
        if ($lim-- <= 0) break;
        $wcLabels[] = $label;
        $wcValues[] = (int)$count;
    }
    [$wcLabels, $wcValues] = fold_others($wcLabels, $wcValues, 10);
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'Work classification chart query failed', ['err' => $e->getMessage()]); }
}
}

// Fetch admin data (robust session validation)
if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_logged_in']) && !empty($_SESSION['is_admin'])) {
    $user_id = (int)$_SESSION['user_id'];
    try {
        if (!empty($_SESSION['admin_number'])) {
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
                INNER JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? AND ap.admin_number = ? LIMIT 1");
            $stmt->execute([$user_id, $_SESSION['admin_number']]);
        } else {
            // Fallback: first admin profile (if multiple exist, explicit admin_number should always be set by login)
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
                LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? LIMIT 1");
            $stmt->execute([$user_id]);
        }
        $admin = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        if (function_exists('log_event')) { log_event('DB_ERR', 'Admin profile fetch failed', ['err'=>$e->getMessage(), 'user_id'=>$user_id]); }
        $admin = [];
    }
    if (!$admin) {
        // If profile missing, redirect to a setup page if it exists; else show a controlled message.
        if (function_exists('redirect') && file_exists(__DIR__ . '/setup_admin_profile.php')) {
            redirect('admin/setup_admin_profile.php');
        }
        echo 'Admin profile not found.'; exit();
    }
    // Derive display fields
    $admin['username'] = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?: 'Admin User';
    $admin['admin_number'] = $admin['admin_number'] ?? ($_SESSION['admin_number'] ?? '');
} else {
    // Session invalid or expired; rely on require_admin earlier, but double safety redirect
    if (function_exists('redirect')) { redirect('admin/login.php'); }
    echo 'Session invalid.'; exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css?v=2'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin-navbar.css'); ?>">

</head>
<body>
    <header class="bg-light border-bottom py-3 shadow-sm" data-admin-name="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" data-admin-email="<?php echo htmlspecialchars($admin['email']); ?>">
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
                            <li class="nav-item ms-auto"><a class="nav-link fw-bold" aria-current="page" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="catalogs.php">Catalogs</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
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

    <div class="container mt-5">
        <!-- Snapshot dashboard: filters removed to avoid live queries -->
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card users text-center w-100">
                    <h2><?= $total_users ?></h2>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-5 col-6 mb-3 me-5 d-flex justify-content-center">
                <div class="summary-card total text-center w-100">
                    <h2><?= $total_applications ?></h2>
                    <p>Total Applications</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card pending text-center w-100">
                    <h2><?= $pending_applications ?></h2>
                   <p>Pending Applications<br><small>• New/Incomplete</small></p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card approved text-center w-100">
                    <h2><?= $approved_applications ?></h2>
                    <p>Approved Evaluation<br></p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card completed text-center w-100">
                    <h2><?= $completed_applications ?></h2>
                    <p>Completed Applications<br></p>
                </div>
            </div>
        </div>
  <!-- Application Overview Section -->
        <div class="dashboard-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <small id="dashAsOf" class="text-muted d-none" data-initial="<?php echo htmlspecialchars($summary_last_updated ?? ''); ?>" data-initial-iso="<?php echo htmlspecialchars($summary_last_updated_iso ?? ''); ?>">As of —</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="dashReloadBtn" class="btn btn-sm btn-outline-primary">Reload data</button>
                    <div id="dashReloadSpin" class="spinner-border spinner-border-sm text-secondary d-none" role="status" aria-hidden="true"></div>
                </div>
            </div>
            <div class="row align-items-center">
                <h5 class="mb-5"><span class="legend-dot legend-open"></span>Application Overview</h5>
                <div class="col-md-6 chart-container d-flex justify-content-center align-items-center">
                    <canvas id="applicationOverviewChart"
                            data-undergrad="<?= (int)$undergrad ?>"
                            data-grad="<?= (int)$grad ?>"
                            data-open="<?= (int)$open ?>"
                            data-notstudying="<?= (int)$not_studying ?>"
                            data-total="<?= (int)$total_applications_chart ?>">
                    </canvas>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <div style="font-size: 1.2rem;">
                        <span style="color:#870000;">Undergraduate</span> <b id="countUndergrad"><?= $undergrad ?></b> &nbsp;
                        <span style="color:#FFD54F;">Graduate School</span> <b id="countGrad"><?= $grad ?></b> &nbsp;
                        <span style="color:gray;">Open University</span> <b id="countOpen"><?= $open ?></b> &nbsp;
                        <span style="color:#6f42c1;">Not Studying</span> <b id="countNotStudying"><?= $not_studying ?></b> &nbsp; <br><br>
                        <span style="font-weight:600;">| <span id="countTotalApplications"><?= $total_applications_chart ?></span> Total Applications</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Applications by College (Top) -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-undergrad"></span> Applications by College</h5>
            <div class="bar-chart-container">
                <canvas id="mainUndergradChart"
                    data-labels='<?php echo htmlspecialchars(json_encode($collegeLabels), ENT_QUOTES, 'UTF-8'); ?>'
                    data-values='<?php echo htmlspecialchars(json_encode($collegeValues), ENT_QUOTES, 'UTF-8'); ?>'></canvas>
            </div>
        </div>
        

        <!-- Applications by Campus (Top) -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-grad"></span> Applications by Campus</h5>
            <div class="bar-chart-container">
                <canvas id="branchesChart"
                    data-labels='<?php echo htmlspecialchars(json_encode($campusLabels), ENT_QUOTES, 'UTF-8'); ?>'
                    data-values='<?php echo htmlspecialchars(json_encode($campusValues), ENT_QUOTES, 'UTF-8'); ?>'></canvas>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script src="../javascript/admin-dashboard.js?v=9" defer></script>
    <script src="../javascript/admin-profile.js?v=5" defer></script>
    <script src="../javascript/admin-notifications.js?v=1" defer></script>

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
                                <input type="email" class="form-control" id="profileAdminEmail" value="<?php echo htmlspecialchars($admin['email']); ?>" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="profileAdminNumber" class="form-label fw-semibold">Admin Number</label>
                                <input type="text" class="form-control" id="profileAdminNumber" value="<?php echo htmlspecialchars($admin['admin_number'] ?? ''); ?>" disabled>
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
                            <div class="form-text">At least 12 characters.</div>
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
        <?php include __DIR__ . '/../partials/standard_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</body>
</html>