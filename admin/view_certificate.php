<?php
// Serve generated certificate PDF (inline preview or download)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

require_once app_path('includes/certificate_generator.php');

// Inputs: ?id=SUBMISSION_CODE_OR_ID & (mode=inline|download)
$req = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
if ($req === '') { http_response_code(400); echo 'Missing id'; exit; }
$mode = isset($_GET['mode']) ? strtolower(trim((string)$_GET['mode'])) : 'inline';
// Resolve submission id
$isNumeric = ctype_digit($req);
$col = $isNumeric ? 'submission_id' : 'submission_code';
$stmt = $pdo->prepare("SELECT submission_id FROM submissions WHERE $col = ? LIMIT 1");
$stmt->execute([$isNumeric ? (int)$req : $req]);
$sid = (int)($stmt->fetchColumn() ?: 0);
if ($sid <= 0) { http_response_code(404); echo 'Submission not found'; exit; }

// Optional coordinate override & debug via query string for tuning:
// Example: &tx=20&ty=95&tw=170&ax=20&ay=125&aw=170&dx=20&dy=155&dw=170&debug=1&force=1
$coords = [];
$map = [
    'title'=>['tx','ty','tw'],
    'authors'=>['ax','ay','aw'],
    'date'=>['dx','dy','dw']
];
foreach ($map as $key=>$params) {
        [$px,$py,$pw] = $params;
        $hasAny = isset($_GET[$px]) || isset($_GET[$py]) || isset($_GET[$pw]);
        if ($hasAny) {
                $coords[$key] = [
                    'x' => isset($_GET[$px]) ? (float)$_GET[$px] : ($key==='title'?1:20),
                    'y' => isset($_GET[$py]) ? (float)$_GET[$py] : ($key==='title'?110:($key==='authors'?130:150)),
                    'w' => isset($_GET[$pw]) ? (float)$_GET[$pw] : 170,
                ];
        }
}
$options = [];
if ($coords) { $options['coords'] = $coords; }
if (!empty($_GET['debug'])) { $options['debug_boxes'] = true; }
if (!empty($_GET['force'])) { $options['force_regen'] = true; }
if (isset($_GET['tfs'])) { $options['title_font_size'] = (int)$_GET['tfs']; }
if (isset($_GET['afs'])) { $options['authors_font_size'] = (int)$_GET['afs']; }
if (isset($_GET['dfs'])) { $options['date_font_size'] = (int)$_GET['dfs']; }

try {
        $pdfPath = generate_certificate($pdo, $sid, $options);
} catch (Throwable $e) {
    http_response_code(500);
    if (function_exists('log_event')) { log_event('CERT_ERROR', 'Generate failed', ['sid'=>$sid, 'err'=>$e->getMessage()]); }
    echo 'Certificate generation failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
if (!is_file($pdfPath)) { http_response_code(500); echo 'Certificate file missing'; exit; }
// Basic caching headers (regenerate logic already checks mtimes)
header('Cache-Control: private, max-age=3600');
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($pdfPath));
if ($mode === 'download') {
    header('Content-Disposition: attachment; filename="certificate-' . basename($pdfPath) . '"');
} else {
    header('Content-Disposition: inline; filename="certificate-' . basename($pdfPath) . '"');
}
readfile($pdfPath);
exit;
?>
