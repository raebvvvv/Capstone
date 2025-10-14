<?php
// Standard admin bootstrap: config -> conn -> secure_bootstrap -> require_admin
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Fetch admin data (robust session validation)
if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_logged_in']) && !empty($_SESSION['is_admin'])) {
    $user_id = (int)$_SESSION['user_id'];
    try {
        if (!empty($_SESSION['admin_number'])) {
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
                INNER JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? AND ap.admin_number = ? LIMIT 1");
            $stmt->execute([$user_id, $_SESSION['admin_number']]);
        } else {
            // Fallback: first admin profile (if multiple exist, explicit admin_number should always be set by login)
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name, ap.admin_number FROM users u 
                LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? LIMIT 1");
            $stmt->execute([$user_id]);
        }
        $admin = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        if (function_exists('log_event')) { log_event('DB_ERR', 'Admin profile fetch failed', ['err'=>$e->getMessage(), 'user_id'=>$user_id]); }
        $admin = [];
    }
    if (!$admin) {
        // If profile missing, redirect to a setup page if it exists; else show a controlled message.
        if (function_exists('redirect') && file_exists(__DIR__ . '/setup_admin_profile.php')) {
            redirect('admin/setup_admin_profile.php');
        }
        echo 'Admin profile not found.'; exit();
    }
    // Derive display fields
    $admin['username'] = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?: 'Admin User';
    $admin['admin_number'] = $admin['admin_number'] ?? ($_SESSION['admin_number'] ?? '');
} else {
    // Session invalid or expired; rely on require_admin earlier, but double safety redirect
    if (function_exists('redirect')) { redirect('admin/login.php'); }
    echo 'Session invalid.'; exit();
}

