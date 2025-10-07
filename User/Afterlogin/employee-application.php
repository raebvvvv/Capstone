<?php 
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php'; // enforce auth and no-cache headers
// Active tab and pagination helpers
$active_tab = isset($_GET['tab']) ? strtolower(trim((string)$_GET['tab'])) : 'pending';
if (!in_array($active_tab, ['pending','approved','completed'], true)) { $active_tab = 'pending'; }
$perPage = 10;
$requested_page_emp = function(string $tab) use ($active_tab): int {
  if ($active_tab === $tab) {
    $p = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    return $p > 0 ? $p : 1;
  }
  return 1;
};
function build_page_url_emp(string $tab, int $page): string {
  $params = $_GET;
  $params['tab'] = $tab;
  $params['page'] = $page;
  $qs = http_build_query($params);
  return htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $qs);
}
function render_pagination_controls_emp(string $tab, int $page, int $pages): void {
  if ($pages <= 1) { return; }
  echo '<nav aria-label="' . htmlspecialchars(ucfirst($tab)) . ' pagination" class="mt-2">';
  echo '<ul class="pagination justify-content-center">';
  $prevDisabled = $page <= 1 ? ' disabled' : '';
  $prevUrl = build_page_url_emp($tab, max(1, $page - 1));
  echo '<li class="page-item' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '">Previous</a></li>';
  for ($i = 1; $i <= $pages; $i++) {
    $active = $i === $page ? ' active' : '';
    $url = build_page_url_emp($tab, $i);
    echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
  }
  $nextDisabled = $page >= $pages ? ' disabled' : '';
  $nextUrl = build_page_url_emp($tab, min($pages, $page + 1));
  echo '<li class="page-item' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '">Next</a></li>';
  echo '</ul>';
  echo '</nav>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="../../Photos/pup-logo.png">
  <link rel="stylesheet" href="<?php echo asset_url('css/student-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/shared-details-modal.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
  <script src="<?php echo asset_url('javascript/shared-details-modal.js'); ?>" defer></script>
</head>
<body data-user-kind="employee">
  <!-- Navbar (uniform across project) -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
  <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="employee-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="employee-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <main class="container py-4">
  <h1 class="fw-bold mb-2 mt-4" style="font-size:2.5rem;">My Application</h1>
  <?php $roleLabel = 'Employee'; ?>
  <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(<?php echo $roleLabel; ?>)</p> 
   
 <div class="d-flex justify-content-center mb-3 gap-2">
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Ethics Clearance</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Patent</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Industrial Design</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Utility Model</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Trademark</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Copyright</button></a>

  </div>
  <ul class="nav nav-tabs mb-3" id="applicationTabs">
      <li class="nav-item"><a class="nav-link <?php echo $active_tab==='pending' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#pending">Pending</a></li>
      <li class="nav-item"><a class="nav-link <?php echo $active_tab==='approved' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#approved">Approved</a></li>
      <li class="nav-item"><a class="nav-link <?php echo $active_tab==='completed' ? 'active' : ''; ?>" data-bs-toggle="tab" href="#completed">Completed</a></li>
    </ul>
    <div class="tab-content mt-3">
      <!-- Pending Tab -->
      <div class="tab-pane fade <?php echo $active_tab==='pending' ? 'show active' : ''; ?>" id="pending">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-nowrap" style="width: 18%;">Request ID</th>
                <th class="text-nowrap" style="width: 16%;">Employee ID</th>
                <th style="width: 45%;">Title of Work</th>
                <th class="text-nowrap" style="width: 14%;">Remarks</th>
                <?php if(isset($_GET['debug']) && $_GET['debug']=='1'): ?>
                  <th class="text-danger">Raw Status</th>
                  <th class="text-danger">Raw Remarks</th>
                  <th class="text-danger">Issue Label</th>
                  <th class="text-danger">Admin Comment</th>
                <?php endif; ?>
                <th class="text-nowrap" style="width: 220px;">Action</th>
              </tr>
            </thead>
            <tbody>
<?php
// Unified fetch for all statuses including approved admin comment so Completed tab mirrors admin view
$user_id = (int)($_SESSION['user_id'] ?? 0);
$pendingRows = $approvedRows = $completedRows = [];
try {
  $sql = "SELECT 
      s.submission_id,
      s.submission_code,
    s.student_number AS employee_number,
      s.title,
      s.work_classification,
      s.status,
      s.remarks,
      s.created_at,
      mp.admin_comment AS pending_admin_comment,
      mp.issue_label   AS pending_issue_label,
      mp.affected_doc_types AS pending_affected_doc_types,
      ma.admin_comment AS approved_admin_comment,
      ma.issue_label   AS approved_issue_label,
      ma.affected_doc_types AS approved_affected_doc_types
    FROM submissions s
    LEFT JOIN submission_incomplete_meta mp ON mp.submission_id = s.submission_id AND mp.scope='pending'
    LEFT JOIN submission_incomplete_meta ma ON ma.submission_id = s.submission_id AND ma.scope='approved'
    WHERE s.user_id = ?
    ORDER BY s.created_at DESC";
  $stAll = $pdo->prepare($sql);
  $stAll->execute([$user_id]);
  $all = $stAll->fetchAll(PDO::FETCH_ASSOC) ?: [];
  foreach($all as $r){
      $st = strtolower((string)$r['status']);
      if(in_array($st,['pending','pending_review','under_review','revision_needed'])){ $pendingRows[] = $r; }
      elseif($st==='approved'){ $approvedRows[] = $r; }
      elseif($st==='completed'){ $completedRows[] = $r; }
  }
} catch(Throwable $e){ $pendingRows = $approvedRows = $completedRows = []; }
// Pagination calculations per tab
$pending_total   = count($pendingRows);
$approved_total  = count($approvedRows);
$completed_total = count($completedRows);

$pending_pages   = max(1, (int)ceil($pending_total / $perPage));
$approved_pages  = max(1, (int)ceil($approved_total / $perPage));
$completed_pages = max(1, (int)ceil($completed_total / $perPage));

$pending_page   = min($pending_pages,  $requested_page_emp('pending'));
$approved_page  = min($approved_pages, $requested_page_emp('approved'));
$completed_page = min($completed_pages,$requested_page_emp('completed'));

$pending_items   = array_slice($pendingRows,   ($pending_page   - 1) * $perPage, $perPage);
$approved_items  = array_slice($approvedRows,  ($approved_page  - 1) * $perPage, $perPage);
$completed_items = array_slice($completedRows, ($completed_page - 1) * $perPage, $perPage);
?>
<?php if (count($pending_items) === 0): ?>
  <tr>
    <td colspan="5" class="text-center text-muted py-5">
      You have not applied for anything yet.
    </td>
  </tr>
<?php else: ?>
  <?php foreach ($pending_items as $row): ?>
  <?php
  $pendingIssue = trim((string)($row['pending_issue_label'] ?? ''));
  $pendingComment = trim((string)($row['pending_admin_comment'] ?? ''));
  $pendingAffected = trim((string)($row['pending_affected_doc_types'] ?? ''));
  ?>
<tr
  data-status="<?php echo htmlspecialchars($row['status']); ?>"
  data-raw-remarks="<?php echo htmlspecialchars($row['remarks']); ?>"
  data-pending-issue="<?php echo htmlspecialchars($pendingIssue); ?>"
  data-pending-comment="<?php echo htmlspecialchars($pendingComment); ?>"
  <?php if($pendingAffected !== ''): ?>data-resubmit-files="<?php echo htmlspecialchars($pendingAffected); ?>"<?php endif; ?>
>
  <?php
    // Pending remarks mapping:
    // - Default: "For Evaluation"
    // - After successful resubmission (status 'pending_review' or 'under_review'), show "Pending Review"
    // - If admin marks incomplete in Pending tab, prefer issue_label mapping; if absent, fall back to s.remarks
    //   Mapping values: "Error in Document" or "Incorrect Document/Upload"
    $stLower = strtolower((string)$row['status']);
    $displayRemark = 'For Evaluation';
    if ($stLower === 'pending_review' || $stLower === 'under_review') {
      $displayRemark = 'Pending Review';
    } else {
      $pIssue = strtolower($pendingIssue);
      if ($pIssue !== '') {
        if (str_contains($pIssue, 'error')) {
          $displayRemark = 'Error in Document/Upload';
        } elseif (str_contains($pIssue, 'incorrect') || str_contains($pIssue, 'upload')) {
          $displayRemark = 'Incorrect Document/Upload';
        }
      } else {
        $rawRemarksLower = strtolower(trim((string)$row['remarks']));
        if ($rawRemarksLower !== '') {
          if (str_contains($rawRemarksLower, 'error')) {
            $displayRemark = 'Error in Document/Upload';
          } elseif (str_contains($rawRemarksLower, 'incorrect') || str_contains($rawRemarksLower, 'upload')) {
            $displayRemark = 'Incorrect Document/Upload';
          }
        }
      }
    }
  ?>
  <td class="text-nowrap"><?php echo htmlspecialchars($row['submission_code']); ?></td>
  <td class="text-nowrap"><?php echo htmlspecialchars($row['employee_number']); ?></td>
  <td>
    <span class="d-inline-block text-truncate" style="max-width: 420px;">
      <?php echo htmlspecialchars($row['title']); ?>
    </span>
  </td>
  <td>
    <span class="text-nowrap" style="white-space: nowrap !important;">
      <?php echo htmlspecialchars($displayRemark); ?>
    </span>
  </td>
  <?php if(isset($_GET['debug']) && $_GET['debug']=='1'): ?>
    <td><code><?php echo htmlspecialchars($row['status']); ?></code></td>
    <td><code><?php echo htmlspecialchars($row['remarks']); ?></code></td>
    <td><code><?php echo htmlspecialchars($pendingIssue); ?></code></td>
    <td><code><?php echo htmlspecialchars($pendingComment); ?></code></td>
  <?php endif; ?>
  <td class="align-middle text-nowrap">
    <div class="d-flex gap-2 align-items-center flex-nowrap justify-content-start">
      <?php if($pendingComment !== ''): ?>
        <?php
          // Include files to be resubmitted in the comments content when available (Pending scope)
          $pendingCommentText = (string)$pendingComment;
          if ($pendingAffected !== '') {
            $parts = array_filter(array_map('trim', explode('|', $pendingAffected)));
            $pretty = array_map(function($s){ return ucwords(str_replace('_',' ', $s)); }, $parts);
            $pendingCommentText = trim($pendingCommentText . "\n\nFile(s) to be resubmitted: " . implode(', ', $pretty));
          }
        ?>
        <a href="#" class="btn btn-outline-secondary btn-sm btn-comments" data-admin-comment="<?php echo htmlspecialchars($pendingCommentText, ENT_QUOTES); ?>">Comments</a>
      <?php else: ?>
        <span class="btn btn-outline-secondary btn-sm invisible">Comments</span>
      <?php endif; ?>
      <a href="#" class="btn btn-success btn-sm view-details-btn" data-id="<?php echo htmlspecialchars($row['submission_code']); ?>" data-resubmit-files="<?php echo htmlspecialchars($pendingAffected, ENT_QUOTES); ?>">View Details</a>
    </div>
  </td>
</tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>
          </table>
        </div>
        <?php render_pagination_controls_emp('pending', $pending_page, $pending_pages); ?>
      </div>
      <!-- Approved Tab -->
      <div class="tab-pane fade <?php echo $active_tab==='approved' ? 'show active' : ''; ?>" id="approved">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-nowrap" style="width: 18%;">Request ID</th>
                <th class="text-nowrap" style="width: 16%;">Employee ID</th>
                <th style="width: 45%;">Title of Work</th>
                <th class="text-nowrap" style="width: 14%;">Remarks</th>
                <th style="width: 1%"> </th>
                <th class="text-nowrap" style="width: 220px;">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if(count($approved_items)===0): ?>
              <tr><td colspan="6" class="text-center text-muted py-5">No approved applications.</td></tr>
            <?php else: foreach($approved_items as $row): ?>
              <?php $approvedAffected = trim((string)($row['approved_affected_doc_types'] ?? '')); ?>
              <?php $approvedIssue = trim((string)($row['approved_issue_label'] ?? '')); ?>
              <?php
                // Attempt to fetch/display employee's name (fallback to session fields if present)
                $employeeName = '';
                if(isset($_SESSION['first_name'])){
                  $employeeName = trim($_SESSION['first_name'].' '.($_SESSION['last_name'] ?? ''));
                }
                $reqDate = '';
                if(!empty($row['created_at'])){
                  try { $reqDate = date('F d, Y', strtotime($row['created_at'])); } catch(Throwable $e){ $reqDate=''; }
                }
                // Approved remarks mapping:
                // - Default: "For Physical Submission"
                // - If admin incompletes in Approved tab, map to one of:
                //   "Missing Document", "Error in Document", or "Documents don't match"
                $approvedRemark = 'For Physical Submission';
                $aIssue = strtolower(trim($approvedIssue));
                if ($aIssue !== '') {
                  // Normalize curly apostrophes to straight apostrophe
                  $aIssueNorm = str_replace(array("\xE2\x80\x99", '’'), "'", $aIssue);
                  if (str_contains($aIssueNorm, 'missing')) {
                    $approvedRemark = 'Missing Document';
                  } elseif (str_contains($aIssueNorm, 'error')) {
                    $approvedRemark = 'Error in Document';
                  } elseif (
                    str_contains($aIssueNorm, "don't match") ||
                    str_contains($aIssueNorm, 'dont match') ||
                    str_contains($aIssueNorm, 'do not match') ||
                    str_contains($aIssueNorm, "doesn't match") ||
                    str_contains($aIssueNorm, 'does not match') ||
                    str_contains($aIssueNorm, 'mismatch') ||
                    str_contains($aIssueNorm, 'mismatched')
                  ) {
                    $approvedRemark = "Documents don't match";
                  }
                }
              ?>
              <tr<?php if($approvedAffected !== ''): ?> data-resubmit-files="<?php echo htmlspecialchars($approvedAffected, ENT_QUOTES); ?>"<?php endif; ?>>
                <td class="text-nowrap"><?php echo htmlspecialchars($row['submission_code']); ?></td>
                <td class="text-nowrap"><?php echo htmlspecialchars($row['employee_number']); ?></td>
                <td>
                  <span class="d-inline-block text-truncate" style="max-width: 420px;">
                    <?php echo htmlspecialchars($row['title']); ?>
                  </span>
                </td>
                <td>
                  <span class="text-nowrap" style="white-space: nowrap !important;">
                    <?php echo htmlspecialchars($approvedRemark); ?>
                  </span>
                </td>
                <td></td>
                <td class="align-middle text-nowrap">
                  <div class="d-flex gap-2 align-items-center flex-nowrap justify-content-start">
                    <a href="#" class="btn btn-success btn-sm view-details-btn" data-id="<?php echo htmlspecialchars($row['submission_code']); ?>"<?php if($approvedAffected !== ''): ?> data-resubmit-files="<?php echo htmlspecialchars($approvedAffected, ENT_QUOTES); ?>"<?php endif; ?>>View Details</a>
                    <!-- Request ID modal trigger button -->
                    <button type="button" class="btn btn-outline-dark btn-sm btn-request-id" data-request-id="<?php echo htmlspecialchars($row['submission_code']); ?>" data-request-date="<?php echo htmlspecialchars($reqDate); ?>" data-student-name="<?php echo htmlspecialchars($employeeName); ?>">Request ID</button>
                    <?php if(!empty($row['approved_admin_comment'])): ?>
                      <?php
                        // Include files to be resubmitted in the comments content when available
                        $commentText = (string)$row['approved_admin_comment'];
                        if ($approvedAffected !== '') {
                          $aParts = array_filter(array_map('trim', explode('|', $approvedAffected)));
                          $aPretty = array_map(function($s){ return ucwords(str_replace('_',' ', $s)); }, $aParts);
                          $commentText = trim($commentText . "\n\nFile(s) to be resubmitted: " . implode(', ', $aPretty));
                        }
                      ?>
                      <a href="#" class="btn btn-outline-secondary btn-sm btn-comments" data-admin-comment="<?php echo htmlspecialchars($commentText, ENT_QUOTES); ?>">Comments</a>
                    <?php else: ?>
                      <span class="btn btn-outline-secondary btn-sm invisible">Comments</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
  </div>
  <?php render_pagination_controls_emp('approved', $approved_page, $approved_pages); ?>
      <!-- Completed Tab -->
      <div class="tab-pane fade <?php echo $active_tab==='completed' ? 'show active' : ''; ?>" id="completed">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-nowrap" style="width: 18%;">Request ID</th>
                <th class="text-nowrap" style="width: 16%;">Employee ID</th>
                <th style="width: 45%;">Title of Work</th>
                <th class="text-nowrap" style="width: 14%;">Remarks</th>
                <th style="width: 1%"> </th>
                <th class="text-nowrap" style="width: 220px;">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if(count($completed_items)===0): ?>
              <tr><td colspan="6" class="text-center text-muted py-5">No completed applications.</td></tr>
            <?php else: foreach($completed_items as $row): ?>
              <tr<?php if(!empty($row['approved_admin_comment'])) echo ' data-admin-comment="'.htmlspecialchars($row['approved_admin_comment'], ENT_QUOTES).'"'; ?>>
                <td class="text-nowrap"><?php echo htmlspecialchars($row['submission_code']); ?></td>
                <td class="text-nowrap"><?php echo htmlspecialchars($row['employee_number']); ?></td>
                <td>
                  <span class="d-inline-block text-truncate" style="max-width: 420px;">
                    <?php echo htmlspecialchars($row['title']); ?>
                  </span>
                </td>
                <td>
                  <span class="text-nowrap" style="white-space: nowrap !important;">Complete</span>
                </td>
                <td></td>
                <td class="align-middle text-nowrap">
                  <div class="d-flex gap-2 align-items-center flex-nowrap justify-content-start">
                    <a href="#" class="btn btn-success btn-sm view-details-btn" data-id="<?php echo htmlspecialchars($row['submission_code']); ?>">View Details</a>
                    <?php if(!empty($row['approved_admin_comment'])): ?>
                      <a href="#" class="btn btn-outline-secondary btn-sm btn-comments">Comments</a>
                    <?php else: ?>
                      <span class="btn btn-outline-secondary btn-sm invisible">Comments</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
        <?php render_pagination_controls_emp('completed', $completed_page, $completed_pages); ?>
      </div>
    </div>
  </main>



<div class="modal fade" id="submissionDetailsModal" tabindex="-1" aria-labelledby="submissionDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="submissionDetailsModalLabel">Submission Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="submissionDetailsContent" class="text-center ">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Comments Modal (placed at root, not nested) -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Comments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea id="comment_text" class="form-control" rows="6" readonly style="resize:none;"></textarea>
      </div>
    </div>
  </div>
</div>

<!-- Request ID Modal -->
<div class="modal fade" id="requestIdModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Request ID</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="requestIdCard" class="border p-3" style="font-size:0.95rem;">
          <h6 class="text-center fw-bold mb-3">REQUEST ID</h6>
          <p class="mb-1"><strong>Request ID:</strong> <span id="rid_value"></span></p>
          <p class="mb-1"><strong>Name:</strong> <span id="rid_name"></span></p>
          <p class="mb-0"><strong>Date:</strong> <span id="rid_date"></span></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="downloadRequestIdBtn" style="background:#6f42c1;border-color:#6f42c1;">Download as PDF</button>
      </div>
    </div>
  </div>
</div>

  
  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>

<!-- JS handlers moved to external student-application.js to satisfy CSP (no inline scripts). -->

  <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script src="<?php echo asset_url('javascript/student-application.js'); ?>"></script>
</body>
</html>

