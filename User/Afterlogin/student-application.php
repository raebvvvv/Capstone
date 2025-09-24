<?php 
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php'; // enforce auth and no-cache headers
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="<?php echo asset_url('css/student-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">

</head>
<body>
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
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <main class="container py-4">
    <h1 class="fw-bold mb-2 mt-4" style="font-size:2.5rem;">My Application</h1>
    <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(Student)</p>
   
 <div class="d-flex justify-content-center mb-3 gap-2">
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Ethics Clearance</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Patent</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Industrial Design</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Utility Model</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Trademark</button></a>
    <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Copyright</button></a>

  </div>
    <ul class="nav nav-tabs mb-3" id="applicationTabs">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#approved">Approved</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#completed">Completed</a></li>
    </ul>
    <div class="tab-content mt-3">
      <!-- Pending Tab -->
      <div class="tab-pane fade show active" id="pending">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Classification</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
<?php
$user_id = $_SESSION['user_id'] ?? 0;
  $status_labels = [
    'pending_review'   => 'Pending',
    'under_review'     => 'Under Review',
    'revision_needed'  => 'Revision Needed',
    'approved'         => 'Approved',
    'rejected'         => 'Rejected',
    'completed'        => 'Completed',
    // add more as needed
];
// Fetch submissions for this user
$stmt = $pdo->prepare("
    SELECT 
        s.submission_code,
        s.student_number,
        s.title,
        s.work_classification,
        s.remarks
    FROM submissions s
    WHERE s.user_id = ? AND s.status = 'pending_review'
    ORDER BY s.created_at DESC
");
$stmt->execute([$user_id]);
$pending_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (count($pending_submissions) === 0): ?>
  <tr>
    <td colspan="5" class="text-center text-muted py-5">
      You have not applied for anything yet.
    </td>

    
  </tr>
<?php else: ?>
  <?php foreach ($pending_submissions as $row): ?>
<tr>
  <td><?php echo htmlspecialchars($row['submission_code']); ?></td>
  <td><?php echo htmlspecialchars($row['student_number']); ?></td>
  <td><?php echo htmlspecialchars($row['title']); ?></td>
  <td><?php echo htmlspecialchars($row['work_classification']); ?></td>
  <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
  <td>
    <!-- Example action: View details -->
    <a href="#" 
       class="btn btn-success btn-sm view-details-btn" 
       data-id="<?php echo htmlspecialchars($row['submission_code']); ?>">
       View Details
    </a>
  </td>
</tr>
  <?php endforeach; ?>
<?php endif; ?>
</tbody>
          </table>
        </div>
      </div>
      <!-- Approved Tab -->
      <div class="tab-pane fade" id="approved">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Remarks</th>
                <th> </th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  You have not applied for anything yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Completed Tab -->
      <div class="tab-pane fade" id="completed">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Remarks</th>
                <th> </th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  You have not applied for anything yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
  
  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>


<div class="modal fade" id="submissionDetailsModal" tabindex="-1" aria-labelledby="submissionDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="submissionDetailsModalLabel">Submission Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="submissionDetailsContent" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/student-application.js'); ?>"></script>
  
</body>
</html>

