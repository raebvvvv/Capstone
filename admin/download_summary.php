<?php
// Secure and connect (align with existing admin pages)
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();

require __DIR__ . '/../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

function fetch_submission_people(PDO $pdo, int $sid, string $submitterFullName = ''): array {
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
    // Ensure submitter is included among authors if provided and not already present
    $submitterFullName = trim($submitterFullName);
    if ($submitterFullName !== '') {
        $key = mb_strtolower($submitterFullName);
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $authors[] = $submitterFullName;
        }
    }
    return ['authors' => $authors, 'adviser' => $adviser];
}

// Parse inputs
// Use separate 'summary' param for summary flavor to avoid clashing with 'type' filter
$summary = isset($_GET['summary']) ? strtolower(trim($_GET['summary'])) : 'national';
$isRmipo = ($summary === 'rmipo');

// Optional date range filter (?start=YYYY-MM-DD&end=YYYY-MM-DD)
$start = isset($_GET['start']) ? trim($_GET['start']) : '';
$end   = isset($_GET['end']) ? trim($_GET['end']) : '';
$hasRange = ($start !== '' && $end !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end));

// Optional filters to mirror Completed Applications page
$level  = isset($_GET['level'])  ? trim($_GET['level'])  : 'All';
$college= isset($_GET['college'])? trim($_GET['college']): 'All'; // code like CCIS
$program= isset($_GET['program'])? trim($_GET['program']): 'All'; // expect lowercased
$department = isset($_GET['department']) ? trim($_GET['department']) : 'All';
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
    $sql = "SELECT 
                s.submission_id, 
                s.campus, 
                s.program, 
                s.title, 
                s.date_accomplished, 
                s.status_updated_at, 
                s.created_at, 
                s.college, 
                s.academic_level, 
                s.work_classification,
                CASE 
                    WHEN u.role = 'student' THEN sp.first_name
                    WHEN u.role = 'employee' THEN ep.first_name
                    ELSE 'Unknown'
                END as first_name,
                CASE 
                    WHEN u.role = 'student' THEN sp.middle_name
                    WHEN u.role = 'employee' THEN ep.middle_name
                    ELSE ''
                END as middle_name,
                CASE 
                    WHEN u.role = 'student' THEN sp.last_name
                    WHEN u.role = 'employee' THEN ep.last_name
                    ELSE 'User'
                END as last_name
            FROM submissions s
            LEFT JOIN users u ON u.user_id = s.user_id
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
            WHERE LOWER(s.status) = 'completed'";
    $params = [];
    // Date range on COALESCE(status_updated_at, created_at)
    if ($hasRange) {
        $sql .= " AND DATE(COALESCE(s.status_updated_at, s.created_at)) BETWEEN ? AND ?";
        $params[] = $start; $params[] = $end;
    }
    // Academic Level (exact, case-insensitive), skip if 'All'
    if ($level !== '' && strcasecmp($level, 'All') !== 0) {
        $sql .= " AND LOWER(s.academic_level) = ?";
        $params[] = strtolower($level);
    }
    // Group filter should reflect the UI which derives group from users.role
    if ($group !== '' && strcasecmp($group, 'All') !== 0) {
        if (strcasecmp($group, 'Employee') === 0) {
            $sql .= " AND LOWER(u.role) = 'employee'";
        } elseif (strcasecmp($group, 'Student') === 0) {
            $sql .= " AND LOWER(u.role) = 'student'";
        }
    }
    // College code (accept code in multiple stored formats)
    if ($college !== '' && strcasecmp($college, 'All') !== 0 && strcasecmp($college, 'N/A') !== 0) {
        // Matches: "CODE - ..." OR "...(CODE)" OR exactly "CODE"
        $sql .= " AND (s.college LIKE ? OR s.college LIKE ? OR s.college = ?)";
        $params[] = $college . ' - %';
        $params[] = '%(' . $college . ')';
        $params[] = $college;
    }
    // Program (exact match, case-insensitive)
    if ($program !== '' && strcasecmp($program, 'All') !== 0) {
        $sql .= " AND LOWER(s.program) = ?";
        $params[] = strtolower($program);
    }
    // Department (for employee submissions). Matches exact lowercased value; 'N/A' handled as empty/na match.
    if ($department !== '' && strcasecmp($department, 'All') !== 0) {
        if (strcasecmp($department, 'N/A') === 0) {
            // consider rows where department is NULL, empty, or literally 'N/A'
            $sql .= " AND (ep.department IS NULL OR TRIM(ep.department) = '' OR LOWER(ep.department) = 'n/a')";
        } else {
            $sql .= " AND LOWER(ep.department) = ?";
            $params[] = strtolower($department);
        }
    }
    // Campus (substring, case-insensitive)
    if ($campus !== '' && strcasecmp($campus, 'All') !== 0) {
        $sql .= " AND LOWER(s.campus) LIKE ?";
        $params[] = '%' . strtolower($campus) . '%';
    }
    // Types -> map to work_classification
    if ($itype !== '' && strcasecmp($itype, 'All') !== 0) {
        $sql .= " AND LOWER(s.work_classification) = ?";
        $params[] = strtolower($itype);
    }
    $sql .= " ORDER BY COALESCE(s.status_updated_at, s.created_at) DESC";
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
    // Build submitter full name in "Last, First M." format for de-duplication with authors
    $submitterName = name_to_author_format($s['first_name'] ?? '', $s['middle_name'] ?? '', $s['last_name'] ?? '');
    $people = fetch_submission_people($pdo, $sid, $submitterName);
    $authors = $people['authors'];
    $adviser = $people['adviser'];

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

