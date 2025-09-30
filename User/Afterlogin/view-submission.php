<?php

require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

$submission_code = $_GET['code'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$isModal = isset($_GET['modal']) && $_GET['modal'] == 1;

// Fetch the submission using submission_code
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE submission_code = ? AND user_id = ?");
$stmt->execute([$submission_code, $user_id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    if ($isModal) {
        echo '<div class="alert alert-danger">Submission not found or access denied.</div>';
        exit;
    } else {
        echo "Submission not found or access denied.";
        exit;
    }
}

// Now use the found submission_id for authors/files
$submission_id = $submission['submission_id'];

// Fetch authors
$authors_stmt = $pdo->prepare("SELECT * FROM submission_authors WHERE submission_id = ?");
$authors_stmt->execute([$submission_id]);
$authors = $authors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch files
$files_stmt = $pdo->prepare("SELECT * FROM submission_documents WHERE submission_id = ?");
$files_stmt->execute([$submission_id]);
$files = $files_stmt->fetchAll(PDO::FETCH_ASSOC);

// Notes feature disabled on user side: no retrieval or rendering
$notes = [];
$noteSaved = null;

function pup_modal_body($submission, $authors, $files, $notes = [], $noteSaved = null) {
?>
<div class="container py-2">
  <div class="mb-4">
    <h5 class="text-center text-primary fw-bold mb-3">Student Information</h5>
    <div class="d-flex justify-content-center">
      <dl class="row w-75">
        <dt class="col-sm-5 text-end fw-bold">Name :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['middle_name'] . ' ' . $submission['last_name']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Student Number :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['student_number']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Email :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['webmail']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Home Address :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['home_address']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Mobile :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['mobile_number']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Campus :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['campus']); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Program :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['program']); ?></dd>

  <dt class="col-sm-5 text-end fw-bold">Academic Level :</dt>
  <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['academic_level']); ?></dd>

  <dt class="col-sm-5 text-end fw-bold">Type (Work Classification) :</dt>
  <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['work_classification'] ?? ''); ?></dd>

        <dt class="col-sm-5 text-end fw-bold">Date Accomplished :</dt>
        <dd class="col-sm-7 mb-2"><?php echo htmlspecialchars($submission['date_accomplished']); ?></dd>
      </dl>
    </div>
    
  </div>
  <div class="mb-4">
    <h5 class="text-center text-primary fw-bold mb-3">Authors</h5>
    <ul class="list-group list-group-flush ms-3" id="authorsList">
      <?php foreach ($authors as $idx => $author): ?>
        <li class="list-group-item border-0 ps-0">
          <div class="d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
              <?php echo htmlspecialchars($author['first_name'] . ' ' . $author['middle_name'] . ' ' . $author['last_name']); ?>
              <?php if ($author['is_adviser']) echo '<span class="badge bg-warning text-dark ms-1">Adviser</span>'; ?>
            </span>
            <button class="btn btn-outline-primary btn-sm toggle-author-details" data-idx="<?php echo $idx; ?>">
              Show Details
            </button>
          </div>
          <div class="author-details mt-2 ms-2" id="author-details-<?php echo $idx; ?>" style="display:none; text-align:left;">
            <?php if ($author['student_id']): ?>
              <div><strong>Student Number:</strong> <?php echo htmlspecialchars($author['student_id']); ?></div>
            <?php endif; ?>
            <?php if ($author['webmail']): ?>
              <div><strong>Email:</strong> <?php echo htmlspecialchars($author['webmail']); ?></div>
            <?php endif; ?>
            <?php if ($author['mobile']): ?>
              <div><strong>Mobile:</strong> <?php echo htmlspecialchars($author['mobile']); ?></div>
            <?php endif; ?>
            <?php if ($author['home_address']): ?>
              <div><strong>Address:</strong> <?php echo htmlspecialchars($author['home_address']); ?></div>
            <?php endif; ?>
            <div><strong>Role:</strong> <?php echo $author['is_adviser'] ? 'Adviser' : 'Author'; ?></div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div>
    <h5 class="text-center text-primary fw-bold mb-3">Files</h5>
    <div class="d-flex flex-column align-items-center">
      <ul class="list-unstyled w-75">
        <?php foreach ($files as $file): 
          $fileUrl = asset_url('uploads/' . $file['file_path']);
        ?>
          <li class="mb-2" data-doc-type="<?php echo htmlspecialchars($file['doc_type']); ?>">
            <div class="d-flex justify-content-between align-items-center gap-2">
              <span class="fw-semibold text-capitalize flex-grow-1"><?php echo htmlspecialchars(str_replace('_', ' ', $file['doc_type'])); ?></span>
              <div class="btn-group btn-group-sm" role="group" aria-label="File actions">
                <a href="<?php echo $fileUrl; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary" title="Open file in a new tab">View File</a>
                <a href="<?php echo $fileUrl; ?>" download class="btn btn-outline-primary" title="Download this file">Download File</a>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div id="reuploadControls" class="w-75 mt-3"></div>
    </div>
  </div>
  <!-- Note feature removed on user side -->
</div>
<?php
}

if ($isModal) {
  pup_modal_body($submission, $authors, $files, $notes, $noteSaved);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submission Details | PUP e-IPMO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .author-adviser { color: #800000; font-weight: bold; }
        .btn-pup { background: #800000; color: #FFD700; border: none; }
        .btn-pup:hover { background: #FFD700; color: #800000; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Submission Details</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <?php pup_modal_body($submission, $authors, $files, $notes, $noteSaved); ?>
            <a href="student-application.php" class="btn btn-secondary mt-4">Back to My Applications</a>
        </div>
    </div>
</div>

<!-- Author Details Modal -->
<div class="modal fade" id="authorDetailsModal" tabindex="-1" aria-labelledby="authorDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="authorDetailsModalLabel">Author Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="authorDetailsContent">
        <!-- Author info will be loaded here -->
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="javascript/student-application.js"></script>
</body>
</html>