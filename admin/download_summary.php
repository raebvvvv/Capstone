<?php
// Secure and connect (align with existing admin pages)
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();

// Helpers
function fmt_date(string $dateStr): string {
    $ts = strtotime($dateStr);
    return $ts ? date('F j, Y', $ts) : trim($dateStr);
}

function name_to_author_format(?string $first, ?string $middle, ?string $last): string {
    $first = trim((string)$first);
    $middle = trim((string)$middle);
    $last = trim((string)$last);
    $mi = '';
    if ($middle !== '') {
        // Take first character of middle name as initial if present
        $c = mb_substr($middle, 0, 1);
        if ($c !== false && $c !== '') { $mi = ' ' . strtoupper($c) . '.'; }
    }
    $firstPart = $first . $mi;
    $firstPart = trim($firstPart);
    if ($last === '' && $firstPart === '') return '';
    if ($last === '') return $firstPart; // fallback when only first present
    if ($firstPart === '') return $last;  // fallback when only last present
    return $last . ', ' . $firstPart;
}

function fetch_submission_people(PDO $pdo, int $sid): array {
    // Returns ['authors' => [..], 'adviser' => '']
    // Include adviser(s) in Author/s list as well, to support cases where adviser is also an author.
    // Note: submission_authors schema observed elsewhere provides first_name, last_name, is_adviser (no middle_name)
    $stmt = $pdo->prepare("SELECT first_name, last_name, is_adviser FROM submission_authors WHERE submission_id = ? ORDER BY is_adviser DESC, last_name ASC, first_name ASC");
    $stmt->execute([$sid]);
    $authors = [];
    $seen = [];
    $adviser = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $formatted = name_to_author_format($row['first_name'] ?? '', '', $row['last_name'] ?? '');
        if ($formatted === '') { continue; }
        if ((int)($row['is_adviser'] ?? 0) === 1 && $adviser === '') {
            $adviser = $formatted; // first adviser
        }
        // Add to authors list (including advisers) with de-duplication
        $key = mb_strtolower($formatted);
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $authors[] = $formatted;
        }
    }
    return ['authors' => $authors, 'adviser' => $adviser];
}

// Parse inputs
// Use separate 'summary' param for summary flavor to avoid clashing with 'type' filter
$summary = isset($_GET['summary']) ? strtolower(trim($_GET['summary'])) : 'national';
$isRmipo = ($summary === 'rmipo');
$filename = ($isRmipo ? 'summary_rmipo' : 'summary_national') . '_' . date('Ymd_His') . '.csv';

// Optional date range filter (?start=YYYY-MM-DD&end=YYYY-MM-DD)
$start = isset($_GET['start']) ? trim($_GET['start']) : '';
$end   = isset($_GET['end']) ? trim($_GET['end']) : '';
$hasRange = ($start !== '' && $end !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end));

// Optional filters to mirror Completed Applications page
$level  = isset($_GET['level'])  ? trim($_GET['level'])  : 'All';
$college= isset($_GET['college'])? trim($_GET['college']): 'All'; // code like CCIS
$program= isset($_GET['program'])? trim($_GET['program']): 'All'; // expect lowercased
$campus = isset($_GET['campus']) ? trim($_GET['campus']) : 'All';
$itype  = isset($_GET['type'])   ? trim($_GET['type'])   : 'All'; // maps to work_classification
$group  = isset($_GET['group'])  ? trim($_GET['group'])  : 'All'; // Employee/Student (derived)

// Build header row per spec
if ($isRmipo) {
    $headers = [
        'Campus',
        'Program',
        'Author/s',
        'Title',
        'Adviser',
        'Date Accepted',
        'Work Classification',
        'Date of Transfer to ITSO (for Evaluation)'
    ];
} else {
    $headers = [
        'Title',
        'Program',
        'Author/s',
        'Date',
        'Adviser',
        'Date of Evaluated',
        'Date of Evaluation'
    ];
}

