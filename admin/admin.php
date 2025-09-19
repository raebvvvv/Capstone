<?php
// Admin dashboard (moved under /admin)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Helper: Check if table exists (returns bool)
function table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
    if (!$stmt) { return false; }
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $res = $stmt->get_result();
    return (bool)$res->fetch_row();
}

// Helper: Secure count query with graceful fallback if table dropped
function getCount($conn, $table, $where = '', $params = []) {
    if (!table_exists($conn, $table)) {
        if (function_exists('log_event')) { log_event('DB_WARN', 'Table missing for count', ['table' => $table]); }
        return 0;
    }
    $sql = "SELECT COUNT(*) as count FROM `$table`";
    if ($where) { $sql .= " WHERE $where"; }
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        if (function_exists('log_event')) { log_event('DB_ERROR', 'Prepare failed in getCount', ['table' => $table, 'error' => $conn->error]); }
        return 0;
    }
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        if (function_exists('log_event')) { log_event('DB_ERROR', 'Execute failed in getCount', ['table' => $table, 'error' => $stmt->error]); }
        return 0;
    }
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    return $row ? (int)$row['count'] : 0;
}

// Dashboard counts
$total_users = getCount($conn, 'users', 'status = ?', ['approved']);
$total_applications = getCount($conn, 'rental_requests');
$pending_applications = getCount($conn, 'rental_requests', 'status = ?', ['Pending']);
$approved_applications = getCount($conn, 'rental_requests', 'status = ?', ['Approved']);
$completed_applications = getCount($conn, 'rental_requests', 'status = ?', ['Completed']);

// Example chart values
$undergrad = 120;
$grad = 45;
$open = 30;
$total_applications_chart = $undergrad + $grad + $open;

// Fetch admin data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if (!$admin) { echo "Admin not found."; exit(); }
} else {
    echo "User ID not set in session."; exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css?v=2'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin-navbar.css'); ?>">
</head>
<body>
    <header class="bg-light border-bottom py-3 shadow-sm" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>" data-admin-email="<?php echo htmlspecialchars($admin['email']); ?>">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="<?php echo asset_url('images/puplogo.png'); ?>" alt="Logo" class="center-img" style="height: 30px; margin-right: 10px;">
                        <span class="fw-bold">PUP e-IPMO [Admin.]</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center w-100">
                            <li class="nav-item ms-auto"><a class="nav-link fw-bold" aria-current="page" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
                            <li class="nav-item d-flex align-items-center header-actions ms-lg-3 mt-2 mt-lg-0">
                                <button type="button" class="btn btn-outline-secondary btn-profile" data-bs-toggle="modal" data-bs-target="#adminProfileModal">My Profile</button>
                                <form method="POST" action="../User/Beforelogin/logout.php" class="d-inline ms-2">
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

    <div class="container mt-5">
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card users text-center w-100">
                    <h2><?= $total_users ?></h2>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-5 col-6 mb-3 me-5 d-flex justify-content-center">
                <div class="summary-card total text-center w-100">
                    <h2><?= $total_applications ?></h2>
                    <p>Total Applications</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card pending text-center w-100">
                    <h2><?= $pending_applications ?></h2>
                   <p>Pending Applications<br><small>• New/Incomplete</small></p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card approved text-center w-100">
                    <h2><?= $approved_applications ?></h2>
                    <p>Approved Evaluation<br></p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card completed text-center w-100">
                    <h2><?= $completed_applications ?></h2>
                    <p>Completed Applications<br></p>
                </div>
            </div>
        </div>
  <!-- Application Overview Section -->
        <div class="dashboard-section mb-4">
            <div class="row align-items-center">
                <h5 class="mb-5"><span class="legend-dot legend-open"></span>Application Overview</h5>
                <div class="col-md-6 chart-container d-flex justify-content-center align-items-center">
                    <canvas id="applicationOverviewChart"
                            data-undergrad="<?= (int)$undergrad ?>"
                            data-grad="<?= (int)$grad ?>"
                            data-open="<?= (int)$open ?>">
                    </canvas>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <div style="font-size: 1.2rem;">
                        <span style="color:#870000;">Undergraduate</span> <b><?= $undergrad ?></b> &nbsp;
                        <span style="color:#FFD54F;">Graduate School</span> <b><?= $grad ?></b> &nbsp;
                        <span style="color:gray;">Open University</span> <b><?= $open ?></b> &nbsp; <br><br>
                         <span style="font-weight:600;">| <?= $total_applications_chart ?> Total Applications</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PUP MAIN–Undergraduates Bar Chart -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-undergrad"></span> Count of Applicant Details/College</h5>
            <div class="bar-chart-container">
                <canvas id="mainUndergradChart"></canvas>
            </div>
        </div>

        <!-- Other Branches and Campus Bar Chart -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-grad"></span> Other Campus</h5>
            <div class="bar-chart-container">
                <canvas id="branchesChart"></canvas>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script src="../javascript/admin-dashboard.js?v=2" defer></script>
    <script src="../javascript/admin-profile.js?v=2" defer></script>

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
      <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>
</body>
</html>
