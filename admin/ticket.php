<?php
require __DIR__ . '/../security_bootstrap.php';
secure_bootstrap();
require __DIR__ . '/../conn.php';
require_admin();

function table_exists(mysqli $conn, string $table): bool {
    if (!$conn) return false;
    if ($stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1")) {
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = (bool)$res->fetch_row();
        $stmt->close();
        return $exists;
    }
    return false;
}

// Check if a specific column exists in a given table
function column_exists(mysqli $conn, string $table, string $column): bool {
    if ($stmt = $conn->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1")) {
        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = (bool)$res->fetch_row();
        $stmt->close();
        return $exists;
    }
    return false;
}

// Build a safe SELECT clause that aliases missing columns to NULL, so queries won't fail
function build_safe_select_clause(mysqli $conn, string $table, array $columns): string {
    $parts = [];
    foreach ($columns as $col) {
        if (column_exists($conn, $table, $col)) {
            // Use backticks to avoid reserved word conflicts
            $parts[] = "`$col`";
        } else {
            // Alias a literal NULL to the expected column name
            $parts[] = "NULL AS `$col`";
        }
    }
    return implode(', ', $parts);
}

$requests_table_exists = table_exists($conn, 'rental_requests');
$search_query = "";
if ($requests_table_exists) {
    // Columns we expect to display; missing ones will be aliased
    $expected_columns = [
        'request_id', 'student_id', 'student_name', 'user_classification',
        'program', 'request_date', 'status', 'remark',
        'approved_timestamp', 'completed_timestamp'
    ];
    $select_clause = build_safe_select_clause($conn, 'rental_requests', $expected_columns);

    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        $query_all = "SELECT $select_clause FROM `rental_requests` WHERE `student_name` LIKE ?";
        if ($stmt_search = $conn->prepare($query_all)) {
            $search_param = "%" . $search_query . "%";
            $stmt_search->bind_param("s", $search_param);
            if ($stmt_search->execute()) {
                $result_all = $stmt_search->get_result();
            } else {
                log_event('DB_ERROR', 'Ticket search execute failed', ['error' => $stmt_search->error]);
            }
        } else {
            log_event('DB_ERROR', 'Ticket search prepare failed', ['error' => $conn->error]);
        }
    } else {
        $query_all = "SELECT $select_clause FROM `rental_requests`";
        $result_all = $conn->query($query_all);
        if (!$result_all) { log_event('DB_ERROR', 'Ticket list query failed', ['error' => $conn->error]); }
    }
    if (!isset($result_all) || !$result_all) {
        echo "An error occurred loading requests.";
        $result_all = new class { public function fetch_assoc(){ return null; } public function data_seek($n){} };
    }
} else {
    log_event('DB_WARN', 'rental_requests table missing in ticket.php');
    $result_all = new class { public function fetch_assoc(){ return null; } public function data_seek($n){} };
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username, email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
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
    <link rel="stylesheet" href="../css/ticket.css?v=6">
    <title>Manage Requests</title>
</head>
<body>
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
                            <li class="nav-item"><a class="nav-link" href="completed_applications.php">Completed Applications</a></li>
                            <li class="nav-item"><a class="nav-link" aria-current="page" href="manageuser.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="ticket.php">Applications</a></li>
                            <form action="../logout.php" method="post" class="d-inline ms-2">
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

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="font-size:2rem;">Manage Applications</h1>
        <div class="d-flex justify-content-center mb-3">
            <div class="input-group search-bar" style="max-width:400px;">
                <input class="form-control rounded-pill ps-4" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 50px;">
                <span class="input-group-text bg-white border-0" style="border-radius: 50px; margin-left:-40px;">
                    <i class="bi bi-search"></i>
                </span>
            </div>
        </div>
        <div class="d-flex justify-content-center mb-3 gap-2">
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Ethics Clearance</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Patent</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Industrial Design</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Utility Model</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Trademark</button></a>
            <a href="#"><button class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm">Copyright</button></a>
        </div>
        <ul class="nav nav-tabs mb-3" id="requestTabs">
            <li class="nav-item"><a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#pending" style="color:#222;">Pending</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold" data-bs-toggle="tab" href="#approved" style="color:#222;">Approved</a></li>
            <li class="nav-item"><a class="nav-link fw-semibold" data-bs-toggle="tab" href="#completed" style="color:#222;">Complete</a></li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="pending">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Remarks</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SRID-2025-0808-001</td>
                                <td>2022-08960-MN-0</td>
                                <td>Dela Cruz, Juan P.</td>
                                <td>Student</td>
                                <td>CCIS</td>
                                <td>2025-08-08</td>
                                <td>For Evaluation</td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                        <form method="post" style="display:inline;">
                                            <?php csrf_input(); ?>
                                            <input type="hidden" name="approve_ticket_id" value="SRID-2025-0808-001">
                                            <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Approve</button>
                                        </form>
                                        <span class="btn btn-incomplete btn-sm rounded-pill px-3">Incomplete</span>
                                    </div>
                                </td>
                            </tr>
                            <?php while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Pending'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket['request_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['user_classification']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['program']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['remark'] ?? 'For Evaluation'); ?></td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                                <form method="post" style="display:inline;">
                                                    <?php csrf_input(); ?>
                                                    <input type="hidden" name="approve_ticket_id" value="<?php echo htmlspecialchars($ticket['request_id']); ?>">
                                                    <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Approve</button>
                                                </form>
                                                <span class="btn btn-incomplete btn-sm rounded-pill px-3">Incomplete</span>
                                            </div>
                                        </td>    
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="approved">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SRID-2025-0808-002</td>
                                <td>2022-08961-MN-0</td>
                                <td>Reyes, Maria L.</td>
                                <td>Student</td>
                                <td>COE</td>
                                <td>2025-08-09</td>
                                <td>In-review</td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                        <form method="post" style="display:inline;">
                                            <?php csrf_input(); ?>
                                            <input type="hidden" name="approve_ticket_id" value="SRID-2025-0808-002">
                                            <button type="submit" class="btn btn-approve btn-sm rounded-pill px-3">Complete</button>
                                        </form>
                                        <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                                    </div>
                                </td>
                            </tr>
                            <?php $result_all->data_seek(0); while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Approved'): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket['request_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['user_classification']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['program']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['request_date']); ?></td>
                                        <td>In-review</td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                                <button class="btn btn-success btn-sm rounded-pill px-3 me-1" onclick="showCompleteModal('<?php echo htmlspecialchars($ticket['request_id']); ?>')">Complete</button>
                                                <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="completed">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student Number / Employee ID</th>
                                <th>Name</th>
                                <th>Classification</th>
                                <th>Program</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $result_all->data_seek(0); while ($ticket = $result_all->fetch_assoc()): ?>
                                <?php if ($ticket['status'] == 'Completed'): ?>
                                    <tr>
                                        <td><span style="font-weight:600;">SRID-2025-0808-001</span></td>
                                        <td>2022-08960-MN-0</td>
                                        <td>Dela Cruz, Juan P.</td>
                                        <td>Student</td>
                                        <td>CCIS</td>
                                        <td>2025-08-08</td>
                                        <td>Completed</td>
                                        <td>Complete</td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="#" class="btn btn-success btn-sm rounded-pill px-3 btn-view-certificate" data-cert-url="#">View Certificate</a>
                                                <a href="#" class="btn btn-view btn-sm rounded-pill px-3">View Details</a>
                                                <a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">Certificate of Copyright Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-certificate" data-bs-dismiss="modal">Close</button>
                    <a id="downloadCertificateBtn" href="#" class="btn btn-download-pdf" download>Download as PDF</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <?php csrf_input(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="completeModalLabel">Complete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="complete_comments" class="form-label mb-2">Comments:</label>
                        <textarea class="form-control" id="complete_comments" name="remark" rows="7" placeholder="Add your Comments here..." style="resize:none;"></textarea>
                        <input type="hidden" name="complete_ticket_id" id="complete_ticket_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background:#6c757d;">Close</button>
                        <button type="submit" class="btn" style="background:#7c3aed; color:#fff;">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="incompleteModal" tabindex="-1" aria-labelledby="incompleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteModalLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-remarks-modal dropdown-toggle" type="button" id="remarksDropdown" data-bs-toggle="dropdown" aria-expanded="false">Remarks</button>
                            <ul class="dropdown-menu" aria-labelledby="remarksDropdown">
                                <li><a class="dropdown-item" href="#" onclick="selectRemark('Incorrect Document/Upload')">Incorrect Document/Upload</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectRemark('Error in Document/Upload')">Error in Document/Upload</a></li>
                            </ul>
                        </div>
                        <br><span><strong>Comments:</strong></span>
                    </div>
                    <textarea id="incompleteTextarea" rows="7" class="form-control mb-2" style="resize:none; border:1px solid #555;"></textarea>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="confirmIncompleteBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="incompleteActiveModal" tabindex="-1" aria-labelledby="incompleteActiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content incomplete-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <h5 class="modal-title fw-bold mb-0" id="incompleteActiveModalLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-remarks-modal dropdown-toggle" type="button" id="remarksDropdownActive" data-bs-toggle="dropdown" aria-expanded="false">Remarks</button>
                            <ul class="dropdown-menu" aria-labelledby="remarksDropdownActive">
                                <li><a class="dropdown-item" href="#" onclick="selectRemarkActive('Missing Document')">Missing Document</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectRemarkActive('Error in Document')">Error in Document</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectRemarkActive('Documents don’t match')">Documents don’t match</a></li>
                            </ul>
                        </div>
                        <span><strong>Comments:</strong></span>
                    </div>
                    <textarea id="incompleteTextareaActive" rows="7" class="form-control mb-2" style="resize:none; border:1px solid #555;"></textarea>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-confirm-modal" id="confirmIncompleteActiveBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content details-modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title fw-bold mb-0" id="detailsModalLabel">Request Details</h5>
                        <button type="button" class="btn btn-sm btn-edit-modal" id="editDetailsBtn">Edit</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="m-0 mb-3">
                <div class="modal-body" id="detailsModalBody"></div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-save-modal" id="saveDetailsBtn" style="display:none;">Save</button>
                </div>
            </div>
        </div>
    </div>

<script>
function escapeHTML(str){ return String(str).replace(/[&<>"]|'|/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];}); }
let currentDetails = {}; let currentAuthorIndex = null;
function renderDetails(details, editMode = false) {
    const v = (x, d='') => (x === undefined || x === null ? d : x);
    const files = details.files || {};
    const items = [
        ['Record of Copyright Application', files.recordOfCopyrightApplication || ''],
        ['Journal Publication Format', files.journalPublicationFormat || ''],
        ['Notarized Copyright Application Form', files.notarizedCopyrightApplicationForm || ''],
        ['Receipt of Payment', files.receiptOfPayment || ''],
        ['Full Manuscript', files.fullManuscript || ''],
        ['Approval Sheet', files.approvalSheet || ''],
        ['Notarized Co-Authorship', files.notarizedCoAuthorship || ''],
    ];
    const attachRow = (label, url) => { const hasUrl = !!url; const safeUrl = hasUrl ? url : '#'; const disabledAttrs = hasUrl ? '' : 'class="disabled" aria-disabled="true" tabindex="-1"'; return `<li class="d-flex justify-content-between align-items-center mb-2"><span>${label}</span><div class="d-flex gap-2"><a class="btn btn-download btn-sm" href="${safeUrl}" ${hasUrl ? 'download' : disabledAttrs}>Download File</a><a class="btn btn-view-file btn-sm" href="${safeUrl}" target="_blank" rel="noopener" ${hasUrl ? '' : disabledAttrs}>View File</a></div></li>`; };
    const attachmentsHTML = `<ul class="list-unstyled mb-0">${items.map(([l,u]) => attachRow(l,u)).join('')}</ul>`;
    if (!Array.isArray(details.additionalAuthors)) {
        const raw = (details.authorName || '').trim();
        const names = raw ? raw.split(/\s*,\s*|\s*;\s*|\s*\n\s*/).filter(Boolean) : [];
        const primary = details.studentName || names.shift() || '';
        details.studentName = primary;
        details.additionalAuthors = names.filter(n => n && n !== primary).map(n => ({ name: n, studentNumber: '', email: '', address: '', phone: '', campus: '', department: '', college: '', program: '' }));
    }
    if (!details.additionalAuthors) details.additionalAuthors = [];
    if (!details._exampleSeeded && details.additionalAuthors.length === 0) {
        details.additionalAuthors.push({ name: 'Jane Doe', studentNumber: '2022-08860-MN-0', email: 'jane@iskolarngbayan.pup.edu.ph', address: '', phone: '', campus: 'PUP Sta. Mesa, Manila', department: 'DIT', college: 'CCIS', program: 'Bachelor of Science In Information Technology' });
        details._exampleSeeded = true;
    }
    const authorsListHTML = (details.additionalAuthors || []).map((a, idx) => `<div class="author-entry"><span class="author-name">${a.name || '—'}</span><button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2 author-view-btn" data-author-index="${idx}">View Details</button></div>`).join('');
    const modalBody = document.getElementById('detailsModalBody');
    modalBody.innerHTML = `<div><h5>Student Information</h5><p><strong>Name:</strong> ${editMode ? `<input type='text' id='editStudentName' value='${escapeHTML(v(details.studentName, ''))}' />` : escapeHTML(v(details.studentName, '—'))}</p><p><strong>Student Number:</strong> ${editMode ? `<input type='text' id='editStudentNumber' value='${escapeHTML(v(details.studentNumber, ''))}' />` : escapeHTML(v(details.studentNumber, '—'))}</p><p><strong>Email Address:</strong> ${editMode ? `<input type='email' id='editEmail' value='${escapeHTML(v(details.email, ''))}' />` : escapeHTML(v(details.email, '—'))}</p><p><strong>Home Address:</strong> ${editMode ? `<input type='text' id='editHomeAddress' value='${escapeHTML(v(details.homeAddress, ''))}' />` : escapeHTML(v(details.homeAddress, '—'))}</p><p><strong>Campus:</strong> ${editMode ? `<input type='text' id='editCampus' value='${escapeHTML(v(details.campus, ''))}' />` : escapeHTML(v(details.campus, '—'))}</p><p><strong>Department:</strong> ${editMode ? `<input type='text' id='editDepartment' value='${escapeHTML(v(details.department, ''))}' />` : escapeHTML(v(details.department, '—'))}</p><p><strong>College:</strong> ${editMode ? `<input type='text' id='editCollege' value='${escapeHTML(v(details.college, ''))}' />` : escapeHTML(v(details.college, '—'))}</p><p><strong>Program:</strong> ${editMode ? `<input type='text' id='editProgram' value='${escapeHTML(v(details.program, ''))}' />` : escapeHTML(v(details.program, '—'))}</p></div><div><h5>Document Information</h5><p><strong>Title:</strong> ${editMode ? `<input type='text' id='editDocumentTitle' value='${escapeHTML(v(details.documentTitle, ''))}' />` : escapeHTML(v(details.documentTitle, '—'))}</p><p><strong>Author/s Full name/s:</strong> ${escapeHTML(v(details.studentName, '—'))}</p>${authorsListHTML ? `<div class="mt-2"><div class="fw-semibold mb-1">Additional Author(s)</div>${authorsListHTML}</div>` : ''}<p class="mt-2"><strong>Date Accomplished:</strong> ${editMode ? `<input type='date' id='editAccomplishmentDate' value='${escapeHTML(v(details.accomplishmentDate, ''))}' />` : escapeHTML(v(details.accomplishmentDate, '—'))}</p></div><div class="mt-3"><h5>Uploaded Files</h5>${attachmentsHTML}</div>`;
    document.getElementById('editDetailsBtn').style.display = editMode ? 'none' : 'inline-block';
    document.getElementById('saveDetailsBtn').style.display = editMode ? 'inline-block' : 'none';
    document.querySelectorAll('.author-view-btn').forEach(btn => { btn.addEventListener('click', (e) => { const idx = parseInt(btn.getAttribute('data-author-index')); openAuthorModal(idx); }); });
}
function showDetailsModal(row) {
    const tr = row && row.tagName === 'TR' ? row : row.closest && row.closest('tr');
    if (!tr) return;
    const tds = tr.querySelectorAll('td');
    const cell = (i) => (tds[i] ? tds[i].textContent.trim() : '');
    const details = { studentName: cell(2), studentNumber: cell(1), email: '', homeAddress: '', campus: '', department: '', college: '', program: cell(4), documentTitle: '', authorName: '', accomplishmentDate: cell(5) };
    currentDetails = details; renderDetails(details, false);
    var detailsModal = new bootstrap.Modal(document.getElementById('detailsModal')); detailsModal.show();
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-view').forEach(function(btn) { btn.addEventListener('click', function(e) { e.preventDefault(); showDetailsModal(btn.closest('tr')); }); });
    document.getElementById('editDetailsBtn').addEventListener('click', function() { renderDetails(currentDetails, true); });
    document.getElementById('saveDetailsBtn').addEventListener('click', function() {
        var updatedDetails = { request_id: currentDetails.request_id, student_name: document.getElementById('edit_name')?.value || '', student_id: document.getElementById('edit_number')?.value || '', email: document.getElementById('edit_email')?.value || '', program: document.getElementById('edit_program')?.value || '', };
        fetch('../edit_ticket.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(updatedDetails) })
        .then(res => res.json())
        .then(data => { if (data.success) { renderDetails(updatedDetails, false); alert('Details updated successfully!'); } else { alert('Update failed: ' + (data.error || 'Unknown error')); } });
    });
});
function renderAuthorModal(author, edit = false) {
    const body = document.getElementById('authorInfoBody');
    const v = (x) => (x ?? '');
    if (!body) return;
    if (!edit) { body.innerHTML = `<div class="mb-3"><strong>Name:</strong> ${author.name || '—'}</div><div class="mb-2"><strong>Student Number:</strong> ${author.studentNumber || ''}</div><div class="mb-2"><strong>Email Address:</strong> ${author.email || ''}</div><div class="mb-2"><strong>Home Address:</strong> ${author.address || ''}</div><div class="mb-2"><strong>Phone Number:</strong> ${author.phone || ''}</div><div class="mb-2"><strong>Campus:</strong> ${author.campus || ''}</div><div class="mb-2"><strong>Department:</strong> ${author.department || ''}</div><div class="mb-2"><strong>College:</strong> ${author.college || ''}</div><div class="mb-2"><strong>Program:</strong> ${author.program || ''}</div>`; }
    else { body.innerHTML = `<div class="mb-2"><label class="form-label">Name</label><input class="form-control" id="editAuthorNameInput" value="${v(author.name)}"></div><div class="mb-2"><label class="form-label">Student Number</label><input class="form-control" id="editAuthorStudNoInput" value="${v(author.studentNumber)}"></div><div class="mb-2"><label class="form-label">Email Address</label><input type="email" class="form-control" id="editAuthorEmailInput" value="${v(author.email)}"></div><div class="mb-2"><label class="form-label">Home Address</label><input class="form-control" id="editAuthorAddressInput" value="${v(author.address)}"></div><div class="mb-2"><label class="form-label">Phone Number</label><input class="form-control" id="editAuthorPhoneInput" value="${v(author.phone)}"></div><div class="mb-2"><label class="form-label">Campus</label><input class="form-control" id="editAuthorCampusInput" value="${v(author.campus)}"></div><div class="mb-2"><label class="form-label">Department</label><input class="form-control" id="editAuthorDeptInput" value="${v(author.department)}"></div><div class="mb-2"><label class="form-label">College</label><input class="form-control" id="editAuthorCollegeInput" value="${v(author.college)}"></div><div class="mb-2"><label class="form-label">Program</label><input class="form-control" id="editAuthorProgramInput" value="${v(author.program)}"></div>`; }
    const editBtn = document.getElementById('authorEditBtn'); const saveBtn = document.getElementById('authorSaveBtn'); if (editBtn && saveBtn) { editBtn.style.display = edit ? 'none' : 'inline-block'; saveBtn.style.display = edit ? 'inline-block' : 'none'; }
}
function openAuthorModal(index) { currentAuthorIndex = index; const author = (currentDetails.additionalAuthors || [])[index] || {}; renderAuthorModal(author, false); const modal = new bootstrap.Modal(document.getElementById('authorInfoModal')); modal.show(); }
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('authorEditBtn'); const saveBtn = document.getElementById('authorSaveBtn');
    if (editBtn) { editBtn.addEventListener('click', function() { const author = (currentDetails.additionalAuthors || [])[currentAuthorIndex] || {}; renderAuthorModal(author, true); }); }
    if (saveBtn) { saveBtn.addEventListener('click', function() { const idx = currentAuthorIndex; if (!Array.isArray(currentDetails.additionalAuthors) || idx == null) return; const updated = { name: document.getElementById('editAuthorNameInput')?.value || '', studentNumber: document.getElementById('editAuthorStudNoInput')?.value || '', email: document.getElementById('editAuthorEmailInput')?.value || '', address: document.getElementById('editAuthorAddressInput')?.value || '', phone: document.getElementById('editAuthorPhoneInput')?.value || '', campus: document.getElementById('editAuthorCampusInput')?.value || '', department: document.getElementById('editAuthorDeptInput')?.value || '', college: document.getElementById('editAuthorCollegeInput')?.value || '', program: document.getElementById('editAuthorProgramInput')?.value || '' }; currentDetails.additionalAuthors[idx] = updated; renderDetails(currentDetails, false); renderAuthorModal(updated, false); }); }
});
function selectRemark(remark) { document.getElementById('remarksDropdown').textContent = remark; }
function selectRemarkActive(remark) { const dd = document.getElementById('remarksDropdownActive'); if (dd) dd.textContent = remark; }
document.addEventListener('DOMContentLoaded', function() { document.querySelectorAll('#pending .btn-incomplete').forEach(function(btn) { btn.addEventListener('click', function(e) { e.preventDefault(); var incompleteModal = new bootstrap.Modal(document.getElementById('incompleteModal')); incompleteModal.show(); }); }); document.getElementById('confirmIncompleteBtn').addEventListener('click', function() { const remark = document.getElementById('remarksDropdown').textContent; const textarea = document.getElementById('incompleteTextarea').value; bootstrap.Modal.getInstance(document.getElementById('incompleteModal')).hide(); }); });
document.addEventListener('DOMContentLoaded', function() {
    const approveCommentModalEl = document.getElementById('approveCommentModal');
    const approveCommentModal = approveCommentModalEl ? new bootstrap.Modal(approveCommentModalEl) : null;
    const successModalEl = document.getElementById('successModal');
    const successModal = successModalEl ? new bootstrap.Modal(successModalEl) : null;
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const hiddenInput = btn.closest('form')?.querySelector('input[name="approve_ticket_id"]');
            const requestId = hiddenInput ? hiddenInput.value : '';
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveCommentText').value = '';
            approveCommentModal && approveCommentModal.show();
        });
    });
    document.getElementById('approveCommentConfirmBtn')?.addEventListener('click', async () => {
        const requestId = document.getElementById('approveRequestId').value;
        const comment = document.getElementById('approveCommentText').value.trim();
        if (!requestId) return;
        try {
            const res = await fetch('../approve_request.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': '<?php echo htmlspecialchars(csrf_token()); ?>' }, body: JSON.stringify({ request_id: requestId, comment }) });
            const data = await res.json();
            if (data.success) {
                approveCommentModal && approveCommentModal.hide();
                const row = [...document.querySelectorAll('tr')].find(r => r.firstElementChild && r.firstElementChild.textContent.trim() === requestId);
                if (row) { const statusCell = row.querySelector('td:nth-child(7)'); if (statusCell) statusCell.textContent = 'Approved'; }
                successModal && successModal.show();
            } else { alert(data.error || 'Approval failed'); }
        } catch (err) { alert('Network error'); }
    });
});
document.addEventListener('click', function(e) { const btn = e.target.closest('.btn-view-file'); if (!btn) return; const url = btn.getAttribute('data-file-url'); if (url) window.open(url, '_blank', 'noopener'); });
document.addEventListener('click', function(e) { const trigger = e.target.closest('.btn-view-certificate'); if (!trigger) return; e.preventDefault(); const url = trigger.getAttribute('data-cert-url') || ''; const dl = document.getElementById('downloadCertificateBtn'); if (dl) { if (url) { dl.href = url; dl.setAttribute('download', 'certificate.pdf'); } else { dl.href = '#'; dl.removeAttribute('download'); } } new bootstrap.Modal(document.getElementById('certificateModal')).show(); });
document.addEventListener('click', function(e) { const trigger = e.target.closest('.btn-incomplete-active'); if (!trigger) return; e.preventDefault(); new bootstrap.Modal(document.getElementById('incompleteActiveModal')).show(); });
document.addEventListener('DOMContentLoaded', function() { const btn = document.getElementById('confirmIncompleteActiveBtn'); if (!btn) return; btn.addEventListener('click', function() { const remark = document.getElementById('remarksDropdownActive').textContent; const textarea = document.getElementById('incompleteTextareaActive').value; bootstrap.Modal.getInstance(document.getElementById('incompleteActiveModal')).hide(); }); });
document.addEventListener('click', function(e) { const trigger = e.target.closest('.btn-comments'); if (!trigger) return; e.preventDefault(); let remarkText = ''; const tr = trigger.closest('tr'); if (tr) { const tds = Array.from(tr.querySelectorAll('td')); if (tr.closest('#pending')) { remarkText = (tds[6]?.textContent || '').trim(); } else if (tr.closest('#approved')) { remarkText = 'No remarks available.'; } else if (tr.closest('#completed')) { remarkText = (tds[7]?.textContent || '').trim(); } } if (!remarkText) remarkText = 'No remarks available.'; const ta = document.getElementById('comment_text'); if (ta) ta.value = remarkText; new bootstrap.Modal(document.getElementById('commentModal')).show(); });
</script>

<div class="modal fade" id="authorInfoModal" tabindex="-1" aria-labelledby="authorInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content details-modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between pb-2 border-0">
                <h5 class="modal-title fw-bold mb-0" id="authorInfoModalLabel">Author’s Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="authorInfoBody"></div>
            <div class="modal-footer border-0 pt-3">
                <button type="button" class="btn btn-edit-modal" id="authorEditBtn">EDIT</button>
                <button type="button" class="btn btn-save-modal" id="authorSaveBtn" style="display:none;">SAVE</button>
            </div>  
        </div>
    </div>
</div>

<div class="modal fade" id="approveCommentModal" tabindex="-1" aria-labelledby="approveCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveCommentModalLabel">Approve Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="approveRequestId" />
                <label for="approveCommentText" class="form-label">Comment (optional)</label>
                <textarea class="form-control" id="approveCommentText" rows="5" placeholder="Enter approval comment..." style="resize:none;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="approveCommentConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-success fw-bold mb-0">The request has been approved.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="comment_text" class="form-label">Comments:</label>
                <textarea readonly class="form-control" id="comment_text" rows="5" placeholder="Please reupload your documents" style="resize:none;"></textarea>
            </div>
        </div>
    </div>
</div>
</body>
</html>
