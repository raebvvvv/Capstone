<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
require_once __DIR__ . '/../../includes/debug_helpers.php';
require_once __DIR__ . '/../../upload_helpers.php';

// Setup error logging
setupErrorLogging();

// Preflight: detect if PHP dropped POST due to exceeding post_max_size (avoids CSRF false negatives)
if (empty($_POST) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
    $postMaxRaw = ini_get('post_max_size');
    $postMax = 0;
    if ($postMaxRaw) {
        $unit = strtolower(substr($postMaxRaw, -1));
        $num = (float)$postMaxRaw;
        switch ($unit) {
            case 'g': $postMax = (int)($num * 1024 * 1024 * 1024); break;
            case 'm': $postMax = (int)($num * 1024 * 1024); break;
            case 'k': $postMax = (int)($num * 1024); break;
            default:  $postMax = (int)$num; // assume bytes
        }
    }
    if ($postMax > 0 && $contentLength > $postMax) {
        http_response_code(413); // Payload Too Large
        $back = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') : asset_url('index.php');
        $msg = 'Total upload size exceeds server limit (' . htmlspecialchars(ini_get('post_max_size'), ENT_QUOTES, 'UTF-8') . '). Reduce file sizes (max 50MB per file) or contact administrator.';
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Upload Too Large</title>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"></head><body class="bg-light">';
        echo '<div class="container py-5"><div class="alert alert-warning shadow-sm">';
        echo '<h4 class="alert-heading mb-3">Upload Too Large</h4><p class="mb-3">' . $msg . '</p>';
        echo '<a class="btn btn-sm btn-secondary" href="' . $back . '">Go Back</a> ';
        echo '<a class="btn btn-sm btn-outline-primary" href="' . asset_url('index.php') . '">Home</a>';
        echo '</div></div></body></html>';
        exit;
    }
}

// Log form submission
logDebug('Form submitted', [
    'POST' => $_POST,
    'FILES' => $_FILES,
    'SESSION' => $_SESSION
]);