// Normalize values so Excel behaves better (no stray spaces/newlines)
foreach ($rows as &$r) {
    foreach ($r as &$cell) {
        $cell = trim((string)$cell);
    }
    unset($cell);
}
unset($r);

// Build XLSX with PhpSpreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 1) Write header row (A1, B1, ...)
$sheet->fromArray($headers, null, 'A1');

// 2) Write data starting at row 2
if (!empty($rows)) {
    $sheet->fromArray($rows, null, 'A2');
}

// 3) Basic styling: bold headers
$lastColumnIndex  = count($headers); // e.g. 7 for A..G
$lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumnIndex);
$highestRow       = $sheet->getHighestRow();

$headerRange = 'A1:' . $lastColumnLetter . '1';
$sheet->getStyle($headerRange)->getFont()->setBold(true);

// 4) Enable wrap text for text-heavy columns (Title, Author/s)
// For national summary: A = Title, C = Author/s
// For RMIPO summary: C = Author/s, D = Title
if ($isRmipo) {
    $wrapColumns = ['C', 'D'];
} else {
    $wrapColumns = ['A', 'C'];
}
foreach ($wrapColumns as $col) {
    if (Coordinate::columnIndexFromString($col) <= $lastColumnIndex) {
        $sheet->getStyle($col . '1:' . $col . $highestRow)
              ->getAlignment()
              ->setWrapText(true);
    }
}

// 5) Auto-size all used columns (A..last)
foreach (range('A', $lastColumnLetter) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Optional: align date-ish columns centered
// National: D (Date), F (Date of Evaluated), G (Date of Evaluation)
// RMIPO: F (Date Accepted), H (Date of Transfer...)
if ($isRmipo) {
    $dateColumns = ['F', 'H'];
} else {
    $dateColumns = ['D', 'F', 'G'];
}
foreach ($dateColumns as $col) {
    if (Coordinate::columnIndexFromString($col) <= $lastColumnIndex) {
        $sheet->getStyle($col . '2:' . $col . $highestRow)
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}

// 6) Send XLSX to browser
$filename = ($isRmipo ? 'summary_rmipo' : 'summary_national') . '_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
