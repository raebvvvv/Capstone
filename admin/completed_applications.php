<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();
// Fetch admin for profile modal
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $admin = $res->fetch_assoc();
        $stmt->close();
    }
}
$applications = [
    [
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'name' => 'Jane Doe','date' => 'May 2025',
        'details' => [
            'student' => [ 'name' => 'Jane Doe','number' => '2022-08960-MN-0','email' => 'Jane@iskolarngbayan.pup.edu.ph','homeAddress' => '','campus' => 'PUP Sta. Mesa, Manila','department' => 'DIT','college' => 'CCIS','program' => 'Bachelor of Science In Information Technology' ],
            'document' => [ 'title' => 'Magna aliqua.','author' => 'Jane Doe','dateAccomplished' => '2025-05-01','applicationDate' => '2025-05-31' ],
            'files' => [ ['label' => 'Record Copyright Application','url' => '#'],['label' => 'Journal Publication Format','url' => '#'],['label' => 'Notarized Copyright Application','url' => '#'],['label' => 'Receipt of Payment','url' => '#'],['label' => 'Full Manuscript','url' => '#'],['label' => 'Approval Sheet','url' => '#'],['label' => 'Notarized Co-Authorship','url' => '#'] ],
            'certificateUrl' => '#'
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/completed_applications.css?v=4">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>">
    <title>Completed Applications</title>
</head>
<body>
    <div id="dropdown-backdrop" class="dropdown-backdrop"></div>
    <header class="bg-light border-bottom py-3 shadow-sm" data-admin-name="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" data-admin-email="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>">
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
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="#">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
                            <button type="button" class="btn btn-outline-secondary px-3 me-2" data-bs-toggle="modal" data-bs-target="#adminProfileModal">My Profile</button>
                            <form method="POST" action="../logout.php" class="d-inline">
                                <?php csrf_input(); ?>
                                <button type="submit" class="btn btn-logout px-4">Logout</button>
                            </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <div class="ipapp-container">
        <header class="ipapp-header">
            <span class="ipapp-title">IP APPLICATIONS</span>
            <form class="ipapp-search" method="get" id="ipappSearchForm">
                <input type="search" class="ipapp-search-input" id="ipappSearch" placeholder="Search by description, name, date" autocomplete="off">
                <button type="button" class="ipapp-search-icon" aria-label="Search" id="ipappSearchBtn">
                    <svg width="20" height="20" fill="none"><circle cx="9" cy="9" r="7.5" stroke="#222"/><path stroke="#222" stroke-linecap="round" d="M17.5 17.5l-4.5-4.5"/></svg>
                </button>
            </form>
            <div class="ipapp-header-options">
                <button type="button" class="ipapp-dropdown" id="othersBtn">Others ▼</button>
                <button type="button" class="ipapp-dropdown" id="allTimeBtn">All time ▼</button>
                <a href="#" class="ipapp-download-btn">Download Summary</a>
            </div>
        </header>
        <div id="allTimeDropdownMenu" style="display:none; position:absolute; background:#fff; border:1px solid #ddd; border-radius:6px; min-width:140px; z-index:10; margin-top:2px;">
            <button class="dropdown-item" type="button" data-range="today">Today</button>
            <button class="dropdown-item" type="button" data-range="thismonth">This Month</button>
            <button class="dropdown-item" type="button" data-range="thisyear">This Year</button>
            <button class="dropdown-item" type="button" data-range="custom">Custom</button>
        </div>
        <div id="calendarSection" style="display:none; margin-top:10px;">
            <label>Start Date: <input type="date" id="startDate"></label>
            <label style="margin-left:10px;">End Date: <input type="date" id="endDate"></label>
            <button type="button" class="ipapp-download-btn" style="margin-left:10px;">Apply</button>
        </div>
        <hr class="ipapp-divider">
        <div id="filtersBar" class="ipapp-filters-bar" style="display:none;">
            <div class="ipapp-mini-dropdown" style="position:relative;">
                <button class="ipapp-mini-btn" data-target="campusMenu">Campus<span>▼</span></button>
                <div class="ipapp-mini-menu" id="campusMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">Main</button>
                    <button class="dropdown-item" type="button">San Juan</button>
                    <button class="dropdown-item" type="button">Sta. Mesa</button>
                </div>
            </div>
            <div class="ipapp-mini-dropdown">
                <button class="ipapp-mini-btn" data-target="collegeMenu">College<span>▼</span></button>
                <div class="ipapp-mini-menu" id="collegeMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">COE</button>
                    <button class="dropdown-item" type="button">CBA</button>
                    <button class="dropdown-item" type="button">CSSD</button>
                </div>
            </div>
            <div class="ipapp-mini-dropdown" style="position:relative;">
                <button class="ipapp-mini-btn" data-target="departmentMenu">Department<span>▼</span></button>
                <div class="ipapp-mini-menu" id="departmentMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">IT</button>
                    <button class="dropdown-item" type="button">CS</button>
                    <button class="dropdown-item" type="button">ECE</button>
                </div>
            </div>
            <div class="ipapp-mini-dropdown" style="position:relative;">
                <button class="ipapp-mini-btn" data-target="programMenu">Program<span>▼</span></button>
                <div class="ipapp-mini-menu" id="programMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">BSIT</button>
                    <button class="dropdown-item" type="button">BSCS</button>
                    <button class="dropdown-item" type="button">BSECE</button>
                </div>
            </div>
            <div class="ipapp-mini-dropdown" style="position:relative;">
                <button class="ipapp-mini-btn" data-target="typesMenu">Types<span>▼</span></button>
                <div class="ipapp-mini-menu" id="typesMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">Patent</button>
                    <button class="dropdown-item" type="button">Utility Model</button>
                    <button class="dropdown-item" type="button">Copyright</button>
                </div>
            </div>
            <div class="ipapp-mini-dropdown" style="position:relative;">
                <button class="ipapp-mini-btn" data-target="groupMenu">Group<span>▼</span></button>
                <div class="ipapp-mini-menu" id="groupMenu">
                    <button class="dropdown-item" type="button">All</button>
                    <button class="dropdown-item" type="button">Faculty</button>
                    <button class="dropdown-item" type="button">Student</button>
                    <button class="dropdown-item" type="button">External</button>
                </div>
            </div>
            <button class="ipapp-go-btn">Go</button>
        </div>
        <main class="ipapp-list" id="ipappList">
            <?php foreach ($applications as $app): ?>
                <?php $detailsAttr = isset($app['details']) ? htmlspecialchars(base64_encode(json_encode($app['details'])), ENT_QUOTES, 'UTF-8') : ''; ?>
                <div class="ipapp-list-item" data-description="<?php echo htmlspecialchars(strtolower($app['description'])); ?>" data-name="<?php echo htmlspecialchars(strtolower($app['name'])); ?>" data-date="<?php echo htmlspecialchars(strtolower($app['date'])); ?>">
                    <a href="#" class="ipapp-desc ipapp-desc-link" data-details="<?php echo $detailsAttr; ?>">
                        <?php echo htmlspecialchars($app['description']); ?>
                    </a>
                    <div class="ipapp-userdate">
                        <a href="#" class="ipapp-user-link"><?php echo htmlspecialchars($app['name']); ?>, <?php echo htmlspecialchars($app['date']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <div class="modal fade" id="detailsGModal" tabindex="-1" aria-labelledby="detailsGModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content gmodal-content">
                <div class="modal-header border-0 pt-3 pb-0">
                    <h5 class="modal-title gmodal-title" id="detailsGModalLabel">Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="m-0 mb-3">
                <div class="modal-body" id="gmodalBody"></div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-danger btn-sm" id="gViewCertBtn">View Certificate</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="certificateModalCA" tabindex="-1" aria-labelledby="certificateModalCALabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalCALabel">Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-certificate" data-bs-dismiss="modal">Close</button>
                    <a id="downloadCertificateBtnCA" href="#" class="btn btn-download-pdf" style="display:none;">Download as PDF</a>
                </div>
            </div>
        </div>
    </div>

<script src="../javascript/admin-completed-applications.js"></script>
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
  <footer class="bg-white border-top py-3">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>
</body>
</html>
