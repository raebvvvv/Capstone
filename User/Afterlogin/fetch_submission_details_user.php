<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json');
// Prevent caching so details always reflect latest server-side fallbacks
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

try {
  $code = trim((string)($_GET['code'] ?? $_POST['code'] ?? ''));
  if ($code === '') { echo json_encode(['success'=>false,'error'=>'Missing code']); exit; }
  $user_id = (int)($_SESSION['user_id'] ?? 0);
  $stmt = $pdo->prepare('SELECT * FROM submissions WHERE submission_code = ? AND user_id = ? LIMIT 1');
  $stmt->execute([$code, $user_id]);
  $sub = $stmt->fetch(PDO::FETCH_ASSOC);
  if(!$sub){ echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
  $sid = (int)$sub['submission_id'];

  // Authors
  $a = $pdo->prepare('SELECT first_name, middle_name, last_name, student_id, mobile, home_address, webmail, is_adviser FROM submission_authors WHERE submission_id = ? ORDER BY is_adviser DESC, first_name ASC');
  $a->execute([$sid]);
  $authors = [];
  foreach($a->fetchAll(PDO::FETCH_ASSOC) as $row){
    $authors[] = [
      'name' => trim(($row['first_name']??'').' '.($row['middle_name']??'').' '.($row['last_name']??'')),
      'studentNumber' => $row['student_id'] ?? '',
      'email' => $row['webmail'] ?? '',
      'phone' => $row['mobile'] ?? '',
      'address' => $row['home_address'] ?? '',
      'is_adviser' => (int)($row['is_adviser'] ?? 0)
    ];
  }

  // Files
  $f = $pdo->prepare('SELECT doc_type, file_path, file_size, mime_type, verified FROM submission_documents WHERE submission_id = ? ORDER BY doc_type ASC');
  $f->execute([$sid]);
  $files = [];
  foreach($f->fetchAll(PDO::FETCH_ASSOC) as $row){
    $files[] = [
      'label' => ucwords(str_replace(['_','-'],' ', (string)$row['doc_type'])),
  // Serve via secure download endpoint scoped to the current user's submission
  'url' => asset_url('User/Afterlogin/download_document.php?id=' . urlencode((string)$sid) . '&type=' . urlencode((string)$row['doc_type'])),
      'size' => isset($row['file_size']) ? (int)$row['file_size'] : null,
      'verified' => isset($row['verified']) ? (int)$row['verified'] : 0,
      'type' => (string)$row['doc_type']
    ];
  }

  // Notes
  $notes = [];
  try {
    $n = $pdo->prepare('SELECT note, created_at FROM submission_notes WHERE submission_id = ? ORDER BY created_at DESC');
    $n->execute([$sid]);
    $notes = $n->fetchAll(PDO::FETCH_ASSOC) ?: [];
  } catch (Throwable $e) { $notes = []; }

  $status = strtolower((string)($sub['status'] ?? ''));
  $allowStatuses = ['pending','pending_review','under_review','revision_needed','approved'];
  $canNote = in_array($status, $allowStatuses, true);

  // IPMO comments (issue/comment/affected) for user side
  $ipmoIssuePending = $ipmoCommentPending = $ipmoAffectedPending = '';
  $ipmoIssueApproved = $ipmoCommentApproved = $ipmoAffectedApproved = '';
  try {
    // Ensure meta table exists to avoid join errors on fresh DBs
    $pdo->exec("CREATE TABLE IF NOT EXISTS submission_incomplete_meta (
      submission_id INT NOT NULL,
      scope ENUM('pending','approved') NOT NULL,
      issue_label VARCHAR(150) DEFAULT NULL,
      admin_comment TEXT NULL,
      affected_doc_types TEXT NULL,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (submission_id, scope),
      CONSTRAINT fk_sim_submission_user FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    // Fetch both scopes in one go
    $m = $pdo->prepare('SELECT scope, issue_label, admin_comment, affected_doc_types FROM submission_incomplete_meta WHERE submission_id = ?');
    $m->execute([$sid]);
    foreach ($m->fetchAll(PDO::FETCH_ASSOC) as $mr) {
      if (($mr['scope'] ?? '') === 'pending') {
        $ipmoIssuePending = trim((string)$mr['issue_label']);
        $ipmoCommentPending = trim((string)$mr['admin_comment']);
        $ipmoAffectedPending = trim((string)$mr['affected_doc_types']);
      } elseif (($mr['scope'] ?? '') === 'approved') {
        $ipmoIssueApproved = trim((string)$mr['issue_label']);
        $ipmoCommentApproved = trim((string)$mr['admin_comment']);
        $ipmoAffectedApproved = trim((string)$mr['affected_doc_types']);
      }
    }
  } catch (Throwable $e) { /* ignore; leave as empty */ }

  // Choose what to show based on status with sensible fallbacks
  $completionRemark = trim((string)($sub['remarks'] ?? ''));
  $issueSrc = '';
  $affectedSrc = '';
  $commentSrc = '';
  if ($status === 'approved') {
    $issueSrc = $ipmoIssueApproved !== '' ? $ipmoIssueApproved : $ipmoIssuePending;
    $affectedSrc = $ipmoAffectedApproved !== '' ? $ipmoAffectedApproved : $ipmoAffectedPending;
    $commentSrc = $ipmoCommentApproved !== '' ? $ipmoCommentApproved : ($ipmoCommentPending !== '' ? $ipmoCommentPending : $completionRemark);
  } elseif ($status === 'completed') {
    $issueSrc = $ipmoIssueApproved; // show latest known issue context
    $affectedSrc = $ipmoAffectedApproved;
    $commentSrc = $completionRemark !== '' ? $completionRemark : ($ipmoCommentApproved !== '' ? $ipmoCommentApproved : $ipmoCommentPending);
  } else { // pending and others
    $issueSrc = $ipmoIssuePending;
    $affectedSrc = $ipmoAffectedPending;
    $commentSrc = $ipmoCommentPending;
  }
  $hasIpmoComments = ($issueSrc !== '' || $affectedSrc !== '' || $commentSrc !== '');

  // Determine adviser name for user side:
  // 1) submissions.adviser string, else 2) submissions.adviser_id via advisers table, else 3) first co-author with is_adviser=1
  $adviserName = trim((string)($sub['adviser'] ?? ''));
  if ($adviserName === '') {
    $adviserId = isset($sub['adviser_id']) ? (int)$sub['adviser_id'] : 0;
    if ($adviserId > 0) {
      try {
        $stmtAdv = $pdo->prepare('SELECT first_name, middle_name, last_name FROM advisers WHERE adviser_id = ? LIMIT 1');
        $stmtAdv->execute([$adviserId]);
        if ($row = $stmtAdv->fetch(PDO::FETCH_ASSOC)) {
          $adviserName = trim(($row['first_name']??'').' '.($row['middle_name']??'').' '.($row['last_name']??''));
        }
      } catch (Throwable $e) { /* ignore missing table/column */ }
    }
  }
  if ($adviserName === '') {
    foreach ($authors as $a) {
      if ((int)($a['is_adviser'] ?? 0) === 1) { $adviserName = $a['name'] ?? ''; if ($adviserName !== '') break; }
    }
  }

  // Robust academic level resolution with fallbacks
  $academicLevelOut = trim((string)($sub['academic_level'] ?? ''));
  if ($academicLevelOut === '') {
    // Try employee profile
    try {
      $p = $pdo->prepare('SELECT academic_level FROM employee_profiles WHERE user_id = ? LIMIT 1');
      $p->execute([$user_id]);
      $profLevel = $p->fetchColumn();
      if (!empty($profLevel)) { $academicLevelOut = trim((string)$profLevel); }
    } catch (Throwable $e) { /* ignore missing table/col */ }
    // Infer Not Studying when program was explicitly N/A (case-insensitive)
    if ($academicLevelOut === '') {
      $prog = trim((string)($sub['program'] ?? ''));
      if ($prog !== '' && strcasecmp($prog, 'N/A') === 0) { $academicLevelOut = 'Not Studying'; }
    }
    // As a last resort, infer from program naming patterns
    if ($academicLevelOut === '') {
      $prog = trim((string)($sub['program'] ?? ''));
      if ($prog === '') {
        // Try to use employee profile program for inference
        try {
          $pp = $pdo->prepare('SELECT program FROM employee_profiles WHERE user_id = ? LIMIT 1');
          $pp->execute([$user_id]);
          $pProg = $pp->fetchColumn();
          if (!empty($pProg)) { $prog = trim((string)$pProg); }
        } catch (Throwable $e) { /* ignore */ }
      }
      if ($prog !== '' && strcasecmp($prog, 'N/A') !== 0) {
        $pl = strtolower($prog);
        if (strpos($pl, 'doctor') !== false || strpos($pl, 'phd') !== false || strpos($pl, "d.") !== false) {
          $academicLevelOut = 'Doctorate';
        } elseif (strpos($pl, 'master') !== false || preg_match('/\bma\b|\bmba\b|\bms\b|\bmpa\b|\bmem\b|\bmit\b/i', $prog)) {
          $academicLevelOut = 'Masters';
        } elseif (strpos($pl, 'open university') !== false) {
          $academicLevelOut = 'Open University';
        } else {
          $academicLevelOut = 'Undergraduate';
        }
      }
    }
  }

  // Resolve Department with fallbacks (submission column first, then employee profile)
  $departmentOut = trim((string)($sub['department'] ?? ''));
  if ($departmentOut === '') {
    try {
      $p = $pdo->prepare('SELECT department FROM employee_profiles WHERE user_id = ? LIMIT 1');
      $p->execute([$user_id]);
      $profDept = $p->fetchColumn();
      if (!empty($profDept)) { $departmentOut = trim((string)$profDept); }
    } catch (Throwable $e) { /* ignore missing table/col */ }
  }

  $resp = [
    'success' => true,
    'submissionCode' => $sub['submission_code'],
    'studentName' => trim(($sub['first_name']??'').' '.($sub['middle_name']??'').' '.($sub['last_name']??'')),
    'studentNumber' => $sub['student_number'] ?? '',
    'email' => $sub['webmail'] ?? '',
    'homeAddress' => $sub['home_address'] ?? '',
    'campus' => $sub['campus'] ?? '',
    'college' => $sub['college'] ?? '',
    'department' => $departmentOut,
    'program' => $sub['program'] ?? '',
  'academicLevel' => $academicLevelOut,
  // provide snake_case alias for maximal compatibility across clients
  'academic_level' => $academicLevelOut,
    'documentTitle' => $sub['title'] ?? '',
  'adviser' => $adviserName,
    'adviser_coauthor' => isset($sub['adviser_coauthor']) ? (int)$sub['adviser_coauthor'] : 0,
    'workClassification' => $sub['work_classification'] ?? '',
    'accomplishmentDate' => $sub['date_accomplished'] ?? '',
    'files_list' => $files,
    'additionalAuthors' => $authors,
    'notes' => $notes,
    'canNote' => $canNote,
    'ipmoComments' => [
      'issue' => $issueSrc,
      'affected' => $affectedSrc,
      'comment' => $commentSrc,
      'has' => $hasIpmoComments
    ],
    // Do not set a banner by default; we will show a success banner only after an actual reupload in client-side flow
    'statusBanner' => ''
  ];
  echo json_encode($resp);
} catch(Throwable $e){
  echo json_encode(['success'=>false,'error'=>'Exception']);
}
?>