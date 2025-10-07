<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }

header('Content-Type: application/json');

// Allow CLI (scheduler) or Admin POST (manual reload)
$isCli = (php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg');
if (!$isCli) {
    // Inline admin check to avoid HTML redirects in XHR
    if (empty($_SESSION['user_logged_in']) || empty($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
        exit;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!$csrf || !$sessionToken || !hash_equals($sessionToken, $csrf)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }
}

// Helpers
function table_exists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function level_label(string $raw): string {
    $t = strtolower(trim($raw));
    if ($t === '') return 'Undergraduate';
    if (strpos($t, 'open') !== false) return 'Open University';
    if (strpos($t, 'master') !== false || strpos($t, 'graduate') !== false || strpos($t, 'grad') !== false) return 'Graduate School';
    return 'Undergraduate';
}

// Ensure summary table exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `dashboard_summary` (
      `id` TINYINT NOT NULL,
      `last_updated` DATETIME NOT NULL,
      `total_users` INT NOT NULL DEFAULT 0,
      `total_apps` INT NOT NULL DEFAULT 0,
      `pending_apps` INT NOT NULL DEFAULT 0,
      `approved_apps` INT NOT NULL DEFAULT 0,
      `completed_apps` INT NOT NULL DEFAULT 0,
      `overview_undergrad` INT NOT NULL DEFAULT 0,
      `overview_grad` INT NOT NULL DEFAULT 0,
      `overview_open` INT NOT NULL DEFAULT 0,
      `by_college_json` LONGTEXT NULL,
      `by_campus_json` LONGTEXT NULL,
      `work_class_json` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR','create dashboard_summary failed',['err'=>$e->getMessage()]); }
    echo json_encode(['success'=>false,'error'=>'Failed to ensure summary table']);
    exit;
}

// Aggregate
// Use Asia/Manila for consistent PH time both in DB and API response
$dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
$now = $dt->format('Y-m-d H:i:s'); // for DB
$now_iso = $dt->format(DateTime::ATOM); // ISO 8601 with timezone
$total_users = 0; $total_apps = 0; $pending = 0; $approved = 0; $completed = 0;
$undergrad = 0; $grad = 0; $open = 0; $notStudying = 0;
$byCollege = ['labels'=>[], 'values'=>[]];
$byCampus  = ['labels'=>[], 'values'=>[]];
$workClass = ['labels'=>[], 'values'=>[]];

try {
    if (table_exists($pdo, 'users')) {
        $total_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('student','employee') AND status='active'")->fetchColumn();
    }
    if (table_exists($pdo, 'submissions')) {
        $total_apps = (int)$pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
        $pending    = (int)$pdo->query("SELECT COUNT(*) FROM submissions WHERE status IN ('pending','pending_review')")->fetchColumn();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE status = ?");
        $stmt->execute(['approved']); $approved = (int)$stmt->fetchColumn();
        $stmt->execute(['completed']); $completed = (int)$stmt->fetchColumn();

        // Overview buckets
        $agg = ['Undergraduate'=>0,'Graduate School'=>0,'Open University'=>0];
        $q = $pdo->query("SELECT academic_level, COUNT(*) c FROM submissions GROUP BY academic_level");
        while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
            $lvl = level_label((string)($r['academic_level'] ?? ''));
            $agg[$lvl] = ($agg[$lvl] ?? 0) + (int)($r['c'] ?? 0);
        }
        $undergrad = (int)($agg['Undergraduate'] ?? 0);
        $grad      = (int)($agg['Graduate School'] ?? 0);
        $open      = (int)($agg['Open University'] ?? 0);
        // Compute Not Studying separately (not part of existing buckets)
        try {
            $stmtNs = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE LOWER(academic_level) = ? OR LOWER(academic_level) LIKE ?");
            $stmtNs->execute(['not studying', 'not stud%']);
            $notStudying = (int)$stmtNs->fetchColumn();
        } catch (Throwable $eNs) { $notStudying = 0; }

        // By College (top 10 + Others)
        $acc = [];$unknown=0;
        $q = $pdo->query("SELECT college FROM submissions");
        while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
            $raw = trim((string)($r['college'] ?? ''));
            if ($raw === '' || strcasecmp($raw,'n/a')===0 || strcasecmp($raw,'na')===0 || stripos($raw,'unknown')!==false){ $unknown++; continue; }
            $label = $raw;
            if (strpos($raw, ' - ') !== false) { $label = trim(substr($raw, 0, strpos($raw, ' - '))); }
            $acc[$label] = ($acc[$label] ?? 0) + 1;
        }
        arsort($acc);
        $labels = array_keys($acc); $values = array_values($acc);
        if (count($labels) > 10) {
            $topL = array_slice($labels,0,10); $topV = array_slice($values,0,10);
            $others = array_sum(array_slice($values,10)); $topL[]='Others'; $topV[]=$others;
            $labels=$topL; $values=$topV;
        }
        if (empty($labels) && $unknown>0){ $labels=['Unspecified']; $values=[$unknown]; }
        $byCollege = ['labels'=>$labels, 'values'=>array_map('intval',$values)];

        // By Campus (top 10 + Others)
        $labels=[]; $values=[]; $q = $pdo->query("SELECT campus, COUNT(*) c FROM submissions GROUP BY campus ORDER BY c DESC");
        while($r=$q->fetch(PDO::FETCH_ASSOC)){
            $lbl = trim((string)($r['campus'] ?? '')); if($lbl===''){ $lbl='Unknown'; }
            $labels[]=$lbl; $values[]=(int)($r['c'] ?? 0);
        }
        if (count($labels) > 10) {
            $topL = array_slice($labels,0,10); $topV = array_slice($values,0,10);
            $others = array_sum(array_slice($values,10)); $topL[]='Others'; $topV[]=$others; $labels=$topL; $values=$topV;
        }
        $byCampus = ['labels'=>$labels, 'values'=>array_map('intval',$values)];

        // Work Classification (top 10 + Others)
        $acc=[]; $q = $pdo->query("SELECT work_classification, COUNT(*) c FROM submissions GROUP BY work_classification ORDER BY c DESC");
        while($r=$q->fetch(PDO::FETCH_ASSOC)){
            $wc = trim((string)($r['work_classification'] ?? '')); $cnt=(int)($r['c'] ?? 0);
            $label = $wc !== '' ? $wc : 'Unspecified';
            $acc[$label] = ($acc[$label] ?? 0) + $cnt;
        }
        arsort($acc); $labels=[];$values=[];$i=0; foreach($acc as $lbl=>$cnt){ if($i++<10){ $labels[]=$lbl; $values[]=(int)$cnt; } }
        $others = array_sum(array_slice(array_values($acc),10)); if($others>0){ $labels[]='Others'; $values[]=$others; }
        $workClass = ['labels'=>$labels, 'values'=>array_map('intval',$values)];
    }
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN','dashboard refresh aggregation failed',['err'=>$e->getMessage()]); }
}

