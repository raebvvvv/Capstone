<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';

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

// Auth check
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}
// (Logout handled via dedicated POST form to logout.php)

// Dashboard counts
$total_users = getCount($conn, 'users', 'status = ?', ['approved']);
// Application counts (table may have been dropped; handled gracefully)
$total_applications = getCount($conn, 'rental_requests');
$pending_applications = getCount($conn, 'rental_requests', 'status = ?', ['Pending']);
$approved_applications = getCount($conn, 'rental_requests', 'status = ?', ['Approved']);
$completed_applications = getCount($conn, 'rental_requests', 'status = ?', ['Completed']);

// Example: Add values for chart breakdowns (replace with actual values or queries as needed)
$undergrad = 120;
$grad = 45;
$open = 30;
$total_applications_chart = $undergrad + $grad + $open;

// Fetch admin data from the database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if (!$admin) {
        echo "Admin not found.";
        exit();
    }
} else {
    echo "User ID not set in session.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css?v=5">
</head>
<body>
    <!-- Navbar -->
    <header class="bg-light border-bottom py-3 shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        <img src="images/puplogo.png" alt="Logo" class="center-img" style="height: 30px; margin-right: 10px;">
                        <span class="fw-bold">PUP e-IPMO [Admin.]</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link fw-bold" aria-current="page" href="#">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>                            
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                            <form method="POST" action="logout.php" class="d-inline">
                                <?php csrf_input(); ?>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Logout</button>
                            </form>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Dashboard -->
    <div class="container mt-5">
        <!-- Summary Cards -->
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card users text-center w-100">
                    <h2><?= $total_users ?></h2>
                    <span>Total Users</span>
                </div>
            </div>
            <div class="col-lg-2 col-md-5 col-6 mb-3 me-5 d-flex justify-content-center">
                <div class="summary-card total text-center w-100">
                    <h2><?= $total_applications ?></h2>
                    <span>Total Applications</span>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card pending text-center w-100">
                    <h2><?= $pending_applications ?></h2>
                    <span>Pending Applications<br><small>• New/Incomplete</small></span>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card approved text-center w-100">
                    <h2><?= $approved_applications ?></h2>
                    <span>Approved Evaluation</span>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-3 d-flex justify-content-center">
                <div class="summary-card completed text-center w-100">
                    <h2><?= $completed_applications ?></h2>
                    <span>Completed Applications</span>
                </div>
            </div>
        </div>

        <!-- Application Overview Section -->
        <div class="dashboard-section mb-4">
            <div class="row align-items-center">
                <h5 class="mb-5"><span class="legend-dot legend-open"></span>Application Overview</h5>
                <div class="col-md-6 chart-container d-flex justify-content-center align-items-center">
                    <canvas id="applicationOverviewChart"></canvas>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                    <div style="font-size: 1.2rem;">
                        <span style="color:#3b82f6;">Undergraduate</span> <b><?= $undergrad ?></b> &nbsp;
                        <span style="color:#a855f7;">Graduate School</span> <b><?= $grad ?></b> &nbsp;
                        <span style="color:#ef4444;">Open University</span> <b><?= $open ?></b> &nbsp; <br><br>
                        <span style="font-weight:600;">| <?= $total_applications_chart ?> Total Applications</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PUP MAIN–Undergraduates Bar Chart -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-undergrad"></span> PUP MAIN–Undergraduates</h5>
            <div class="bar-chart-container">
                <canvas id="mainUndergradChart"></canvas>
            </div>
        </div>

        <!-- Other Branches and Campus Bar Chart -->
        <div class="dashboard-section mb-4">
            <h5><span class="legend-dot legend-grad"></span> Other Branches and Campus</h5>
            <div class="bar-chart-container">
                <canvas id="branchesChart"></canvas>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            © 2024 Polytechnic University of the Philippines | <a href="https://www.pup.edu.ph/terms" target="_blank">Terms of Use</a> | <a href="https://www.pup.edu.ph/privacy" target="_blank">Privacy Statement</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Application Overview Donut Chart
        document.addEventListener('DOMContentLoaded', function() {
            var overviewCanvas = document.getElementById('applicationOverviewChart');
            if (overviewCanvas) {
                new Chart(overviewCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Undergraduate', 'Graduate School', 'Open University'],
                        datasets: [{
                            data: [<?= $undergrad ?>, <?= $grad ?>, <?= $open ?>],
                            backgroundColor: ['#3b82f6', '#a855f7', '#ef4444'],
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            legend: { display: true, position: 'bottom' },
                            title: { display: false }
                        }
                    }
                });
            }
        });

        // PUP MAIN–Undergraduates Bar Chart
        new Chart(document.getElementById('mainUndergradChart'), {
            type: 'bar',
            data: {
                labels: [
                    "College of Tourism, Hospitality and Transportation Management",
                    "Science College",
                    "College of Social Sciences and Development",
                    "College of Political Science and Public Administration",
                    "College of Law",
                    "College of Human Kinetics",
                    "College of Communication",
                    "College of Business Administration",
                    "College of Arts and Letters",
                    "College of Accountancy and Finance",
                    "College of Education",
                    "College of Engineering",
                    "College of Computer and Information Sciences"
                ],
                datasets: [{
                    label: 'Applications',
                    data: [20, 10, 15, 5, 10, 359, 8, 7, 6, 5, 4, 3, 2], // TODO: Replace with actual data
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Other Branches and Campus Bar Chart
        new Chart(document.getElementById('branchesChart'), {
            type: 'bar',
            data: {
                labels: ["San Juan City", "Quezon City", "Taguig City"],
                datasets: [{
                    label: 'Applications',
                    data: [80, 10, 65], // TODO: Replace with actual data
                    backgroundColor: '#a855f7'
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    </script>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" minlength="8" required>
                            <div class="form-text">At least 8 characters.</div>
                        </div>
                        <div class="mb-2">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" minlength="8" required>
                        </div>
                        <div id="changePassAlert" class="alert d-none mt-3" role="alert"></div>
                        <div class="modal-footer px-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle Change Password submit
    document.getElementById('changePasswordForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const cur = document.getElementById('currentPassword').value.trim();
            const pass = document.getElementById('newPassword').value.trim();
            const conf = document.getElementById('confirmPassword').value.trim();
            const alertBox = document.getElementById('changePassAlert');
            const showAlert = (msg, type='danger') => {
                alertBox.className = `alert alert-${type}`;
                alertBox.textContent = msg;
                alertBox.classList.remove('d-none');
            };
            alertBox.classList.add('d-none');

            if (!cur || !pass || !conf) return showAlert('Please fill in all fields.');
            if (pass.length < 8) return showAlert('New password must be at least 8 characters.');
            if (pass !== conf) return showAlert('New password and confirmation do not match.');
            if (cur === pass) return showAlert('New password must be different from current password.');

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('change_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrf
                    },
                    body: JSON.stringify({ current_password: cur, new_password: pass })
                });
                let data;
                const text = await res.text();
                try { data = JSON.parse(text); } catch { data = { success:false, error:text.trim() || 'Unexpected response' }; }
                if (!res.ok) {
                    return showAlert(data.error || `Error ${res.status}`);
                }
                if (data.success) {
                    showAlert('Password updated successfully.', 'success');
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';
                } else {
                    showAlert(data.error || 'Failed to update password.');
                }
            } catch (err) {
                showAlert('Network error. Please try again.');
            }
        });
    </script>
</body>
</html>

