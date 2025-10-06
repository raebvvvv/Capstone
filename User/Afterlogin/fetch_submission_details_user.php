<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json');

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
      'url' => asset_url('uploads/'.$row['file_path']),
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

  $resp = [
    'success' => true,
    'submissionCode' => $sub['submission_code'],
    'studentName' => trim(($sub['first_name']??'').' '.($sub['middle_name']??'').' '.($sub['last_name']??'')),
    'studentNumber' => $sub['student_number'] ?? '',
    'email' => $sub['webmail'] ?? '',
    'homeAddress' => $sub['home_address'] ?? '',
    'campus' => $sub['campus'] ?? '',
    'college' => $sub['college'] ?? '',
    'program' => $sub['program'] ?? '',
    'academicLevel' => $sub['academic_level'] ?? '',
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