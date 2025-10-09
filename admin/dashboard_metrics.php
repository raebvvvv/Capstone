<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

header('Content-Type: application/json');

// CSRF check for POST
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
if (!$csrf || !$sessionToken || !hash_equals($sessionToken, $csrf)) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Parse JSON body
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body)) { $body = []; }

// Validate as_of: expected format 'YYYY-MM-DD HH:MM:SS'
$asOf = null;
if (!empty($body['as_of']) && is_string($body['as_of'])) {
    $s = trim($body['as_of']);
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $s) === 1) {
        $asOf = $s;
    }
}

// Helper: safe table existence
function table_exists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
    $stmt->execute([$table]);
    return (bool)$stmt->fetchColumn();
}

// Build snapshot constraint
$whereSnap = '';
$paramsSnap = [];
if ($asOf !== null) {
    $whereSnap = ' WHERE created_at <= ? ';
    $paramsSnap[] = $asOf;
}

// Normalize academic level to 3 buckets
function level_label(string $raw): string {
    $t = strtolower(trim($raw));
    if ($t === '') return 'Undergraduate';
    if (strpos($t, 'open') !== false) return 'Open University';
    if (strpos($t, 'master') !== false || strpos($t, 'graduate') !== false || strpos($t, 'grad') !== false) return 'Graduate School';
    return 'Undergraduate';
}

// Overview aggregate
$overview = ['undergrad' => 0, 'grad' => 0, 'open' => 0, 'total' => 0];
try {
    if (!table_exists($pdo, 'submissions')) { throw new Exception('Table missing'); }
    $sql = 'SELECT academic_level, COUNT(*) c FROM submissions' . $whereSnap . ' GROUP BY academic_level';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsSnap);
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lvl = level_label((string)($r['academic_level'] ?? ''));
        $count = (int)($r['c'] ?? 0);
        if ($lvl === 'Undergraduate') { $overview['undergrad'] += $count; }
        elseif ($lvl === 'Graduate School') { $overview['grad'] += $count; }
        elseif ($lvl === 'Open University') { $overview['open'] += $count; }
    }
    $overview['total'] = $overview['undergrad'] + $overview['grad'] + $overview['open'];
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'metrics overview failed', ['err' => $e->getMessage()]); }
}

// College chart (top 10 + Others)
$byCollege = ['labels' => [], 'values' => []];
try {
    if (!table_exists($pdo, 'submissions')) { throw new Exception('Table missing'); }
    $sql = 'SELECT college FROM submissions' . $whereSnap;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsSnap);
    $agg = [];
    $unknown = 0;
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $raw = trim((string)($r['college'] ?? ''));
        if ($raw === '' || strcasecmp($raw, 'n/a') === 0 || strcasecmp($raw, 'na') === 0 || stripos($raw, 'unknown') !== false) {
            $unknown++;
            continue;
        }
        // Prefer code before " - " if present else raw
        $label = $raw;
        if (strpos($raw, ' - ') !== false) {
            $label = trim(substr($raw, 0, strpos($raw, ' - ')));
        }
        $agg[$label] = ($agg[$label] ?? 0) + 1;
    }
    arsort($agg);
    $labels = array_keys($agg);
    $values = array_values($agg);
    if (count($labels) > 10) {
        $topLabels = array_slice($labels, 0, 10);
        $topValues = array_slice($values, 0, 10);
        $others = array_sum(array_slice($values, 10));
        $topLabels[] = 'Others';
        $topValues[] = $others;
        $labels = $topLabels; $values = $topValues;
    }
    if (empty($labels) && $unknown > 0) { $labels = ['Unspecified']; $values = [$unknown]; }
    $byCollege = ['labels' => $labels, 'values' => array_map('intval', $values)];
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'metrics byCollege failed', ['err' => $e->getMessage()]); }
}

// Campus chart (top 10 + Others)
$byCampus = ['labels' => [], 'values' => []];
try {
    if (!table_exists($pdo, 'submissions')) { throw new Exception('Table missing'); }
    $sql = 'SELECT campus, COUNT(*) c FROM submissions' . $whereSnap . ' GROUP BY campus ORDER BY c DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsSnap);
    $labels = [];$values = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lbl = trim((string)($r['campus'] ?? ''));
        if ($lbl === '') { $lbl = 'Unknown'; }
        $labels[] = $lbl; $values[] = (int)($r['c'] ?? 0);
    }
    if (count($labels) > 10) {
        $topLabels = array_slice($labels, 0, 10);
        $topValues = array_slice($values, 0, 10);
        $others = array_sum(array_slice($values, 10));
        $topLabels[] = 'Others';
        $topValues[] = $others;
        $labels = $topLabels; $values = $topValues;
    }
    $byCampus = ['labels' => $labels, 'values' => array_map('intval', $values)];
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'metrics byCampus failed', ['err' => $e->getMessage()]); }
}

// Work classification distribution (top 10 + Others)
$workClass = ['labels' => [], 'values' => []];
try {
    if (!table_exists($pdo, 'submissions')) { throw new Exception('Table missing'); }
    $sql = 'SELECT work_classification, COUNT(*) c FROM submissions' . $whereSnap . ' GROUP BY work_classification ORDER BY c DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($paramsSnap);
    $agg = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $wc = trim((string)($r['work_classification'] ?? ''));
        $count = (int)($r['c'] ?? 0);
        $label = $wc !== '' ? $wc : 'Unspecified';
        $agg[$label] = ($agg[$label] ?? 0) + $count;
    }
    arsort($agg);
    $labels = [];$values = [];$i=0;
    foreach ($agg as $lbl => $cnt) {
        if ($i++ < 10) { $labels[] = $lbl; $values[] = (int)$cnt; }
    }
    $others = array_sum(array_slice(array_values($agg), 10));
    if ($others > 0) { $labels[] = 'Others'; $values[] = $others; }
    $workClass = ['labels' => $labels, 'values' => array_map('intval', $values)];
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_WARN', 'metrics workClass failed', ['err' => $e->getMessage()]); }
}

// Use provided as_of string or server now in ISO for display
$asOfOut = $asOf !== null ? $asOf : gmdate('Y-m-d H:i:s');

echo json_encode([
    'success' => true,
    'as_of' => $asOfOut,
    'overview' => $overview,
    'byCollege' => $byCollege,
    'byCampus' => $byCampus,
    'workClass' => $workClass,
]);
