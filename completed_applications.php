<?php
session_start();
require 'conn.php'; // Include your database connection file

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}
// --- Example data for demonstration. Replace with actual DB query as needed. ---
$applications = [
    [
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'name' => 'Jane Doe',
        'date' => 'May 2025',
        // added details payload
        'details' => [
            'student' => [
                'name' => 'Jane Doe',
                'number' => '2022-08960-MN-0',
                'email' => 'Jane@iskolarngbayan.pup.edu.ph',
                'homeAddress' => '',
                'campus' => 'PUP Sta. Mesa, Manila',
                'department' => 'DIT',
                'college' => 'CCIS',
                'program' => 'Bachelor of Science In Information Technology',
            ],
            'document' => [
                'title' => 'Magna aliqua.',
                'author' => 'Jane Doe',
                'dateAccomplished' => '2025-05-01',
                'applicationDate' => '2025-05-31',
            ],
            'files' => [
                ['label' => 'Record of Copyright Application', 'url' => '#'],
                ['label' => 'Journal Publication Format', 'url' => '#'],
                ['label' => 'Notarized Copyright Application Form', 'url' => '#'],
                ['label' => 'Receipt of Payment', 'url' => '#'],
                ['label' => 'Full Manuscript', 'url' => '#'],
                ['label' => 'Approval Sheet', 'url' => '#'],
                ['label' => 'Notarized Co-Authorship', 'url' => '#'],
            ],
            'certificateUrl' => '#'
        ],
    ],
    [
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'name' => 'Victor Magtanggol',
        'date' => 'June 2024',
        'details' => [
            'student' => [
                'name' => 'Victor Magtanggol',
                'number' => '2022-08961-MN-0',
                'email' => 'Victor@iskolarngbayan.pup.edu.ph',
                'homeAddress' => '',
                'campus' => 'PUP Sta. Mesa, Manila',
                'department' => 'CIT',
                'college' => 'CCIS',
                'program' => 'Bachelor of Science In Computer Science',
            ],
            'document' => [
                'title' => 'Dolore magna aliqua.',
                'author' => 'Victor Magtanggol',
                'dateAccomplished' => '2024-06-01',
                'applicationDate' => '2024-06-30',
            ],
            'files' => [
                ['label' => 'Record of Copyright Application', 'url' => '#'],
                ['label' => 'Journal Publication Format', 'url' => '#'],
                ['label' => 'Notarized Copyright Application Form', 'url' => '#'],
                ['label' => 'Receipt of Payment', 'url' => '#'],
                ['label' => 'Full Manuscript', 'url' => '#'],
                ['label' => 'Approval Sheet', 'url' => '#'],
                ['label' => 'Notarized Co-Authorship', 'url' => '#'],
            ],
            'certificateUrl' => '#'
        ],
    ],
    [
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'name' => 'Juan Dela Cruz',
        'date' => 'July 2023',
        'details' => [
            'student' => [
                'name' => 'Juan Dela Cruz',
                'number' => '2022-08962-MN-0',
                'email' => 'Juan@iskolarngbayan.pup.edu.ph',
                'homeAddress' => '',
                'campus' => 'PUP Sta. Mesa, Manila',
                'department' => 'CIT',
                'college' => 'CCIS',
                'program' => 'Bachelor of Science In Information Technology',
            ],
            'document' => [
                'title' => 'Incididunt ut labore et dolore magna aliqua.',
                'author' => 'Juan Dela Cruz',
                'dateAccomplished' => '2023-07-01',
                'applicationDate' => '2023-07-31',
            ],
            'files' => [
                ['label' => 'Record of Copyright Application', 'url' => '#'],
                ['label' => 'Journal Publication Format', 'url' => '#'],
                ['label' => 'Notarized Copyright Application Form', 'url' => '#'],
                ['label' => 'Receipt of Payment', 'url' => '#'],
                ['label' => 'Full Manuscript', 'url' => '#'],
                ['label' => 'Approval Sheet', 'url' => '#'],
                ['label' => 'Notarized Co-Authorship', 'url' => '#'],
            ],
            'certificateUrl' => '#'
        ],
    ],
    [
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'name' => 'Klein Calvin',
        'date' => 'Oct 2022',
        'details' => [
            'student' => [
                'name' => 'Klein Calvin',
                'number' => '2022-08963-MN-0',
                'email' => 'Klein@iskolarngbayan.pup.edu.ph',
                'homeAddress' => '',
                'campus' => 'PUP Sta. Mesa, Manila',
                'department' => 'DIT',
                'college' => 'CCIS',
                'program' => 'Bachelor of Science In Information Technology',
            ],
            'document' => [
                'title' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'author' => 'Klein Calvin',
                'dateAccomplished' => '2022-10-01',
                'applicationDate' => '2022-10-31',
            ],
            'files' => [
                ['label' => 'Record of Copyright Application', 'url' => '#'],
                ['label' => 'Journal Publication Format', 'url' => '#'],
                ['label' => 'Notarized Copyright Application Form', 'url' => '#'],
                ['label' => 'Receipt of Payment', 'url' => '#'],
                ['label' => 'Full Manuscript', 'url' => '#'],
                ['label' => 'Approval Sheet', 'url' => '#'],
                ['label' => 'Notarized Co-Authorship', 'url' => '#'],
            ],
            'certificateUrl' => '#'
        ],
    ],
];