// Create full name for display
$admin['username'] = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? ''));
if (empty($admin['username'])) {
    $admin['username'] = 'Admin'; // Fallback if no name in profile
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    verify_csrf_post();
    $user_id = (int)$_POST['user_id'];
    $role = isset($_POST['role']) ? $_POST['role'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    // Optional student profile fields
    $student_campus = isset($_POST['student_campus']) ? trim($_POST['student_campus']) : null;
    $student_college = isset($_POST['student_college']) ? trim($_POST['student_college']) : null;
    $student_program = isset($_POST['student_program']) ? trim($_POST['student_program']) : null;
    $student_home_address = isset($_POST['student_home_address']) ? trim($_POST['student_home_address']) : null;
    $student_academic_level = isset($_POST['student_academic_level']) ? trim($_POST['student_academic_level']) : null;
    // Optional employee profile fields
    $employee_campus = isset($_POST['employee_campus']) ? trim($_POST['employee_campus']) : null;
    $employee_college = isset($_POST['employee_college']) ? trim($_POST['employee_college']) : null;
    $employee_department = isset($_POST['employee_department']) ? trim($_POST['employee_department']) : null;
    $employee_program = isset($_POST['employee_program']) ? trim($_POST['employee_program']) : null;
    $employee_home_address = isset($_POST['employee_home_address']) ? trim($_POST['employee_home_address']) : null;
    $employee_academic_level = isset($_POST['employee_academic_level']) ? trim($_POST['employee_academic_level']) : null;
    // Validate role and status
    // Restrict roles: do not allow assigning 'admin' via Manage Users
    $valid_roles = ['student','employee'];
    $valid_status = ['active','inactive','pending'];
    if (!in_array($role, $valid_roles, true)) { $role = null; }
    if (!in_array($status, $valid_status, true)) { $status = null; }

    if ($role !== null && $status !== null) {
        $stmt_update = $pdo->prepare("UPDATE users SET role = ?, status = ? WHERE user_id = ?");
        $stmt_update->execute([$role, $status, $user_id]);
    }

        // If student role, upsert campus/college/program in student_profiles
        if ($role === 'student') {
            try {
                $stmt_check = $pdo->prepare("SELECT profile_id FROM student_profiles WHERE user_id = ? LIMIT 1");
                $stmt_check->execute([$user_id]);
                $existing = $stmt_check->fetchColumn();
                if ($existing) {
                    $stmt_sp = $pdo->prepare("UPDATE student_profiles SET campus = ?, college = ?, program = ?, home_address = COALESCE(?, home_address), academic_level = COALESCE(?, academic_level), last_updated_at = NOW() WHERE user_id = ?");
                    $stmt_sp->execute([$student_campus, $student_college, $student_program, $student_home_address, $student_academic_level, $user_id]);
                } else {
                    $stmt_sp = $pdo->prepare("INSERT INTO student_profiles (user_id, campus, college, program, home_address, academic_level, last_updated_at) VALUES (?,?,?,?,?,?, NOW())");
                    $stmt_sp->execute([$user_id, $student_campus, $student_college, $student_program, $student_home_address, $student_academic_level]);
                }
            } catch (Exception $e) {
                // swallow and continue to avoid breaking the flow; optionally log error
            }
        }

        // If employee role, upsert campus/college/department/program in employee_profiles
        if ($role === 'employee') {
            try {
                $stmt_check_e = $pdo->prepare("SELECT profile_id FROM employee_profiles WHERE user_id = ? LIMIT 1");
                $stmt_check_e->execute([$user_id]);
                $existing_e = $stmt_check_e->fetchColumn();
                if ($existing_e) {
                    $stmt_ep = $pdo->prepare("UPDATE employee_profiles SET campus = ?, college = ?, department = ?, program = ?, home_address = COALESCE(?, home_address), academic_level = COALESCE(?, academic_level), last_updated_at = NOW() WHERE user_id = ?");
                    $stmt_ep->execute([$employee_campus, $employee_college, $employee_department, $employee_program, $employee_home_address, $employee_academic_level, $user_id]);
                } else {
                    $stmt_ep = $pdo->prepare("INSERT INTO employee_profiles (user_id, campus, college, department, program, home_address, academic_level, last_updated_at) VALUES (?,?,?,?,?,?,?, NOW())");
                    $stmt_ep->execute([$user_id, $employee_campus, $employee_college, $employee_department, $employee_program, $employee_home_address, $employee_academic_level]);
                }
            } catch (Exception $e) {
                // swallow and continue; optionally log
            }
        }

    header("Location: manageuser.php");
    exit();
}

// Search
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = '%' . $search_query . '%';

// Role filter (all | student | employee | admin)
$role_filter = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : 'all';
$allowed_roles = ['all','student','employee'];
if (!in_array($role_filter, $allowed_roles, true)) { $role_filter = 'all'; }

// Helper to add role filter clause
$role_sql = $role_filter !== 'all' ? " AND role = :role_filter" : '';

// Fetch active users with proper identifiers from profile tables
$sql_active = "
    SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status, 
        u.created_at,
        CASE 
            WHEN u.role = 'student' THEN sp.student_number
            WHEN u.role = 'employee' THEN ep.employee_number  
            WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
            ELSE CONCAT('User-', u.user_id)
        END as identifier,
        CASE 
            WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
            WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
            WHEN u.role = 'admin' THEN CONCAT_WS(' ', ap.first_name, ap.last_name)
            ELSE 'Unknown User'
        END as full_name,
    sp.campus AS student_campus,
    sp.college AS student_college,
    sp.program AS student_program,
    sp.academic_level AS student_academic_level,
    sp.home_address AS student_home_address,
    ep.campus AS employee_campus,
    ep.college AS employee_college,
    ep.department AS employee_department,
    ep.program AS employee_program,
    ep.academic_level AS employee_academic_level,
    ep.home_address AS employee_home_address
    FROM users u
    LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
    LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
    WHERE u.status = 'active' AND u.role <> 'admin' AND (
        u.email LIKE :q1 
        OR sp.student_number LIKE :q2
        OR ep.employee_number LIKE :q3
        OR CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name) LIKE :q4
        OR CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name) LIKE :q5
        OR CONCAT_WS(' ', ap.first_name, ap.last_name) LIKE :q6
        OR sp.first_name LIKE :q7
        OR sp.last_name LIKE :q8
        OR ep.first_name LIKE :q9
        OR ep.last_name LIKE :q10
        OR ap.first_name LIKE :q11
        OR ap.last_name LIKE :q12
    )" . $role_sql . " 
    ORDER BY u.created_at DESC";
