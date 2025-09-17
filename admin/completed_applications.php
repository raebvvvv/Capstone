<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();
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
    <link rel="stylesheet" href="../css/completed_applications.css?v=5">
    <title>Completed Applications</title>
</head>
<body>
    <div id="dropdown-backdrop" class="dropdown-backdrop"></div>
    <header class="bg-light border-bottom py-3 shadow-sm">
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
                            <form method="POST" action="../logout.php" class="d-inline">
                                <?php csrf_input(); ?>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Logout</button>
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
</body>
</html>