// Fetch tools from the database
$sql = "SELECT * FROM tools"; // Adjust table name if necessary
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/completed_applications.css?v=5">
    <title>Completed Applications</title>
</head>
<body>
    <!-- Add backdrop overlay right after body -->
    <div id="dropdown-backdrop" class="dropdown-backdrop"></div>

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
                            <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="#">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="ticket.php">Applications</a></li>
                            <a href="?logout=true" class="btn btn-primary rounded-pill px-4">Logout</a>
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
            <form class="ipapp-search" method="get">
                <input type="search" class="ipapp-search-input" placeholder="Search" name="search">
                <button type="submit" class="ipapp-search-icon" aria-label="Search">
                    <svg width="20" height="20" fill="none"><circle cx="9" cy="9" r="7.5" stroke="#222"/><path stroke="#222" stroke-linecap="round" d="M17.5 17.5l-4.5-4.5"/></svg>
                </button>
            </form>
            <div class="ipapp-header-options">
                <button type="button" class="ipapp-dropdown" id="othersBtn">Others ▼</button>
                <button type="button" class="ipapp-dropdown" id="allTimeBtn">All time ▼</button>
                <a href="#" class="ipapp-download-btn">Download Summary</a>
            </div>
        </header>

        <!-- All Time Dropdown -->
        <div id="allTimeDropdownMenu" style="display:none; position:absolute; background:#fff; border:1px solid #ddd; border-radius:6px; min-width:140px; z-index:10; margin-top:2px;">
            <button class="dropdown-item" type="button" data-range="today">Today</button>
            <button class="dropdown-item" type="button" data-range="thismonth">This Month</button>
            <button class="dropdown-item" type="button" data-range="thisyear">This Year</button>
            <button class="dropdown-item" type="button" data-range="custom">Custom</button>
        </div>
        <!-- Calendar for custom date range -->
        <div id="calendarSection" style="display:none; margin-top:10px;">
            <label>Start Date: <input type="date" id="startDate"></label>
            <label style="margin-left:10px;">End Date: <input type="date" id="endDate"></label>
            <button type="button" class="ipapp-download-btn" style="margin-left:10px;">Apply</button>
        </div>
        <hr class="ipapp-divider">
        <!-- Filters Bar: hidden by default, shown when Others is clicked -->
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
        <main class="ipapp-list">
            <?php foreach ($applications as $app): ?>
                <?php
                    // pack details as base64-encoded JSON for safe embedding in attribute
                    $detailsAttr = isset($app['details'])
                        ? htmlspecialchars(base64_encode(json_encode($app['details'])), ENT_QUOTES, 'UTF-8')
                        : '';
                ?>
                <div class="ipapp-list-item">
                    <a href="#"
                       class="ipapp-desc ipapp-desc-link"
                       data-details="<?php echo $detailsAttr; ?>">
                        <?php echo htmlspecialchars($app['description']); ?>
                    </a>
                    <div class="ipapp-userdate">
                        <a href="#" class="ipapp-user-link"><?php echo htmlspecialchars($app['name']); ?>, <?php echo htmlspecialchars($app['date']); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsGModal" tabindex="-1" aria-labelledby="detailsGModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content gmodal-content">
                <div class="modal-header border-0 pt-3 pb-0">
                    <h5 class="modal-title gmodal-title" id="detailsGModalLabel">Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="m-0 mb-3">
                <div class="modal-body" id="gmodalBody">
                    <!-- injected by JS -->
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-danger btn-sm" id="gViewCertBtn">View Certificate</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Modal (Completed Applications) -->
    <div class="modal fade" id="certificateModalCA" tabindex="-1" aria-labelledby="certificateModalCALabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalCALabel">Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- intentionally left blank -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-certificate" data-bs-dismiss="modal">Close</button>
                    <!-- optional: keep download if you later wire a URL -->
                    <a id="downloadCertificateBtnCA" href="#" class="btn btn-download-pdf" style="display:none;">Download as PDF</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Others button toggles filters bar visibility
    document.getElementById('othersBtn').onclick = function() {
        var bar = document.getElementById('filtersBar');
        bar.style.display = (bar.style.display === 'none' || bar.style.display === '') ? 'flex' : 'none';
    };

    // Backdrop element
    const backdrop = document.getElementById('dropdown-backdrop');
    
    // Generic mini dropdown logic with Google-style behavior
    document.querySelectorAll('.ipapp-mini-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const targetId = this.getAttribute('data-target');
            const menu = document.getElementById(targetId);
            
            // Check if menu is currently shown
            const isOpen = menu.style.display === 'block';
            
            // Close all menus first
            closeAllMiniMenus();
            
            if (isOpen) {
                return; // Menu was open, now closed by closeAllMiniMenus
            }
            
            // Show backdrop
            backdrop.classList.add('active');
            
            // Show this menu (CSS positions it relative to the parent dropdown)
            menu.style.display = 'block';
            menu.classList.add('menu-active');
        });
    });

    // Make the closeAllMiniMenus function simpler
    const closeAllMiniMenus = () => {
        document.querySelectorAll('.ipapp-mini-menu').forEach(m => {
            m.style.display = 'none';
            m.classList.remove('menu-active');
        });
        backdrop.classList.remove('active');
    };

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.ipapp-mini-dropdown')) {
            document.querySelectorAll('.ipapp-mini-menu').forEach(menu => {
                menu.classList.remove('menu-active');
            });
            backdrop.classList.remove('active');
        }
    });

    // Update label on selection
    document.querySelectorAll('.ipapp-mini-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(){
            const menu = this.closest('.ipapp-mini-menu');
            const btn = document.querySelector('.ipapp-mini-btn[data-target="'+ menu.id +'"]');
            const selectedText = this.textContent.trim();
            btn.textContent = selectedText + ' ▼';
            closeAllMiniMenus();
        });
    });

    // All time dropdown logic - updated to use backdrop
    document.getElementById('allTimeBtn').onclick = function(e) {
        e.stopPropagation();
        const menu = document.getElementById('allTimeDropdownMenu');
        const isOpen = menu.classList.contains('menu-active');
        closeAllMiniMenus();
        if (isOpen) {
            menu.classList.remove('menu-active');
            menu.style.display = 'none';
            return;
        }
        backdrop.classList.add('active');
        const rect = this.getBoundingClientRect();
        menu.style.left = rect.left + 'px';
        menu.style.top = (rect.bottom + window.scrollY) + 'px';
        menu.style.display = 'block';
        menu.classList.add('menu-active');
    };
    
    document.querySelectorAll('#allTimeDropdownMenu .dropdown-item').forEach(function(btn){
        btn.onclick = function() {
            var range = btn.getAttribute('data-range');
            closeAllMiniMenus();
            if (range === 'custom') {
                document.getElementById('calendarSection').style.display = 'block';
            } else {
                document.getElementById('calendarSection').style.display = 'none';
            }
        };
    });

    // Click backdrop or body to close menus
    backdrop.addEventListener('click', function() {
        document.getElementById('allTimeDropdownMenu').classList.remove('menu-active');
        document.getElementById('allTimeDropdownMenu').style.display = 'none';
        closeAllMiniMenus();
    });
    document.body.addEventListener('click', function() {
        document.getElementById('allTimeDropdownMenu').classList.remove('menu-active');
        document.getElementById('allTimeDropdownMenu').style.display = 'none';
        closeAllMiniMenus();
    });

    // Prevent closing when clicking inside dropdowns
    document.querySelectorAll('.ipapp-mini-menu').forEach(m => {
        m.addEventListener('click', e => e.stopPropagation());
    });
    document.getElementById('allTimeDropdownMenu').onclick = function(e){
        e.stopPropagation();
    };

    // Open Google-like Details modal from description click
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.ipapp-desc-link');
        if (!link) return;
        e.preventDefault();

        const payload = link.getAttribute('data-details') || '';
        let details = null;
        try { details = payload ? JSON.parse(atob(payload)) : null; } catch (err) { details = null; }

        // helper
        const v = (x, d='') => (x === undefined || x === null || x === '' ? d : x);

        // build HTML
        const s = details?.student || {};
        const d = details?.document || {};
        const files = Array.isArray(details?.files) ? details.files : [];

        const filesHTML = files.map(f => {
            const url = f.url || '';
            const safeHref = url || '#';
            const disabledAttrs = url ? '' : 'aria-disabled="true" tabindex="-1"';
            const downloadAttrs = url ? 'download' : disabledAttrs;
            const viewAttrs = url ? 'target="_blank" rel="noopener"' : disabledAttrs;
            return `
                <div class="g-file-row">
                    <span>${f.label}</span>
                    <div class="g-chip-group">
                        <a class="g-chip" href="${safeHref}" ${downloadAttrs}>Download File</a>
                        <a class="g-chip g-chip-view" href="${safeHref}" ${viewAttrs}>View File</a>
                    </div>
                </div>
            `;
        }).join('');

        const html = `
            <div class="g-section">
                <h4 class="g-section-title">Student Information</h4>
                <div class="g-line"><strong>Name:</strong> ${v(s.name, '')}</div>
                <div class="g-line"><strong>Student Number:</strong> ${v(s.number, '')}</div>
                <div class="g-line"><strong>Email Address:</strong> ${s.email ? `<a href="mailto:${s.email}">${s.email}</a>` : ''}</div>
                <div class="g-line"><strong>Home Address:</strong> ${v(s.homeAddress, '')}</div>
                <div class="g-line"><strong>Campus:</strong> ${v(s.campus, '')}</div>
                <div class="g-line"><strong>Department:</strong> ${v(s.department, '')}</div>
                <div class="g-line"><strong>College:</strong> ${v(s.college, '')}</div>
                <div class="g-line"><strong>Program:</strong> ${v(s.program, '')}</div>
            </div>
            <div class="g-section">
                <h4 class="g-section-title">Document Information</h4>
                <div class="g-line"><strong>Title:</strong> ${v(d.title, '')}</div>
                <div class="g-line"><strong>Author/s Full name/s:</strong> ${v(d.author, '')}</div>
                <div class="g-line"><strong>Date Accomplished for the Work:</strong> ${v(d.dateAccomplished, '')}</div>
                <div class="g-line"><strong>Application Date Accomplished:</strong> ${v(d.applicationDate, '')}</div>
            </div>
            <div class="g-files">
                ${filesHTML}
            </div>
        `;

        document.getElementById('gmodalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('detailsGModal')).show();
    });

    // View Certificate: just open a blank window (no iframe/content)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('#gViewCertBtn');
        if (!btn) return;
        e.preventDefault();

        // Hide details, then show blank certificate modal
        const detailsInst = bootstrap.Modal.getInstance(document.getElementById('detailsGModal'));
        if (detailsInst) detailsInst.hide();

        new bootstrap.Modal(document.getElementById('certificateModalCA')).show();
    });
    </script>
</body>
</html>