$stmt_active = $pdo->prepare($sql_active);
$stmt_active->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q2', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q3', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q4', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q5', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q6', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q7', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q8', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q9', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q10', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q11', $search_param, PDO::PARAM_STR);
$stmt_active->bindValue(':q12', $search_param, PDO::PARAM_STR);
if ($role_filter !== 'all') { $stmt_active->bindValue(':role_filter', $role_filter, PDO::PARAM_STR); }
$stmt_active->execute();
$result_active = $stmt_active->fetchAll();

// Fetch pending users (waiting for verification)  
$sql_pending = "
    SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status, 
        u.created_at,
        CASE 
            WHEN u.role = 'student' THEN sp.student_number
            WHEN u.role = 'employee' THEN ep.employee_number  
            WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
            ELSE CONCAT('User-', u.user_id)
        END as identifier,
        CASE 
            WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
            WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
            WHEN u.role = 'admin' THEN CONCAT_WS(' ', ap.first_name, ap.last_name)
            ELSE 'Unknown User'
        END as full_name,
    sp.campus AS student_campus,
    sp.college AS student_college,
    sp.program AS student_program,
    sp.academic_level AS student_academic_level,
    sp.home_address AS student_home_address,
    ep.campus AS employee_campus,
    ep.college AS employee_college,
    ep.department AS employee_department,
    ep.program AS employee_program,
    ep.academic_level AS employee_academic_level,
    ep.home_address AS employee_home_address
    FROM users u
    LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
    LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
    WHERE u.status = 'pending' AND u.role <> 'admin' AND (
        u.email LIKE :q1 
        OR sp.student_number LIKE :q2
        OR ep.employee_number LIKE :q3
        OR CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name) LIKE :q4
        OR CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name) LIKE :q5
        OR CONCAT_WS(' ', ap.first_name, ap.last_name) LIKE :q6
        OR sp.first_name LIKE :q7
        OR sp.last_name LIKE :q8
        OR ep.first_name LIKE :q9
        OR ep.last_name LIKE :q10
        OR ap.first_name LIKE :q11
        OR ap.last_name LIKE :q12
    )" . $role_sql . " 
    ORDER BY u.created_at DESC";
$stmt_pending = $pdo->prepare($sql_pending);
$stmt_pending->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q2', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q3', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q4', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q5', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q6', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q7', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q8', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q9', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q10', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q11', $search_param, PDO::PARAM_STR);
$stmt_pending->bindValue(':q12', $search_param, PDO::PARAM_STR);
if ($role_filter !== 'all') { $stmt_pending->bindValue(':role_filter', $role_filter, PDO::PARAM_STR); }
$stmt_pending->execute();
$result_pending = $stmt_pending->fetchAll();

// Fetch inactive users
$sql_inactive = "
    SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status, 
        u.created_at,
        CASE 
            WHEN u.role = 'student' THEN sp.student_number
            WHEN u.role = 'employee' THEN ep.employee_number  
            WHEN u.role = 'admin' THEN CONCAT('Admin-', u.user_id)
            ELSE CONCAT('User-', u.user_id)
        END as identifier,
        CASE 
            WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
            WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
            WHEN u.role = 'admin' THEN CONCAT_WS(' ', ap.first_name, ap.last_name)
            ELSE 'Unknown User'
        END as full_name,
    sp.campus AS student_campus,
    sp.college AS student_college,
    sp.program AS student_program,
    sp.academic_level AS student_academic_level,
    sp.home_address AS student_home_address,
    ep.campus AS employee_campus,
    ep.college AS employee_college,
    ep.department AS employee_department,
    ep.program AS employee_program,
    ep.academic_level AS employee_academic_level,
    ep.home_address AS employee_home_address
    FROM users u
    LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
    LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
    LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id AND u.role = 'admin'
    WHERE u.status = 'inactive' AND u.role <> 'admin' AND (
        u.email LIKE :q1 
        OR sp.student_number LIKE :q2
        OR ep.employee_number LIKE :q3
        OR CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name) LIKE :q4
        OR CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name) LIKE :q5
        OR CONCAT_WS(' ', ap.first_name, ap.last_name) LIKE :q6
        OR sp.first_name LIKE :q7
        OR sp.last_name LIKE :q8
        OR ep.first_name LIKE :q9
        OR ep.last_name LIKE :q10
        OR ap.first_name LIKE :q11
        OR ap.last_name LIKE :q12
    )" . $role_sql . " 
    ORDER BY u.created_at DESC";
