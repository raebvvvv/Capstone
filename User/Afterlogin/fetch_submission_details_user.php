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
    'workClassification' => $sub['work_classification'] ?? '',
    'accomplishmentDate' => $sub['date_accomplished'] ?? '',
    'files_list' => $files,
    'additionalAuthors' => $authors,
    'notes' => $notes,
    'canNote' => $canNote,
    // Do not set a banner by default; we will show a success banner only after an actual reupload in client-side flow
    'statusBanner' => ''
  ];
  echo json_encode($resp);
} catch(Throwable $e){
  echo json_encode(['success'=>false,'error'=>'Exception']);
}
?>