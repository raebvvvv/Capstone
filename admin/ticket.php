<?php
// Standard admin bootstrap: config -> conn -> secure_bootstrap -> require_admin
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

$status_labels = [
    'draft'           => 'Draft',
    'pending_review'  => 'Pending',
    'under_review'    => 'Under Review',
    'revision_needed' => 'Revision Needed',
    'approved'        => 'Approved',
    'rejected'        => 'Rejected',
];

// Get filter values from GET
$filters = [
    'submission_id' => isset($_GET['submission_id']) ? trim($_GET['submission_id']) : '',
    'student_number' => isset($_GET['student_number']) ? trim($_GET['student_number']) : '',
    'name' => isset($_GET['name']) ? trim($_GET['name']) : '',
    'title' => isset($_GET['title']) ? trim($_GET['title']) : '',
    'work_classification' => isset($_GET['work_classification']) ? trim($_GET['work_classification']) : '',
    'program' => isset($_GET['program']) ? trim($_GET['program']) : '',
    'created_at' => isset($_GET['created_at']) ? trim($_GET['created_at']) : '',
    'status' => isset($_GET['status']) ? trim($_GET['status']) : '',
    'remarks' => isset($_GET['remarks']) ? trim($_GET['remarks']) : '',
];

// Build WHERE clause
$where = [];
$params = [];
if ($filters['submission_id'] !== '') {
    $where[] = 'submission_id LIKE :submission_id';
    $params['submission_id'] = '%' . $filters['submission_id'] . '%';
}
if ($filters['student_number'] !== '') {
    $where[] = 'student_number LIKE :student_number';
    $params['student_number'] = '%' . $filters['student_number'] . '%';
}
if ($filters['name'] !== '') {
    $where[] = "(first_name LIKE :name OR last_name LIKE :name OR middle_name LIKE :name OR last_name LIKE :name)";
    $params['name'] = '%' . $filters['name'] . '%';
}
if ($filters['title'] !== '') {
    $where[] = 'title LIKE :title';
    $params['title'] = '%' . $filters['title'] . '%';
}
if ($filters['work_classification'] !== '') {
    $where[] = 'work_classification LIKE :work_classification';
    $params['work_classification'] = '%' . $filters['work_classification'] . '%';
}
if ($filters['program'] !== '') {
    $where[] = 'program LIKE :program';
    $params['program'] = '%' . $filters['program'] . '%';
}
if ($filters['created_at'] !== '') {
    $where[] = 'DATE(created_at) = :created_at';
    $params['created_at'] = $filters['created_at'];
}
if ($filters['status'] !== '') {
    $where[] = 'status = :status';
    $params['status'] = $filters['status'];
}
if ($filters['remarks'] !== '') {
    $where[] = 'remarks LIKE :remarks';
    $params['remarks'] = '%' . $filters['remarks'] . '%';
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT * FROM submissions $where_sql ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Fetch admin data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $admin = $stmt->fetch();
    if (!$admin) { echo "Admin not found."; exit(); }
} else { echo "User ID not set in session."; exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/admin-navbar.css">
    <!-- Add your page-specific CSS after this -->
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
    <title>Manage Applications</title>
</head>
<body>
<header class="bg-light border-bottom py-3 shadow-sm" data-admin-email="<?php echo htmlspecialchars($admin['email']); ?>">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="../images/puplogo.png" alt="Logo" class="center-img" style="height: 30px; margin-right: 10px;">
                    <span class="fw-bold">PUP e-IPMO [Admin.]</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center w-100">
                        <li class="nav-item ms-auto"><a class="nav-link" href="admin.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                        <li class="nav-item"><a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='manageuser.php') echo 'fw-bold'; ?>" href="manageuser.php">Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='ticket.php') echo 'fw-bold'; ?>" href="ticket.php">Applications</a></li>
                        <li class="nav-item d-flex align-items-center header-actions ms-lg-3 mt-2 mt-lg-0">
                            <button type="button" class="btn btn-outline-secondary btn-profile" data-bs-toggle="modal" data-bs-target="#adminProfileModal">My Profile</button>
                            <form method="POST" action="logout.php" class="d-inline ms-2">
                                <?php csrf_input(); ?>
                                <button type="submit" class="btn btn-logout btn-logout-nav">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>

<div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminProfileLabel">My Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="adminProfileBody">
                <div class="border rounded p-3 mb-4 bg-light-subtle" style="border-color:#ddd!important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Account Information</h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="profileEditBtn">Edit</button>
                            <button type="button" class="btn btn-outline-secondary d-none" id="profileCancelBtn">Cancel</button>
                        </div>
                    </div>
                    <form id="profileInfoForm">
                        <div class="mb-3">
                            <label for="profileAdminName" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="profileAdminName" required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="profileAdminEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="profileAdminEmail" required disabled>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary d-none" id="profileSaveBtn">Save Changes</button>
                        </div>
                    </form>
                </div>
                <hr>
                <h6 class="fw-bold mb-3">Change Password</h6>
                <form id="profileChangePasswordForm">
                    <div class="mb-3">
                        <label for="profileCurrentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="profileCurrentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="profileNewPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="profileNewPassword" minlength="8" required>
                        <div class="form-text">At least 8 characters.</div>
                    </div>
                    <div class="mb-2">
                        <label for="profileConfirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="profileConfirmPassword" minlength="8" required>
                    </div>
                    <div id="profileChangePassAlert" class="alert d-none mt-3" role="alert"></div>
                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h1 class="text-center mb-4" style="font-size:2rem;">Manage Applications</h1>
    <form method="get" class="mb-3">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Submission ID</th>
                        <th>Student Number</th>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Classification</th>
                        <th>Program</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                    <tr>
                        <th><input type="text" name="submission_id" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['submission_id']); ?>"></th>
                        <th><input type="text" name="student_number" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['student_number']); ?>"></th>
                        <th><input type="text" name="name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['name']); ?>"></th>
                        <th><input type="text" name="title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['title']); ?>"></th>
                        <th><input type="text" name="work_classification" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['work_classification']); ?>"></th>
                        <th><input type="text" name="program" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['program']); ?>"></th>
                        <th><input type="date" name="created_at" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['created_at']); ?>"></th>
                        <th>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <?php foreach ($status_labels as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php if ($filters['status'] === $key) echo 'selected'; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th><input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filters['remarks']); ?>"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['submission_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['work_classification']); ?></td>
                            <td><?php echo htmlspecialchars($row['program']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo $status_labels[$row['status']] ?? htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($submissions)): ?>
                        <tr><td colspan="9" class="text-center">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="ticket.php" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>
<footer class="bg-white border-top py-3 mt-5">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('input[name="search"]');
    var form = searchInput && searchInput.form;
    if (searchInput && form) {
        searchInput.addEventListener('input', function() {
            if (searchInput.value === '') {
                form.submit();
            }
        });
    }
});
</script>

</body>
</html>
