<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// Fetch admin for header display
if (!empty($_SESSION['user_id']) && !empty($_SESSION['user_logged_in']) && !empty($_SESSION['is_admin'])) {
    $user_id = (int)$_SESSION['user_id'];
    try {
        if (!empty($_SESSION['admin_number'])) {
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name FROM users u INNER JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? AND ap.admin_number = ? LIMIT 1");
            $stmt->execute([$user_id, $_SESSION['admin_number']]);
        } else {
            $stmt = $pdo->prepare("SELECT u.email, ap.first_name, ap.last_name FROM users u LEFT JOIN admin_profiles ap ON u.user_id = ap.user_id WHERE u.user_id = ? LIMIT 1");
            $stmt->execute([$user_id]);
        }
        $admin = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $admin['username'] = trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?: 'Admin User';
    } catch (Throwable $e) { $admin = ['username' => 'Admin User', 'email' => '']; }
} else {
    if (function_exists('redirect')) { redirect('admin/login.php'); }
    echo 'Session invalid.'; exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css?v=2'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin-navbar.css'); ?>">
    <title>Catalog Management</title>
    <style>
        .entity-table td, .entity-table th { vertical-align: middle; }
    </style>
    </head>
<body>
    <header class="bg-light border-bottom py-3 shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="admin.php">
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
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link active" aria-current="page" href="catalogs.php">Catalogs</a></li>
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

    <div class="container mt-4">
        <h3 class="mb-3">Catalog Management</h3>
        <p class="text-muted">Add, edit, or remove options used in forms. Changes take effect immediately.</p>

        <ul class="nav nav-tabs" id="catalogTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="campuses-tab" data-bs-toggle="tab" data-bs-target="#campuses" type="button" role="tab">Campuses</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="levels-tab" data-bs-toggle="tab" data-bs-target="#levels" type="button" role="tab">Academic Levels</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="colleges-tab" data-bs-toggle="tab" data-bs-target="#colleges" type="button" role="tab">Colleges</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="departments-tab" data-bs-toggle="tab" data-bs-target="#departments" type="button" role="tab">Departments</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs" type="button" role="tab">Programs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <!-- Campuses -->
            <div class="tab-pane fade show active" id="campuses" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Campuses</h5>
                    <button class="btn btn-sm btn-primary" data-entity="campus" id="btnAddCampus">Add Campus</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableCampus">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60%">Name</th>
                                <th style="width:20%">Code</th>
                                <th style="width:20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Academic Levels -->
            <div class="tab-pane fade" id="levels" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Academic Levels</h5>
                    <button class="btn btn-sm btn-primary" data-entity="level" id="btnAddLevel">Add Level</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableLevel">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60%">Name</th>
                                <th style="width:20%">Code</th>
                                <th style="width:20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Colleges -->
            <div class="tab-pane fade" id="colleges" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Colleges</h5>
                    <button class="btn btn-sm btn-primary" data-entity="college" id="btnAddCollege">Add College</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableCollege">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50%">Name</th>
                                <th style="width:15%">Code</th>
                                <th style="width:15%">Campus</th>
                                <th style="width:20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Departments -->
            <div class="tab-pane fade" id="departments" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Departments</h5>
                    <button class="btn btn-sm btn-primary" data-entity="department" id="btnAddDepartment">Add Department</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableDepartment">
                        <thead class="table-light">
                            <tr>
                                <th style="width:45%">Name</th>
                                <th style="width:15%">Code</th>
                                <th style="width:20%">College</th>
                                <th style="width:20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Programs -->
            <div class="tab-pane fade" id="programs" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Programs</h5>
                    <button class="btn btn-sm btn-primary" data-entity="program" id="btnAddProgram">Add Program</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableProgram">
                        <thead class="table-light">
                            <tr>
                                <th style="width:45%">Name</th>
                                <th style="width:15%">Code</th>
                                <th style="width:20%">College</th>
                                <th style="width:20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

            <!-- Documents -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Documents to Submit</h5>
                    <button class="btn btn-sm btn-primary" data-entity="document" id="btnAddDocument">Add Document</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped entity-table" id="tableDocument">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50%">Name</th>
                                <th style="width:20%">Code</th>
                                <th style="width:15%">Role</th>
                                <th style="width:15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

    <!-- Modal for Add/Edit -->
    <div class="modal fade" id="entityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="entityModalLabel">Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="entityForm">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="id" id="entityId">
                    <input type="hidden" name="entity" id="entityName">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="entityDisplayName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code (optional)</label>
                            <input type="text" class="form-control" name="code" id="entityCode" placeholder="e.g., CCIS, BSIT">
                        </div>
                        <div class="mb-3 d-none" id="entityParentRow">
                            <label class="form-label" id="entityParentLabel">Parent</label>
                            <select class="form-select" name="parent_id" id="entityParent"></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="entitySubmitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/standard_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
    <script src="../javascript/admin-catalogs.js?v=1" defer></script>
</body>
</html>
