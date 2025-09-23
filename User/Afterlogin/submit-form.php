<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
require_once __DIR__ . '/../../includes/debug_helpers.php';

// Setup error logging
setupErrorLogging();

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

// Required scalar fields
$requiredFields = [
    'first_name','last_name','student_number','home_address','mobile_number','webmail',
    'campus','academicLevel','program','workClassification','title','date_accomplished'
    // 'college' is handled separately below
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

// Special handling for college
$academicLevel = $_POST['academicLevel'] ?? '';
$college = trim($_POST['college'] ?? '');

if ($academicLevel === 'Undergraduate') {
    if ($college === '' || $college === 'N/A') {
        $errors[] = "Missing required field: college";
    } else {
        $data['college'] = $college;
    }
} else {
    // For Masters, Doctorate, Open University, accept "N/A" as valid
    $data['college'] = $college !== '' ? $college : 'N/A';
}

// Adviser / authors handling
$data['adviser'] = trim($_POST['adviser'] ?? '');
$data['adviser_Coauthor'] = isset($_POST['adviser_Coauthor']);
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

if (empty($errors)) {
    try {
        $pdo->beginTransaction();

        // --- Adviser handling: insert/get adviser_id ---
        $adviserFullName = trim($_POST['adviser'] ?? '');
        $nameParts = preg_split('/\s+/', $adviserFullName);
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
        $middleName = count($nameParts) > 1 ? implode(' ', $nameParts) : '';

        $adviserStmt = $pdo->prepare("SELECT adviser_id FROM advisers WHERE first_name = ? AND last_name = ? AND middle_name = ?");
        $adviserStmt->execute([$firstName, $lastName, $middleName]);
        $adviser_id = $adviserStmt->fetchColumn();

        if (!$adviser_id) {
            $insertAdviser = $pdo->prepare("INSERT INTO advisers (first_name, middle_name, last_name) VALUES (?, ?, ?)");
            $insertAdviser->execute([$firstName, $middleName, $lastName]);
            $adviser_id = $pdo->lastInsertId();
        }

        // Insert main submission
        $stmt = $pdo->prepare("
            INSERT INTO submissions (
                user_id, first_name, middle_name, last_name, 
                student_number, home_address, mobile_number, 
                webmail, campus, academic_level, college, 
                program, work_classification, title, adviser_id,
                date_accomplished, accepted_terms, status
            ) VALUES (
                :user_id, :first_name, :middle_name, :last_name,
                :student_number, :home_address, :mobile_number,
                :webmail, :campus, :academic_level, :college,
                :program, :work_classification, :title, :adviser_id,
                :date_accomplished, :accepted_terms, 'pending_review'
            )
        ");

        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'first_name' => $data['first_name'],
            'middle_name' => $_POST['middle_name'] ?? '',
            'last_name' => $data['last_name'],
            'student_number' => $data['student_number'],
            'home_address' => $data['home_address'],
            'mobile_number' => $data['mobile_number'],
            'webmail' => $data['webmail'],
            'campus' => $data['campus'],
            'academic_level' => $data['academicLevel'],
            'college' => $data['college'],
            'program' => $data['program'],
            'work_classification' => $data['workClassification'],
            'title' => $data['title'],
            'adviser_id' => $adviser_id,
            'date_accomplished' => $data['date_accomplished'],
            'accepted_terms' => $data['accepted_terms'] ? 1 : 0
        ]);

        $submission_id = $pdo->lastInsertId();

        // Insert adviser as co-author if checkbox is checked
        if (isset($_POST['adviser_Coauthor']) && $adviser_id) {
            $authorStmt = $pdo->prepare("
                INSERT INTO submission_authors (
                    submission_id, adviser_id, first_name, middle_name, last_name, is_adviser, role
                ) VALUES (?, ?, ?, ?, ?, 1, 'Adviser')
            ");
            $authorStmt->execute([
                $submission_id,
                $adviser_id,
                $firstName,
                $middleName,
                $lastName
            ]);
        }

        // Insert documents
        $docStmt = $pdo->prepare("
            INSERT INTO submission_documents (
                submission_id, doc_type, file_path,
                file_size, mime_type
            ) VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($storedFiles as $type => $filename) {
            $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            $docStmt->execute([
                $submission_id,
                $type,
                $filename,
                filesize($filepath),
                mime_content_type($filepath)
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
                        $author['first_name'],
                        $author['middle_name'], // <-- use middle_name
                        $author['last_name'],
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
        // Get adviser name parts for comparison
        $adviserFirstName = $firstName;
        $adviserMiddleName = $middleName;
        $adviserLastName = $lastName;

        if (!empty($_POST['coauthors'])) {
            $authorStmt = $pdo->prepare("
                INSERT INTO submission_authors (
                    submission_id, first_name, middle_name, last_name,
                    student_id, mobile, home_address, webmail,
                    is_adviser
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($_POST['coauthors'] as $coauthor) {
                // Skip if this coauthor is the adviser (already inserted as adviser)
                if (
                    trim(strtolower($coauthor['first_name'])) === trim(strtolower($adviserFirstName)) &&
                    trim(strtolower($coauthor['middle_name'])) === trim(strtolower($adviserMiddleName)) &&
                    trim(strtolower($coauthor['last_name'])) === trim(strtolower($adviserLastName))
                ) {
                    continue;
                }
                $authorStmt->execute([
                    $submission_id,
                    $coauthor['first_name'],
                    $coauthor['middle_name'],
                    $coauthor['last_name'],
                    $coauthor['student_id'],
                    $coauthor['mobile'],
                    $coauthor['home_address'],
                    $coauthor['webmail'],
                    $coauthor['is_adviser'] ? 0 : 1
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
        header('Location: student-application.php?status=success');
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
