<?php
// Standard admin bootstrap: config -> conn -> secure_bootstrap -> require_admin
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Fetch admin data for navbar/profile
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();
if (!$admin) {
    echo "Admin not found.";
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    verify_csrf_post();
    $user_id = (int)$_POST['user_id'];
    $email = $_POST['email'];
    $student_number = $_POST['student_number'];
    $role = isset($_POST['role']) ? $_POST['role'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    // Validate role and status
    $valid_roles = ['student','employee','admin'];
    $valid_status = ['active','inactive','pending'];
    if (!in_array($role, $valid_roles, true)) { $role = null; }
    if (!in_array($status, $valid_status, true)) { $status = null; }

    if ($role !== null && $status !== null) {
        $stmt_update = $pdo->prepare("UPDATE users SET email = ?, student_number = ?, role = ?, status = ? WHERE user_id = ?");
        $stmt_update->execute([$email, $student_number, $role, $status, $user_id]);
    } else {
        $stmt_update = $pdo->prepare("UPDATE users SET email = ?, student_number = ? WHERE user_id = ?");
        $stmt_update->execute([$email, $student_number, $user_id]);
    }

    header("Location: manageuser.php");
    exit();
}

// Search
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = '%' . $search_query . '%';

// Role filter (all | student | employee | admin)
$role_filter = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : 'all';
$allowed_roles = ['all','student','employee','admin'];
if (!in_array($role_filter, $allowed_roles, true)) { $role_filter = 'all'; }

// Helper to add role filter clause
$role_sql = $role_filter !== 'all' ? " AND role = :role_filter" : '';

// Fetch active users
$sql_active = "SELECT user_id, student_number, email, role, status FROM users WHERE status = 'active' AND (student_number LIKE :q1 OR email LIKE :q2)" . $role_sql . " ORDER BY created_at DESC";
$stmt_active = $pdo->prepare($sql_active);
$stmt_active->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q2', $search_param, PDO::PARAM_STR);
if ($role_filter !== 'all') { $stmt_active->bindValue(':role_filter', $role_filter, PDO::PARAM_STR); }
$stmt_active->execute();
$result_active = $stmt_active->fetchAll();

// Fetch pending users (waiting for verification)
$sql_pending = "SELECT user_id, student_number, email, role, status FROM users WHERE status = 'pending' AND (student_number LIKE :q1 OR email LIKE :q2)" . $role_sql . " ORDER BY created_at DESC";
$stmt_pending = $pdo->prepare($sql_pending);
$stmt_pending->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q2', $search_param, PDO::PARAM_STR);
if ($role_filter !== 'all') { $stmt_pending->bindValue(':role_filter', $role_filter, PDO::PARAM_STR); }
$stmt_pending->execute();
$result_pending = $stmt_pending->fetchAll();

// Fetch inactive users
$sql_inactive = "SELECT user_id, student_number, email, role, status FROM users WHERE status = 'inactive' AND (student_number LIKE :q1 OR email LIKE :q2)" . $role_sql . " ORDER BY created_at DESC";
$stmt_inactive = $pdo->prepare($sql_inactive);
$stmt_inactive->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q2', $search_param, PDO::PARAM_STR);
if ($role_filter !== 'all') { $stmt_inactive->bindValue(':role_filter', $role_filter, PDO::PARAM_STR); }
$stmt_inactive->execute();
$result_inactive = $stmt_inactive->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/manageuser.css?v=2">
    <link rel="stylesheet" href="../css/admin-navbar.css?v=1">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
    <title>User Management</title>
</head>
<body>
    <header class="bg-light border-bottom py-3 shadow-sm" data-admin-email="<?php echo htmlspecialchars($admin['email']); ?>">
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
                            <li class="nav-item ms-auto"><a class="nav-link" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" aria-current="page"  href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
                            <li class="nav-item d-flex align-items-center header-actions ms-lg-3 mt-2 mt-lg-0">
                                <button type="button" class="btn btn-outline-secondary btn-profile" data-bs-toggle="modal" data-bs-target="#adminProfileModal">My Profile</button>
                                <form method="POST" action="../logout.php" class="d-inline ms-2">
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
        <h1 class="text-center mb-4" style="font-size:2rem;">Manage Users</h1>
        <div class="d-flex justify-content-center mb-3">
            <form method="GET" action="manageuser.php" class="row g-2 align-items-center" style="max-width:800px;">
                <div class="col-12 col-md-6">
                    <div class="input-group search-bar">
                        <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search by ID or Email" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                        <button class="btn btn-outline-secondary rounded-pill" type="submit" style="margin-left:-40px; border-radius: 50px;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select">
                        <option value="all" <?php echo $role_filter==='all'?'selected':''; ?>>All Roles</option>
                        <option value="student" <?php echo $role_filter==='student'?'selected':''; ?>>Student</option>
                        <option value="employee" <?php echo $role_filter==='employee'?'selected':''; ?>>Employee</option>
                        <option value="admin" <?php echo $role_filter==='admin'?'selected':''; ?>>Admin</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-grid">
                    <button class="btn btn-primary" type="submit">Apply</button>
                </div>
            </form>
        </div>

        <!-- Tabs for Active and Pending Users -->
        <ul class="nav nav-tabs" id="userTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#active">Active Users</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pending">Waiting for Verification</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#inactive">Inactive Users</a></li>
        </ul>
        <div class="tab-content mt-3">
            <!-- Active Users Tab -->
            <div class="tab-pane fade show active" id="active">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <a class="btn btn-outline-secondary btn-sm" href="manageuser_export.php?status=active&role=<?php echo urlencode($role_filter); ?>&search=<?php echo urlencode($search_query); ?>">Export CSV</a>
                    </div>
                </div>
                <form method="POST" action="bulk_user_action.php" class="bulk-form">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="status_scope" value="active">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px;"><input type="checkbox" class="form-check-input select-all"></th>
                                    <th>#</th>
                                    <th>Student Number/Employee ID</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result_active as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input row-check" name="user_ids[]" value="<?php echo (int)$row['user_id']; ?>"></td>
                                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['role'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">Edit</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex gap-2">
                        <select name="bulk_action" class="form-select form-select-sm bulk-action-select" style="max-width:200px;">
                            <option value="">Bulk action…</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-sm bulk-submit" disabled>Apply</button>
                    </div>
                </form>
            </div>
            <!-- Pending Users Tab -->
            <div class="tab-pane fade" id="pending">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <a class="btn btn-outline-secondary btn-sm" href="manageuser_export.php?status=pending&role=<?php echo urlencode($role_filter); ?>&search=<?php echo urlencode($search_query); ?>">Export CSV</a>
                    </div>
                </div>
                <form method="POST" action="bulk_user_action.php" class="bulk-form">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="status_scope" value="pending">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px;"><input type="checkbox" class="form-check-input select-all"></th>
                                    <th>#</th>
                                    <th>Student Number/Employee ID</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result_pending as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input row-check" name="user_ids[]" value="<?php echo (int)$row['user_id']; ?>"></td>
                                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['role'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">Edit</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex gap-2">
                        <select name="bulk_action" class="form-select form-select-sm bulk-action-select" style="max-width:200px;">
                            <option value="">Bulk action…</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-sm bulk-submit" disabled>Apply</button>
                    </div>
                </form>
            </div>
            <!-- Inactive Users Tab -->
            <div class="tab-pane fade" id="inactive">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <a class="btn btn-outline-secondary btn-sm" href="manageuser_export.php?status=inactive&role=<?php echo urlencode($role_filter); ?>&search=<?php echo urlencode($search_query); ?>">Export CSV</a>
                    </div>
                </div>
                <form method="POST" action="bulk_user_action.php" class="bulk-form">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="status_scope" value="inactive">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px;"><input type="checkbox" class="form-check-input select-all"></th>
                                    <th>#</th>
                                    <th>Student Number/Employee ID</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result_inactive as $row): ?>
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input row-check" name="user_ids[]" value="<?php echo (int)$row['user_id']; ?>"></td>
                                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['role'])); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">Edit</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex gap-2">
                        <select name="bulk_action" class="form-select form-select-sm bulk-action-select" style="max-width:200px;">
                            <option value="">Bulk action…</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-sm bulk-submit" disabled>Apply</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modals for editing users (output after tables for valid HTML) -->
        <?php foreach (array_merge($result_active, $result_pending, $result_inactive) as $row): ?>
        <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $row['user_id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel<?php echo $row['user_id']; ?>">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="manageuser.php">
                            <?php csrf_input(); ?>
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Webmail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="student_number" class="form-label">Student Number/Employee ID</label>
                                <input type="text" class="form-control" id="student_number" name="student_number" value="<?php echo htmlspecialchars($row['student_number']); ?>" required>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-6">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="student" <?php echo $row['role']==='student'?'selected':''; ?>>Student</option>
                                        <option value="employee" <?php echo $row['role']==='employee'?'selected':''; ?>>Employee</option>
                                        <option value="admin" <?php echo $row['role']==='admin'?'selected':''; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?php echo $row['status']==='active'?'selected':''; ?>>Active</option>
                                        <option value="inactive" <?php echo $row['status']==='inactive'?'selected':''; ?>>Inactive</option>
                                        <option value="pending" <?php echo $row['status']==='pending'?'selected':''; ?>>Pending</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="update_user" class="btn btn-success">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
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
                                <label for="profileAdminEmail" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="profileAdminEmail" value="<?php echo htmlspecialchars($admin['email']); ?>" required disabled>
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
    <script src="../javascript/admin-profile.js?v=2" defer></script>
    <script src="../javascript/admin-notifications.js?v=1" defer></script>
    <?php include __DIR__ . '/../partials/standard_footer.php'; ?>
    <script src="../javascript/admin-manageuser.js?v=2" defer></script>
</body>
</html>