// Query completed submissions
try {
    $sql = "SELECT submission_id, first_name, middle_name, last_name, campus, program, title, date_accomplished, status_updated_at, created_at, college, academic_level, work_classification FROM submissions WHERE LOWER(status) = 'completed'";
    $params = [];
    // Date range on COALESCE(status_updated_at, created_at)
    if ($hasRange) {
        $sql .= " AND DATE(COALESCE(status_updated_at, created_at)) BETWEEN ? AND ?";
        $params[] = $start; $params[] = $end;
    }
    // Academic Level (exact, case-insensitive), skip if 'All'
    if ($level !== '' && strcasecmp($level, 'All') !== 0) {
        $sql .= " AND LOWER(academic_level) = ?";
        $params[] = strtolower($level);
    }
    // Group: Employee -> academic_level like %employee%; Student -> NOT like %employee%
    if ($group !== '' && strcasecmp($group, 'All') !== 0) {
        if (strcasecmp($group, 'Employee') === 0) {
            $sql .= " AND LOWER(academic_level) LIKE '%employee%'";
        } elseif (strcasecmp($group, 'Student') === 0) {
            $sql .= " AND LOWER(academic_level) NOT LIKE '%employee%'";
        }
    }
    // College code (accept code in multiple stored formats)
    if ($college !== '' && strcasecmp($college, 'All') !== 0 && strcasecmp($college, 'N/A') !== 0) {
        // Matches: "CODE - ..." OR "...(CODE)" OR exactly "CODE"
        $sql .= " AND (college LIKE ? OR college LIKE ? OR college = ?)";
        $params[] = $college . ' - %';
        $params[] = '%(' . $college . ')';
        $params[] = $college;
    }
    // Program (exact match, case-insensitive)
    if ($program !== '' && strcasecmp($program, 'All') !== 0) {
        $sql .= " AND LOWER(program) = ?";
        $params[] = strtolower($program);
    }
    // Campus (substring, case-insensitive)
    if ($campus !== '' && strcasecmp($campus, 'All') !== 0) {
        $sql .= " AND LOWER(campus) LIKE ?";
        $params[] = '%' . strtolower($campus) . '%';
    }
    // Types -> map to work_classification
    if ($itype !== '' && strcasecmp($itype, 'All') !== 0) {
        $sql .= " AND LOWER(work_classification) = ?";
        $params[] = strtolower($itype);
    }
    $sql .= " ORDER BY COALESCE(status_updated_at, created_at) DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Failed to build summary: ' . $e->getMessage();
    exit;
}

// Build data rows
$rows = [];
foreach ($subs as $s) {
    $sid = (int)$s['submission_id'];
    $people = fetch_submission_people($pdo, $sid);
    $authors = $people['authors'];
    $adviser = $people['adviser'];

    // Fallback to primary submitter if no authors listed
    if (count($authors) === 0) {
        $fallback = name_to_author_format($s['first_name'] ?? '', $s['middle_name'] ?? '', $s['last_name'] ?? '');
        if ($fallback !== '') { $authors[] = $fallback; }
    }
    $authorsStr = implode('; ', $authors);

    // Preferred date: date_accomplished -> status_updated_at -> created_at
    $d = $s['date_accomplished'] ?: ($s['status_updated_at'] ?: $s['created_at']);
    $datePretty = $d ? fmt_date($d) : '';

    if ($isRmipo) {
        $rows[] = [
            (string)($s['campus'] ?? ''),
            (string)($s['program'] ?? ''),
            $authorsStr,
            (string)($s['title'] ?? ''),
            (string)$adviser,
            $datePretty,
            'class O', // per provided format
            ''         // Date of Transfer to ITSO (for Evaluation)
        ];
    } else {
        $rows[] = [
            (string)($s['title'] ?? ''),
            (string)($s['program'] ?? ''),
            $authorsStr,
            $datePretty,
            (string)$adviser,
            '', // Date of Evaluated
            ''  // Date of Evaluation
        ];
    }
}

// Output CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, $headers);
foreach ($rows as $row) {
    fputcsv($out, $row);
}
fclose($out);
exit;