// Basic CSRF protection if helper exists
// Enforce CSRF via existing helper (defined in security_bootstrap)
if (function_exists('verify_csrf_post')) {
    verify_csrf_post();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// Determine role (default to 'student')
$role = $_SESSION['role'] ?? 'student';

// Alias employee_id -> student_number for employees (kept for backward compatibility)
if ($role === 'employee') {
    if (empty($_POST['student_number']) && !empty($_POST['employee_id'])) {
        $_POST['student_number'] = $_POST['employee_id'];
    }
}

// Server-side immutability: override readonly/disabled fields with profile values
$errors = [];
if ($role === 'student') {
    // Fetch full profile for students
    $stmt = $pdo->prepare("SELECT sp.first_name, sp.middle_name, sp.last_name, sp.student_number, sp.home_address, sp.mobile_number, sp.campus, sp.academic_level, sp.college, sp.program, u.email
                           FROM student_profiles sp JOIN users u ON sp.user_id = u.user_id WHERE sp.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $studentProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($studentProfile && !empty($studentProfile['academic_level'])) {
        // Identity and contact
        $_POST['first_name']      = $studentProfile['first_name'] ?? '';
        $_POST['middle_name']     = $studentProfile['middle_name'] ?? '';
        $_POST['last_name']       = $studentProfile['last_name'] ?? '';
        $_POST['student_number']  = $studentProfile['student_number'] ?? '';
        $_POST['home_address']    = $studentProfile['home_address'] ?? '';
        $_POST['mobile_number']   = $studentProfile['mobile_number'] ?? '';
        $_POST['webmail']         = $studentProfile['email'] ?? '';
        // Academic (locked in UI)
        $_POST['academicLevel']   = $studentProfile['academic_level'];
        if (!empty($studentProfile['campus']))  { $_POST['campus']  = $studentProfile['campus']; }
        if (array_key_exists('college', $studentProfile)) { $_POST['college'] = $studentProfile['college'] ?? ''; }
        if (array_key_exists('program', $studentProfile)) { $_POST['program'] = $studentProfile['program'] ?? ''; }
    } else {
        $errors[] = 'Your student profile is incomplete. Please complete your profile and try again.';
    }
} elseif ($role === 'employee') {
    // Fetch full profile for employees
    $stmt = $pdo->prepare("SELECT ep.first_name, ep.middle_name, ep.last_name, ep.employee_number, ep.home_address, ep.mobile_number, ep.campus, ep.academic_level, ep.college, ep.department, ep.program, u.email
                           FROM employee_profiles ep JOIN users u ON ep.user_id = u.user_id WHERE ep.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $empProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($empProfile && !empty($empProfile['employee_number'])) {
        // Identity and contact
        $_POST['first_name']      = $empProfile['first_name'] ?? '';
        $_POST['middle_name']     = $empProfile['middle_name'] ?? '';
        $_POST['last_name']       = $empProfile['last_name'] ?? '';
        // Store employee_number in student_number column on submissions as per schema
        $_POST['student_number']  = $empProfile['employee_number'] ?? '';
        $_POST['home_address']    = $empProfile['home_address'] ?? '';
        $_POST['mobile_number']   = $empProfile['mobile_number'] ?? '';
        $_POST['webmail']         = $empProfile['email'] ?? '';
        // Academic (locked in UI)
        if (!empty($empProfile['academic_level'])) { $_POST['academicLevel'] = $empProfile['academic_level']; }
        if (!empty($empProfile['campus']))  { $_POST['campus']  = $empProfile['campus']; }
        if (array_key_exists('college', $empProfile))    { $_POST['college']    = $empProfile['college'] ?? ''; }
        if (array_key_exists('program', $empProfile))    { $_POST['program']    = $empProfile['program'] ?? ''; }
        // department exists but not stored in submissions table directly
    } else {
        $errors[] = 'Your employee profile is incomplete. Please complete your profile and try again.';
    }
}

// Required scalar fields
$requiredFields = [
    'first_name','last_name','student_number','home_address','mobile_number','webmail',
    'campus','academicLevel','workClassification','title','date_accomplished'
    // 'college' and 'program' handled separately based on role/level
];
$data = [];
foreach ($requiredFields as $f) {
    $val = trim($_POST[$f] ?? '');
    if ($val === '') {
        $errors[] = "Missing required field: $f";
    } else {
        $data[$f] = $val;
    }
}

// Special handling for college
$academicLevel = $_POST['academicLevel'] ?? '';
$college = trim($_POST['college'] ?? '');
$program = trim($_POST['program'] ?? '');

if ($academicLevel === 'Undergraduate') {
    if ($college === '' || $college === 'N/A') {
        $errors[] = "Missing required field: college";
    } else {
        $data['college'] = $college;
    }
} else {
    // For Masters, Doctorate, Open University, or Not Studying, accept N/A
    $data['college'] = $college !== '' ? $college : 'N/A';
}

// Program requirements by role/level
if ($role === 'student') {
    if ($program === '') {
        $errors[] = "Missing required field: program";
    } else {
        $data['program'] = $program;
    }
} else {
    // employee: require program unless Academic Level is Not Studying; if Not Studying, force N/A
    if ($academicLevel === 'Not Studying') {
        $data['program'] = 'N/A';
    } else {
        if ($program === '') {
            $errors[] = "Missing required field: program";
        } else {
            $data['program'] = $program;
        }
    }
}

// Adviser / authors handling
$data['adviser'] = trim($_POST['adviser'] ?? '');
$data['adviser_Coauthor'] = isset($_POST['adviser_Coauthor']);
$data['accepted_terms'] = (($_POST['accepted_terms'] ?? '') === 'yes') || (isset($_POST['termsAgree']) && ($_POST['termsAgree'] === 'on' || $_POST['termsAgree'] === 'yes' || $_POST['termsAgree'] === '1'));
if (!$data['accepted_terms']) {
    $errors[] = 'Terms not accepted.';
}

// Validate date_accomplished is not in the future
if (!empty($data['date_accomplished'])) {
    $ts = strtotime($data['date_accomplished']);
    if ($ts === false || $ts > strtotime('today')) {
        $errors[] = 'Invalid date accomplished.';
    }
}

// Files expected (role-aware)
if ($role === 'employee') {
    // Employees: 'presentation' replaces 'full_manuscript'. Notarized co-authorship is optional.
    $fileFields = [
        'journal_publication_format',
        'notarized_copyright',
        'receipt_payment',
        'presentation',
        // 'notarized_coauthorship' is optional for employees; process later if provided
        'record_copyright'
    ];
} else {
    // Students
    $fileFields = [
        'journal_publication_format',
        'notarized_copyright',
        'receipt_payment',
        'full_manuscript',
        'notarized_coauthorship',
        'approval_sheet',
        'record_copyright'
    ];
}

$uploadDir = storage_path('uploads');
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

$storedFiles = [];
foreach ($fileFields as $ff) {
    if (!isset($_FILES[$ff]) || $_FILES[$ff]['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error: $ff";
        continue;
    }
    // Use secure upload helper which validates PDF content and MIME and sanitizes filename
    $res = secure_upload_file($_FILES[$ff], $ff, $uploadDir);
    if (!$res['success']) {
        foreach ($res['errors'] as $err) { $errors[] = $ff . ': ' . $err; }
    } else {
        $storedFiles[$ff] = $res['filename'];
    }
}

// Handle optional employee notarized_coauthorship if provided
if ($role === 'employee' && isset($_FILES['notarized_coauthorship']) && $_FILES['notarized_coauthorship']['error'] === UPLOAD_ERR_OK) {
    $ff = 'notarized_coauthorship';
    $res = secure_upload_file($_FILES[$ff], $ff, $uploadDir);
    if (!$res['success']) {
        foreach ($res['errors'] as $err) { $errors[] = $ff . ': ' . $err; }
    } else {
        $storedFiles[$ff] = $res['filename'];
    }
}

if ($errors) {
    http_response_code(400);
    $back = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') : asset_url('index.php');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Submission Errors</title>';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"></head><body class="bg-light">';
    echo '<div class="container py-5"><div class="alert alert-danger shadow-sm"><h4 class="alert-heading mb-3">Submission Errors</h4><ul class="mb-3">';
    foreach ($errors as $e) { echo '<li>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</li>'; }
    echo '</ul><a class="btn btn-sm btn-secondary" href="' . $back . '">Go Back</a> ';
    echo '<a class="btn btn-sm btn-outline-primary" href="' . asset_url('index.php') . '">Home</a></div></div></body></html>';
    foreach ($storedFiles as $sf) { @unlink($uploadDir . DIRECTORY_SEPARATOR . $sf); }
    exit;
}

// Append an audit log line
$logLine = date('c') . ' | SUBMISSION | ' . json_encode([
    'user_id' => $_SESSION['user_id'] ?? null,
    'data' => $data,
    'files' => $storedFiles
]) . PHP_EOL;
file_put_contents(app_path('audit.log'), $logLine, FILE_APPEND);

// Helper: title-case capitalization for names (handles hyphens and apostrophes)
function normalize_name($s) {
    $s = trim((string)$s);
    if ($s === '') return '';
    $s = strtolower(preg_replace('/\s+/', ' ', $s));
    $s = preg_replace_callback('/\b([a-z])/', function($m){ return strtoupper($m[1]); }, $s);
    $s = preg_replace_callback('/-([a-z])/', function($m){ return '-'.strtoupper($m[1]); }, $s);
    $s = preg_replace_callback("/'([a-z])/", function($m){ return "'".strtoupper($m[1]); }, $s);
    return $s;
}

// Normalize submitter names (from server-enforced values)
$data['first_name'] = normalize_name($_POST['first_name'] ?? '');
$data['last_name']  = normalize_name($_POST['last_name'] ?? '');
$_POST['middle_name'] = normalize_name($_POST['middle_name'] ?? '');

// Normalize adviser field
$data['adviser'] = normalize_name($data['adviser']);

if (empty($errors)) {
    try {
        $pdo->beginTransaction();

        // --- Adviser handling: insert/get adviser_id ---
    $adviserFullName = trim($data['adviser'] ?? '');
        $nameParts = preg_split('/\s+/', $adviserFullName);
    $firstName = normalize_name($nameParts[0] ?? '');
    $lastName = normalize_name(count($nameParts) > 1 ? array_pop($nameParts) : '');
    $middleName = normalize_name(count($nameParts) > 1 ? implode(' ', $nameParts) : '');

        $adviserStmt = $pdo->prepare("SELECT adviser_id FROM advisers WHERE first_name = ? AND last_name = ? AND middle_name = ?");
        $adviserStmt->execute([$firstName, $lastName, $middleName]);
        $adviser_id = $adviserStmt->fetchColumn();

        if (!$adviser_id) {
            $insertAdviser = $pdo->prepare("INSERT INTO advisers (first_name, middle_name, last_name) VALUES (?, ?, ?)");
            $insertAdviser->execute([$firstName, $middleName, $lastName]);
            $adviser_id = $pdo->lastInsertId();
        }

    // Generate submission_code with role-aware prefix (SRID for students, ERID for employees)
        $today = date('Y-m-d');
        $today_code = date('Ymd');
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $count_today = $stmt->fetchColumn();
        $next_count = $count_today + 1;
    $prefix = ($role === 'employee') ? 'ERID' : 'SRID';
    $submission_code = sprintf('%s-%s-%d', $prefix, date('Y').'-'.$today_code, $next_count);

        // Insert main submission
        $stmt = $pdo->prepare("
            INSERT INTO submissions (
                submission_code, user_id, first_name, middle_name, last_name, 
                student_number, home_address, mobile_number, 
                webmail, campus, academic_level, college, 
                program, work_classification, title, adviser_id,
                date_accomplished, accepted_terms, status
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, 'pending_review'
            )
        ");

        $stmt->execute([
            $submission_code,
            $_SESSION['user_id'],
            $data['first_name'],
            $_POST['middle_name'] ?? '',
            $data['last_name'],
            $data['student_number'],
            $data['home_address'],
            $data['mobile_number'],
            $data['webmail'],
            $data['campus'],
            $data['academicLevel'],
            $data['college'],
            $data['program'],
            $data['workClassification'],
            $data['title'],
            $adviser_id,
            $data['date_accomplished'],
            $data['accepted_terms'] ? 1 : 0
        ]);

        $submission_id = $pdo->lastInsertId();

        // Note: Adviser as co-author is handled via the coauthors array below (with is_adviser flag)
        // No separate insertion needed here

        // Insert documents
        $docStmt = $pdo->prepare("
            INSERT INTO submission_documents (
                submission_id, doc_type, file_path,
                file_size, mime_type
            ) VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($storedFiles as $type => $filename) {
            $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            // Determine MIME type safely even if fileinfo/mime_content_type is unavailable
            $mimeType = 'application/pdf'; // default; uploads are PDFs only
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            // Prefer finfo when available (fileinfo extension)
            if (function_exists('finfo_open')) {
                $fi = @finfo_open(FILEINFO_MIME_TYPE);
                if ($fi) {
                    $detected = @finfo_file($fi, $filepath);
                    if (!empty($detected)) { $mimeType = $detected; }
                    @finfo_close($fi);
                }
            } elseif (function_exists('mime_content_type')) {
                // Older function; may not exist if fileinfo disabled
                $detected = @mime_content_type($filepath);
                if (!empty($detected)) { $mimeType = $detected; }
            } elseif ($ext === 'pdf') {
                $mimeType = 'application/pdf';
            }

            $docStmt->execute([
                $submission_id,
                $type,
                $filename,
                filesize($filepath),
                $mimeType
            ]);
        }

        // Handle authors if present
        // Debug authors data
        error_log('POST data: ' . print_r($_POST, true));

        if (!empty($_POST['authors']) && is_array($_POST['authors'])) {
            error_log('Processing authors array: ' . count($_POST['authors']));
            
            $authorStmt = $pdo->prepare("
                INSERT INTO submission_authors (
                    submission_id, first_name, middle_name, last_name,
                    student_id, mobile, home_address, webmail
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($_POST['authors'] as $author) {
                error_log('Processing author: ' . print_r($author, true));
                
                try {
                    $authorStmt->execute([
                        $submission_id,
                        normalize_name($author['first_name']),
                        normalize_name(isset($author['middle_name']) ? $author['middle_name'] : (isset($author['middle_initial']) ? $author['middle_initial'] : '')),
                        normalize_name($author['last_name']),
                        $author['student_id'],
                        $author['mobile'],
                        $author['home_address'],
                        $author['webmail']
                    ]);
                    error_log('Author inserted successfully');
                } catch (PDOException $e) {
                    error_log('Author insertion failed: ' . $e->getMessage());
                    throw $e;
                }
            }
        } else {
            error_log('No authors data found in POST');
        }

        // Handle coauthors
        if (!empty($_POST['coauthors'])) {
            $authorStmt = $pdo->prepare("
                INSERT INTO submission_authors (
                    submission_id, first_name, middle_name, last_name,
                    student_id, mobile, home_address, webmail,
                    is_adviser
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($_POST['coauthors'] as $coauthor) {
                // Insert all coauthors including advisers (is_adviser flag distinguishes them)
                // Explicitly check for '1' or true to handle both string and boolean values
                $isAdviser = (isset($coauthor['is_adviser']) && ($coauthor['is_adviser'] === '1' || $coauthor['is_adviser'] === 1 || $coauthor['is_adviser'] === true)) ? 1 : 0;
                
                $authorStmt->execute([
                    $submission_id,
                    normalize_name($coauthor['first_name']),
                    normalize_name(isset($coauthor['middle_name']) ? $coauthor['middle_name'] : (isset($coauthor['middle_initial']) ? $coauthor['middle_initial'] : '')),
                    normalize_name($coauthor['last_name']),
                    $coauthor['student_id'],
                    $coauthor['mobile'],
                    $coauthor['home_address'],
                    $coauthor['webmail'],
                    $isAdviser
                ]);
            }
        }

        // Log the complete submission data
        $logData = [
            'user_id' => $_SESSION['user_id'],
            'data' => $_POST,
            'coauthors' => $_POST['coauthors'] ?? [],
            'files' => $storedFiles
        ];
        error_log(date('Y-m-d\TH:i:sP') . ' | SUBMISSION | ' . json_encode($logData));

    $pdo->commit();
    // Redirect based on role
    $target = ($role === 'employee') ? 'employee-application.php' : 'student-application.php';
    header('Location: ' . $target . '?status=success');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        exit;
    }
}

// Minimal success page
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Submission Received | PUP e-IPMO</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="alert alert-success shadow-sm">
      <h4 class="alert-heading">Submission Received</h4>
      <p>Your documents were uploaded successfully and are pending evaluation.</p>
      <hr />
    <p class="mb-0"><a class="btn btn-sm btn-primary" href="<?php echo asset_url('User/Afterlogin/' . ((isset($_SESSION['role']) && $_SESSION['role'] === 'employee') ? 'employee-application.php' : 'student-application.php')); ?>">View My Applications</a>
      <a class="btn btn-sm btn-secondary ms-2" href="<?php echo asset_url('index.php'); ?>">Return Home</a></p>
    </div>
  </div>
</body>
</html>