$stmt_inactive = $pdo->prepare($sql_inactive);
$stmt_inactive->bindValue(':q1', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q2', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q3', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q4', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q5', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q6', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q7', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q8', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q9', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q10', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q11', $search_param, PDO::PARAM_STR);
$stmt_inactive->bindValue(':q12', $search_param, PDO::PARAM_STR);
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
    <link rel="stylesheet" href="../css/admin-navbar.css?v=2">
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
                            <li class="nav-item"><a class="nav-link" href="catalogs.php">Catalogs</a></li>
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
                        <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search by Name, Student Number, Employee ID, or Email" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
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
                                    <th>Name</th>
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
                                        <td><?php echo htmlspecialchars($row['identifier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['full_name'] ?? 'N/A'); ?></td>
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
                                    <th>Name</th>
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
                                        <td><?php echo htmlspecialchars($row['identifier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['full_name'] ?? 'N/A'); ?></td>
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
                                    <th>Name</th>
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
                                        <td><?php echo htmlspecialchars($row['identifier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['full_name'] ?? 'N/A'); ?></td>
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
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="identifier_display" class="form-label">Student Number/Employee ID</label>
                                <input type="text" class="form-control" id="identifier_display" value="<?php echo htmlspecialchars($row['identifier'] ?? 'N/A'); ?>" readonly>
                                <small class="text-muted">This field cannot be edited from this interface.</small>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-6">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="student" <?php echo $row['role']==='student'?'selected':''; ?>>Student</option>
                                        <option value="employee" <?php echo $row['role']==='employee'?'selected':''; ?>>Employee</option>
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
                                <?php if (($row['role'] ?? '') === 'student'): ?>
                                <div class="border rounded p-3 mb-3 bg-light-subtle student-profile-section" style="border-color:#ddd!important;">
                                    <h6 class="fw-bold mb-3">Student Profile</h6>
                                    <div class="mb-3">
                                        <label for="student_home_<?php echo $row['user_id']; ?>" class="form-label">Home Address</label>
                                        <input type="text" class="form-control" id="student_home_<?php echo $row['user_id']; ?>" name="student_home_address" value="<?php echo htmlspecialchars($row['student_home_address'] ?? ''); ?>" placeholder="Enter home address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_level_<?php echo $row['user_id']; ?>" class="form-label">Academic Level</label>
                                        <select class="form-select acad-level-student" id="student_level_<?php echo $row['user_id']; ?>" name="student_academic_level" data-current="<?php echo htmlspecialchars($row['student_academic_level'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_campus_<?php echo $row['user_id']; ?>" class="form-label">Campus</label>
                                        <select class="form-select acad-campus" id="student_campus_<?php echo $row['user_id']; ?>" name="student_campus" data-current="<?php echo htmlspecialchars($row['student_campus'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_college_<?php echo $row['user_id']; ?>" class="form-label">College</label>
                                        <select class="form-select acad-college" id="student_college_<?php echo $row['user_id']; ?>" name="student_college" data-current="<?php echo htmlspecialchars($row['student_college'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_program_<?php echo $row['user_id']; ?>" class="form-label">Program</label>
                                        <select class="form-select acad-program" id="student_program_<?php echo $row['user_id']; ?>" name="student_program" data-current="<?php echo htmlspecialchars($row['student_program'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (($row['role'] ?? '') === 'employee'): ?>
                                <div class="border rounded p-3 mb-3 bg-light-subtle employee-profile-section" style="border-color:#ddd!important;">
                                    <h6 class="fw-bold mb-3">Employee Profile</h6>
                                    <div class="mb-3">
                                        <label for="employee_home_<?php echo $row['user_id']; ?>" class="form-label">Home Address</label>
                                        <input type="text" class="form-control" id="employee_home_<?php echo $row['user_id']; ?>" name="employee_home_address" value="<?php echo htmlspecialchars($row['employee_home_address'] ?? ''); ?>" placeholder="Enter home address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_level_<?php echo $row['user_id']; ?>" class="form-label">Academic Level</label>
                                        <select class="form-select emp-level" id="employee_level_<?php echo $row['user_id']; ?>" name="employee_academic_level" data-current="<?php echo htmlspecialchars($row['employee_academic_level'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_campus_<?php echo $row['user_id']; ?>" class="form-label">Campus</label>
                                        <select class="form-select emp-campus" id="employee_campus_<?php echo $row['user_id']; ?>" name="employee_campus" data-current="<?php echo htmlspecialchars($row['employee_campus'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_college_<?php echo $row['user_id']; ?>" class="form-label">College</label>
                                        <select class="form-select emp-college" id="employee_college_<?php echo $row['user_id']; ?>" name="employee_college" data-current="<?php echo htmlspecialchars($row['employee_college'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_department_<?php echo $row['user_id']; ?>" class="form-label">Department</label>
                                        <select class="form-select emp-department" id="employee_department_<?php echo $row['user_id']; ?>" name="employee_department" data-current="<?php echo htmlspecialchars($row['employee_department'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="employee_program_<?php echo $row['user_id']; ?>" class="form-label">Program</label>
                                        <select class="form-select emp-program" id="employee_program_<?php echo $row['user_id']; ?>" name="employee_program" data-current="<?php echo htmlspecialchars($row['employee_program'] ?? ''); ?>">
                                            <option value="" disabled selected>Choose...</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
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
                                <label for="profileAdminName" class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control" id="profileAdminName" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" required disabled>
                            </div>
                            <div class="mb-3">
                                <label for="profileAdminEmail" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="profileAdminEmail" value="<?php echo htmlspecialchars($admin['email']); ?>" required disabled>
                            </div>
                             <div class="mb-3">
                                <label for="profileAdminNumber" class="form-label fw-semibold">Admin Number</label>
                                <input type="text" class="form-control" id="profileAdminNumber" value="<?php echo htmlspecialchars($admin['admin_number'] ?? ''); ?>" disabled>
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
                            <div class="form-text">At least 12 characters.</div>
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
    <script src="../javascript/admin-profile.js?v=5" defer></script>
    <script src="../javascript/admin-notifications.js?v=2" defer></script>
    <?php include __DIR__ . '/../partials/standard_footer.php'; ?>
    <script src="../javascript/admin-manageuser.js?v=2" defer></script>
    <script nonce="<?php echo SecurityHeaders::getCSPNonce(); ?>" defer>
        // Populate campus/college/department/program selects in Edit User modals using Catalogs API
        document.addEventListener('DOMContentLoaded', function () {
            const CAT_API = (window.CATALOGS_API_URL || 'catalogs_api.php');

            const Catalogs = {
                levels: [], // [{id,name,code}]
                campuses: [], // [{id,name,code}]
                colleges: [], // [{id,name,code}]
                departments: [], // raw rows with college_id
                programs: [], // raw rows with college_id
                depsByCollege: {}, // college_id -> [name]
                progsByCollege: {}, // college_id -> [name]
                collegeIdByName: {}, // normalized name -> id
            };

            // Helper normalizer for case/whitespace-insensitive comparisons
            function norm(s){ return (s||'').toString().trim().toLowerCase(); }

            // Ensure a select picks a value by case-insensitive text match, or add it if missing
            function ensureSelectedOrAdd(selectEl, value){
                if (!selectEl) return;
                const v = (value||'').toString().trim();
                if (!v) return;
                const n = norm(v);
                let matched = false;
                for (const opt of Array.from(selectEl.options)){
                    if (norm(opt.textContent) === n){
                        opt.selected = true;
                        matched = true;
                        break;
                    }
                }
                if (!matched){
                    const o = document.createElement('option');
                    o.value = v; o.textContent = v; o.selected = true;
                    selectEl.appendChild(o);
                }
            }

            function populateSelect(selectEl, options, currentValue) {
                if (!selectEl) return;
                selectEl.innerHTML = '';
                const def = document.createElement('option');
                def.value = '';
                def.disabled = true;
                def.selected = true;
                def.textContent = 'Choose...';
                selectEl.appendChild(def);
                (options || []).forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt;
                    o.textContent = opt;
                    if (currentValue && currentValue === opt) {
                        o.selected = true;
                        def.selected = false;
                    }
                    selectEl.appendChild(o);
                });
            }

            async function fetchEntity(entity) {
                const url = `${CAT_API}?action=list&entity=${encodeURIComponent(entity)}`;
                const r = await fetch(url, { credentials: 'same-origin' });
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                const d = await r.json();
                if (!d || d.ok !== true || !Array.isArray(d.data)) throw new Error('Bad response');
                return d.data;
            }

            async function loadCatalogs() {
                const [levels, campuses, colleges, departments, programs] = await Promise.all([
                    fetchEntity('level'), fetchEntity('campus'), fetchEntity('college'), fetchEntity('department'), fetchEntity('program')
                ]);
                Catalogs.levels = levels;
                Catalogs.campuses = campuses;
                Catalogs.colleges = colleges;
                Catalogs.departments = departments;
                Catalogs.programs = programs;
                // Build normalized name -> id map for colleges
                Catalogs.collegeIdByName = Object.fromEntries((colleges||[]).map(c => [norm(c.name), c.id]));
                Catalogs.depsByCollege = {};
                (departments||[]).forEach(d => {
                    const k = String(d.college_id||'');
                    if (!Catalogs.depsByCollege[k]) Catalogs.depsByCollege[k] = [];
                    Catalogs.depsByCollege[k].push(d.name);
                });
                Catalogs.progsByCollege = {};
                (programs||[]).forEach(p => {
                    const k = String(p.college_id||'');
                    if (!Catalogs.progsByCollege[k]) Catalogs.progsByCollege[k] = [];
                    Catalogs.progsByCollege[k].push(p.name);
                });
            }

            function initStudentSection(section) {
                const campusSel = section.querySelector('.acad-campus');
                const collegeSel = section.querySelector('.acad-college');
                const programSel = section.querySelector('.acad-program');
                const levelSel = section.querySelector('.acad-level-student');
                if (!campusSel || !collegeSel || !programSel) return;

                const campusCurrent = campusSel.dataset.current || '';
                const collegeCurrent = collegeSel.dataset.current || '';
                const programCurrent = programSel.dataset.current || '';
                const levelCurrent = levelSel ? (levelSel.dataset.current || '') : '';

                const campusNames = (Catalogs.campuses||[]).map(c => c.name);
                populateSelect(campusSel, campusNames, campusCurrent);

                if (levelSel) {
                    const levelNames = (Catalogs.levels||[]).map(l => l.name);
                    const studentLevels = levelNames.filter(l => l.toLowerCase() !== 'not studying');
                    populateSelect(levelSel, studentLevels, levelCurrent);
                }

                const collegeNames = (Catalogs.colleges||[]).map(c => c.name);
                populateSelect(collegeSel, collegeNames, collegeCurrent);

                function rebuildProgramsFor(collegeName, current) {
                    const id = Catalogs.collegeIdByName[norm(collegeName)] || null;
                    const list = id ? (Catalogs.progsByCollege[String(id)] || []) : [];
                    populateSelect(programSel, list, current || '');
                    // Fallback: if current program not found in list, still show it
                    if (current && (!programSel.value || programSel.value === '')) {
                        ensureSelectedOrAdd(programSel, current);
                    }
                }

                rebuildProgramsFor(collegeCurrent || collegeSel.value, programCurrent);
                collegeSel.addEventListener('change', function(){ rebuildProgramsFor(this.value, ''); });
            }

            function initEmployeeSection(section) {
                const campusSel = section.querySelector('.emp-campus');
                const collegeSel = section.querySelector('.emp-college');
                const deptSel = section.querySelector('.emp-department');
                const programSel = section.querySelector('.emp-program');
                const levelSel = section.querySelector('.emp-level');
                if (!campusSel || !collegeSel || !deptSel || !programSel) return;

                const campusCurrent = campusSel.dataset.current || '';
                const collegeCurrent = collegeSel.dataset.current || '';
                const deptCurrent = deptSel.dataset.current || '';
                const programCurrent = programSel.dataset.current || '';
                const levelCurrent = levelSel ? (levelSel.dataset.current || '') : '';

                const campusNames = (Catalogs.campuses||[]).map(c => c.name);
                populateSelect(campusSel, campusNames, campusCurrent);

                if (levelSel) {
                    // Ensure Not Studying, Doctorate, Masters, Open University (preserve order when possible)
                    const set = new Set((Catalogs.levels||[]).map(l => l.name));
                    const preferred = ['Not Studying','Doctorate','Masters','Open University'];
                    const employeeLevels = preferred.filter(n => set.has(n)).concat(Array.from(set).filter(n => !preferred.includes(n)));
                    populateSelect(levelSel, employeeLevels, levelCurrent);
                }

                const collegeNames = (Catalogs.colleges||[]).map(c => c.name);
                populateSelect(collegeSel, collegeNames, collegeCurrent);

                function rebuildDepsAndProgs(collegeName, depCur, progCur) {
                    const id = Catalogs.collegeIdByName[norm(collegeName)] || null;
                    const deps = id ? (Catalogs.depsByCollege[String(id)] || []) : [];
                    const progs = id ? (Catalogs.progsByCollege[String(id)] || []) : [];
                    if (deps.length) {
                        deptSel.disabled = false;
                        populateSelect(deptSel, deps, depCur || '');
                    } else {
                        deptSel.disabled = true;
                        deptSel.innerHTML = '';
                        const na = document.createElement('option');
                        na.value = 'N/A'; na.textContent = 'N/A';
                        deptSel.appendChild(na);
                    }
                    populateSelect(programSel, progs, progCur || '');
                    // Fallbacks: ensure preselected values appear even if catalogs changed
                    if (depCur && (!deptSel.value || deptSel.value === '')) { ensureSelectedOrAdd(deptSel, depCur); }
                    if (progCur && (!programSel.value || programSel.value === '')) { ensureSelectedOrAdd(programSel, progCur); }
                }

                rebuildDepsAndProgs(collegeCurrent || collegeSel.value, deptCurrent, programCurrent);
                collegeSel.addEventListener('change', function(){ rebuildDepsAndProgs(this.value, '', ''); });
            }

            // Fetch catalogs once, then init modals on open
            loadCatalogs().then(() => {
                document.querySelectorAll('.modal[id^="editUserModal"]').forEach(modal => {
                    modal.addEventListener('shown.bs.modal', function () {
                        const sSec = modal.querySelector('.student-profile-section');
                        if (sSec && !sSec.dataset.initialized) { initStudentSection(sSec); sSec.dataset.initialized = '1'; }
                        const eSec = modal.querySelector('.employee-profile-section');
                        if (eSec && !eSec.dataset.initialized) { initEmployeeSection(eSec); eSec.dataset.initialized = '1'; }
                    });
                });
            }).catch(() => {
                // If catalogs fetch fails, leave selects as-is (fallback to existing behavior)
            });
        });
    </script>
</body>
</html>