<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

// Basic CSRF protection if helper exists
// Enforce CSRF via existing helper (defined in security_bootstrap)
if (function_exists('verify_csrf_post')) {
    verify_csrf_post();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Required scalar fields
$requiredFields = [
    'first_name','last_name','student_number','home_address','mobile_number','webmail',
    'campus','academicLevel','college','program','workClassification','title','date_accomplished'
];
$errors = [];
$data = [];
foreach ($requiredFields as $f) {
    $val = trim($_POST[$f] ?? '');
    if ($val === '') {
        $errors[] = "Missing required field: $f";
    } else {
        $data[$f] = $val;
    }
}

// Adviser / authors handling
$data['adviser'] = trim($_POST['adviser'] ?? '');
$data['adviser_coauthor'] = isset($_POST['adviser_coauthor']);
$data['accepted_terms'] = (($_POST['accepted_terms'] ?? '') === 'yes') || (isset($_POST['termsAgree']) && ($_POST['termsAgree'] === 'on' || $_POST['termsAgree'] === 'yes' || $_POST['termsAgree'] === '1'));
if (!$data['accepted_terms']) {
    $errors[] = 'Terms not accepted.';
}

// Files expected
$fileFields = [
    'journal_publication_format',
    'notarized_copyright',
    'receipt_payment',
    'full_manuscript',
    'notarized_coauthorship',
    'approval_sheet',
    'record_copyright'
];

$uploadDir = app_path('uploads');
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

$storedFiles = [];
foreach ($fileFields as $ff) {
    if (!isset($_FILES[$ff]) || $_FILES[$ff]['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error: $ff";
        continue;
    }
    $fileInfo = $_FILES[$ff];
    // Simple MIME/type safeguard (basic)
    $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $errors[] = "$ff must be a PDF.";
        continue;
    }
    $safeName = $ff . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
    $dest = $uploadDir . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($fileInfo['tmp_name'], $dest)) {
        $errors[] = "Failed to store $ff";
    } else {
        $storedFiles[$ff] = $safeName;
    }
}

if ($errors) {
    http_response_code(400);
    // Attempt a safe referrer fallback
    $back = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') : asset_url('index.php');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Submission Errors</title>';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">';
    echo '<div class="container py-5"><div class="alert alert-danger shadow-sm"><h4 class="alert-heading mb-3">Submission Errors</h4><ul class="mb-3">';
    foreach ($errors as $e) { echo '<li>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</li>'; }
    echo '</ul><a class="btn btn-sm btn-secondary" href="' . $back . '">Go Back</a> ';
    echo '<a class="btn btn-sm btn-outline-primary" href="' . asset_url('index.php') . '">Home</a></div></div></body></html>';
    // Cleanup any stored files if partial failure
    foreach ($storedFiles as $sf) { @unlink($uploadDir . DIRECTORY_SEPARATOR . $sf); }
    exit;
}

// For now: append a log line (could be DB insert in future)
$logLine = date('c') . ' | SUBMISSION | ' . json_encode([
    'user_id' => $_SESSION['user_id'] ?? null,
    'data' => $data,
    'files' => $storedFiles
]) . PHP_EOL;
file_put_contents(app_path('audit.log'), $logLine, FILE_APPEND);

// Minimal success page
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Submission Received | PUP e-IPMO</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="alert alert-success shadow-sm">
      <h4 class="alert-heading">Submission Received</h4>
      <p>Your documents were uploaded successfully and are pending evaluation.</p>
      <hr />
      <p class="mb-0"><a class="btn btn-sm btn-primary" href="<?php echo asset_url('User/Afterlogin/student-application.php'); ?>">View My Applications</a>
      <a class="btn btn-sm btn-secondary ms-2" href="<?php echo asset_url('index.php'); ?>">Return Home</a></p>
    </div>
  </div>
</body>
</html>
