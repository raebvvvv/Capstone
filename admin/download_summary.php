<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();

// Helper to format dates like "May 18, 2025"
function fmt_date(string $dateStr): string {
    $ts = strtotime($dateStr);
    return $ts ? date('F j, Y', $ts) : $dateStr;
}

// CSV download based on requested type: 'rmipo' or 'national'
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'national';
$isRmipo = ($type === 'rmipo');
$filename = ($isRmipo ? 'summary_rmipo' : 'summary_national') . '_' . date('Ymd_His') . '.csv';

// NOTE: Replace the stubbed rows below with real data/query results.
// Columns per requirement:
// - RMIPO: Campus, Program, Author/s, Title, Adviser (if any), Date Accepted, Work Classification ("class O"), Date of Transfer to ITSO (for Evaluation) (blank)
// - National Library: Title, Program, Author/s, Date, Adviser (if any), Date of Evaluated (blank), Date of Evaluation (blank)

// Build header row
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

// Example rows (placeholder values). Replace with real records.
if ($isRmipo) {
    $dataRows = [
        [
            'PUP Sta. Mesa, Manila',     // Campus
            'BSIT',                       // Program
            'Doe, Jane; Dela Cruz, Juan P.', // Author/s
            'A Study on Smart Campus IoT',   // Title
            'Prof. Maria L. Santos',      // Adviser (if any)
            fmt_date('2025-05-31'),       // Date Accepted
            'class O',                    // Work Classification (per requirement)
            ''                            // Date of Transfer to ITSO (for Evaluation) (blank)
        ],
        [
            'PUP San Juan',               // Campus
            'BSCS',                       // Program
            'Reyes, Ana M.; Cruz, Mark T.',
            'Optimizing Graph Traversals with Heuristics',
            '',                           // Adviser (none)
            fmt_date('2025-04-15'),       // Date Accepted
            'class O',
            ''
        ],
        [
            'PUP Sta. Rosa',              // Campus
            'BSECE',                      // Program
            'Garcia, Pedro L.; Chan, Mei L.; Smith, John',
            'Low-Power Embedded Vision System for Traffic Monitoring',
            'Engr. Roberto D. Cruz',      // Adviser
            fmt_date('2025-03-10'),       // Date Accepted
            'class O',
            ''
        ],
        [
            'PUP Sta. Mesa, Manila',
            'BSCpE',
            'Santos, Maria L.',
            'FPGA-Based Accelerator for Matrix Operations',
            '',                           // Adviser (none)
            fmt_date('2025-02-20'),
            'class O',
            ''
        ],
        [
            'PUP Sta. Mesa, Manila',
            'BSAIS',
            'Tan, Carlos M.; Lim, Andrea P.',
            'AI-Assisted Financial Fraud Detection in SMEs',
            'Prof. Juan Miguel S. Dizon',
            fmt_date('2025-01-12'),
            'class O',
            ''
        ]
    ];
} else {
    $dataRows = [
        [
            'A Study on Smart Campus IoT',   // Title
            'BSIT',                          // Program
            'Doe, Jane; Dela Cruz, Juan P.', // Author/s
            fmt_date('2025-05-31'),          // Date
            'Prof. Maria L. Santos',         // Adviser (if any)
            '',                               // Date of Evaluated (blank)
            ''                                // Date of Evaluation (blank)
        ],
        [
            'Optimizing Graph Traversals with Heuristics',
            'BSCS',
            'Reyes, Ana M.; Cruz, Mark T.',
            fmt_date('2025-04-15'),
            '',                               // Adviser (none)
            '',
            ''
        ],
        [
            'Low-Power Embedded Vision System for Traffic Monitoring',
            'BSECE',
            'Garcia, Pedro L.; Chan, Mei L.; Smith, John',
            fmt_date('2025-03-10'),
            'Engr. Roberto D. Cruz',
            '',
            ''
        ],
        [
            'FPGA-Based Accelerator for Matrix Operations',
            'BSCpE',
            'Santos, Maria L.',
            fmt_date('2025-02-20'),
            '',
            '',
            ''
        ],
        [
            'AI-Assisted Financial Fraud Detection in SMEs',
            'BSAIS',
            'Tan, Carlos M.; Lim, Andrea P.',
            fmt_date('2025-01-12'),
            'Prof. Juan Miguel S. Dizon',
            '',
            ''
        ]
    ];
}

// Output CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Optional: add UTF-8 BOM for better Excel compatibility
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, $headers);
foreach ($dataRows as $row) {
    fputcsv($out, $row);
}
fclose($out);
exit;