// Upsert summary row
try {
    $stmt = $pdo->prepare("INSERT INTO dashboard_summary
      (id,last_updated,total_users,total_apps,pending_apps,approved_apps,completed_apps,overview_undergrad,overview_grad,overview_open,by_college_json,by_campus_json,work_class_json)
      VALUES (1,?,?,?,?,?,?,?,?,?,?,?,?)
      ON DUPLICATE KEY UPDATE
      last_updated=VALUES(last_updated), total_users=VALUES(total_users), total_apps=VALUES(total_apps), pending_apps=VALUES(pending_apps), approved_apps=VALUES(approved_apps), completed_apps=VALUES(completed_apps), overview_undergrad=VALUES(overview_undergrad), overview_grad=VALUES(overview_grad), overview_open=VALUES(overview_open), by_college_json=VALUES(by_college_json), by_campus_json=VALUES(by_campus_json), work_class_json=VALUES(work_class_json)");
    $stmt->execute([
        $now,
        $total_users, $total_apps, $pending, $approved, $completed,
        $undergrad, $grad, $open,
        json_encode($byCollege, JSON_UNESCAPED_UNICODE),
        json_encode($byCampus, JSON_UNESCAPED_UNICODE),
        json_encode($workClass, JSON_UNESCAPED_UNICODE)
    ]);
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR','dashboard summary upsert failed',['err'=>$e->getMessage()]); }
    echo json_encode(['success'=>false,'error'=>'Failed to update summary']);
    exit;
}

echo json_encode([
    'success' => true,
    'last_updated' => $now,
    'last_updated_iso' => $now_iso,
    'overview' => [
        'undergrad' => $undergrad,
        'grad' => $grad,
        'open' => $open,
        'notStudying' => $notStudying,
        'total' => $undergrad + $grad + $open + $notStudying,
    ],
    'byCollege' => $byCollege,
    'byCampus' => $byCampus,
    'workClass' => $workClass,
    'totals' => [
        'users' => $total_users,
        'applications' => $total_apps,
        'pending' => $pending,
        'approved' => $approved,
        'completed' => $completed,
    ]
]);
?>
