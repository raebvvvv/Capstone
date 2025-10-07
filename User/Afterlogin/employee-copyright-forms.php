<?php require __DIR__ . '/../../config.php'; ?>
<?php require __DIR__ . '/../../auth_check.php'; ?>
<?php $isEmployee = ((($_SESSION['role'] ?? '') === 'employee')); ?>
<?php
// Fetch employee profile data
$stmt = $pdo->prepare("
    SELECT ep.*, u.email
    FROM employee_profiles ep 
    JOIN users u ON ep.user_id = u.user_id 
    WHERE ep.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Backfill academic_level and program from the user's most recent submission if not present in profile
if ($profile) {
  if (empty($profile['academic_level']) || empty($profile['program'])) {
    $s = $pdo->prepare("SELECT academic_level, program FROM submissions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $s->execute([$_SESSION['user_id']]);
    $last = $s->fetch(PDO::FETCH_ASSOC);
    if ($last) {
      if (empty($profile['academic_level']) && !empty($last['academic_level'])) {
        $profile['academic_level'] = $last['academic_level'];
      }
      if (empty($profile['program']) && !empty($last['program'])) {
        $profile['program'] = $last['program'];
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How do I apply for IP? | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/forms.css'); ?>">
</head>
<body class="forms-page">
  <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
  <a class="navbar-brand d-flex align-items-center" href="../../index.php">
  <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-application.php' : 'student-application.php'; ?>">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-profile.php' : 'student-profile.php'; ?>">My Profile</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
          <!-- Terms & Conditions (match student structure) -->
          <div id="termsGate" class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-2">
              <strong>Terms &amp; Conditions</strong>
            </div>
            <div class="card-body small">
              <p class="mb-2">By filling in this form, you agree to the following:</p>
              <ol class="mb-3 ps-3">
                <li class="mb-2">
                  <strong>Purpose</strong><br>
                  This form is for disclosing inventions, creative works, or other intellectual property created by students, faculty, staff, or collaborators of the Polytechnic University of the Philippines.
                </li>
                <li class="mb-2">
                  <strong>Accuracy</strong><br>
                  All information provided is true and complete to the best of your knowledge.
                </li>
                <li class="mb-2">
                  <strong>Confidentiality</strong><br>
                  Submitted information may be reviewed internally for IP evaluation and protection.
                </li>
                <li class="mb-2">
                  <strong>Compliance</strong><br>
                  You agree to comply with PUP policies, national IP laws, and related regulations.
                </li>
                <li class="mb-2">
                  <strong>Authorization</strong><br>
                  You authorize PUP to process the data for evaluation, filing, and administrative purposes.
                </li>
              </ol>
              <div class="d-flex align-items-center flex-wrap gap-3">
                <div class="form-check m-0">
                  <input class="form-check-input" type="checkbox" id="termsAgree">
                  <label class="form-check-label fw-semibold" for="termsAgree">
                    I accept and agree to the Terms &amp; Conditions.
                  </label>
                </div>
                <button type="button" id="termsNext" class="btn btn-primary btn-sm px-4" disabled>Next</button>
              </div>
            </div>
          </div>

      <!-- Form Section (hidden until acceptance) -->
      <div id="formSection" class="d-none">
        <h1 class="fw-bold">Submission Form <small class="text-danger fw-normal" style="font-size:.55em;">(Employee)</small></h1>
        <p class="text-muted small">Please fill out the form below to submit your intellectual property for evaluation and processing. All fields marked with an asterisk (*) are required.</p>
        <div class="card shadow-sm">
          <div class="card-body">
            <!-- Employees Information  -->
            <form id="submissionForm" class="mt-2" method="post" action="submit-form.php" enctype="multipart/form-data">
              <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
              <input type="hidden" name="accepted_terms" id="accepted_terms" value="">

              <h2 class="section-heading mb-3">Employee Information</h2>
              <fieldset>
                <legend>Personal Details</legend>
                <div class="row g-3 mt-2">
                  <div class="col-md-3">
                    <label class="form-label required">First name</label>
                    <input type="text" name="first_name" class="form-control" required
                      value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Middle name</label>
                    <input type="text" name="middle_name" class="form-control"
                      value="<?php echo htmlspecialchars($profile['middle_name'] ?? ''); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Last name</label>
                    <input type="text" name="last_name" class="form-control" required
                      value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" required
                      value="<?php echo htmlspecialchars($profile['employee_number'] ?? ''); ?>" readonly>
                    <small class="text-muted">Format: XXXXX</small>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Contact</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Home Address</label>
          <input type="text" name="home_address" class="form-control" required
            value="<?php echo htmlspecialchars($profile['home_address'] ?? ''); ?>" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Mobile Number</label>
          <input type="tel" name="mobile_number" class="form-control" required
            value="<?php echo htmlspecialchars($profile['mobile_number'] ?? ''); ?>" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">PUP Webmail</label>
          <input type="email" name="webmail" class="form-control" required
            value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" readonly>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Academic Affiliation</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Campus</label>
                    <select name="campus" id="campus" class="form-select" required disabled aria-disabled="true" title="Locked from editing">
                      <!-- populated by JS -->
                    </select>
                    <input type="hidden" name="campus" id="campus_hidden" value="">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Academic Level</label>
                    <select name="academicLevel" id="academicLevel" class="form-select" required disabled aria-disabled="true" title="Locked from editing">
                      <option value="" disabled selected>Choose...</option>
                      <!-- Will be populated from JS -->
                    </select>
                    <input type="hidden" name="academicLevel" id="academicLevel_hidden" value="">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">College</label>
                    <select name="college" id="college" class="form-select" required disabled aria-disabled="true" title="Locked from editing">
                      <!-- Will be populated from JS -->
                    </select>
                    <input type="hidden" name="college" id="college_hidden" value="">
                  </div>
                </div>
                <div class="row g-3 mt-2">
                  <div class="col-md-4">
                    <label class="form-label required">Department</label>
                    <select name="department" id="department" class="form-select" required disabled aria-disabled="true" title="Locked from editing">
                      <!-- Will be populated from JS based on College -->
                    </select>
                    <input type="hidden" name="department" id="department_hidden" value="">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Program</label>
                    <select name="program" id="program" class="form-select" required disabled aria-disabled="true" title="Locked from editing">
                      <!-- Will be populated from JS -->
                    </select>
                    <input type="hidden" name="program" id="program_hidden" value="">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Work Classification</label>
                    <select name="workClassification" id="workClassification" class="form-select" required>
                      <!-- Will be populated from JS -->
                    </select>
                    <small class="text-muted">Type O (Original), Type A (Adaptation), Type B (Based on Public Domain)</small>
                  </div>
                </div>
              </fieldset>
              <!-- Document Information -->
              <h2 class="section-heading">Document Information</h2>
              <fieldset>
                <legend>Work Metadata</legend>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label required">Title</label>
                    <input type="text" name="title" class="form-control" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Author(s)</label>
                    <div id="authorsList" class="mb-2 small text-muted">No authors added yet.</div>
                    <div id="authorsHidden"></div>
                    <button type="button" id="addAuthorBtn" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authorModal">+ Add Author</button>
                    <small class="text-muted d-block mt-1">Use the button to add each author.</small>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Adviser</label>
                    <input type="text" name="adviser" class="form-control" placeholder="Add an Adviser">
                    <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="adviser_coauthor" id="adviserCoauthor">
                    <label class="form-check-label" for="adviserCoauthor">
                      Adviser is a Co-author
                    </label>
                  </div>
                  <!-- mirror checkbox state for server if needed (kept hidden, doesn't change UI) -->
                  <input type="hidden" name="adviser_Coauthor" id="adviser_Coauthor_hidden" value="">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Date Accomplished</label>
                    <input type="date" name="date_accomplished" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                    <small class="text-muted">Finalization date.</small>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Upload Documents (PDF)</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Journal Publication Format</label>
                    <input type="file" name="journal_publication_format" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Notarized Copyright Application Form</label>
                    <input type="file" name="notarized_copyright" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Receipt of Payment</label>
                    <input type="file" name="receipt_payment" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Presentation</label>
                    <input type="file" name="presentation" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Record of Copyright Application</label>
                    <input type="file" name="record_copyright" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Notarized Co-Authorship (Optional)</label>
                    <input type="file" name="notarized_coauthorship" class="form-control" accept="application/pdf">
                  </div>
                </div>
                <small class="text-muted d-block mt-2">All PDFs must be clear, complete, and properly signed where applicable.</small>
              </fieldset>
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-5">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Author Modal -->
  <div class="modal fade" id="authorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title">Author Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="authorForm">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">First name</label>
                <input type="text" name="first_name" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Last name</label>
                <input type="text" name="last_name" class="form-control">
              </div>
                <div class="row g-3 mt-2">
                  <div class="col-md-12">
                <input type="text" name="middle_initial" maxlength="1" class="form-control text-uppercase">
              </div>
              <div class="col-md-4">
                <label class="form-label required">Employee ID</label>
                <input type="text" name="employee_id" class="form-control">
                  <div class="col-md-4 d-none">
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">College</label>
                <select name="college" class="form-select">
                  <option value="">College</option>
                  <option>CCIS</option>
                  <option>CAF</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Program</label>
                <input type="text" name="program" class="form-control" placeholder="e.g. BSIT">
              </div>
              <div class="col-md-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" placeholder="Dept.">
              </div>
              <div class="col-md-4">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" class="form-control">
              </div>
              <div class="col-md-8">
                <label class="form-label">Home Address</label>
                <input type="text" name="home_address" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label">Webmail Address</label>
                <input type="email" name="webmail" class="form-control" placeholder="email@domain.com">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="saveAuthorBtn" class="btn btn-primary btn-sm">Add Author</button>
        </div>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/forms/terms-accept.js'); ?>" defer></script>
  <script src="<?php echo asset_url('javascript/forms/employee-academic-dropdowns.js'); ?>" defer></script>
  <script src="<?php echo asset_url('javascript/forms/employee-authors.js'); ?>" defer></script>
  <script>
    // Prefill Academic Affiliation fields (except Work Classification) from employee profile
    (function() {
      const prefill = {
        campus: <?php echo json_encode($profile['campus'] ?? ''); ?>,
        academicLevel: <?php echo json_encode($profile['academic_level'] ?? ''); ?>,
        college: <?php echo json_encode($profile['college'] ?? ''); ?>,
        department: <?php echo json_encode($profile['department'] ?? ''); ?>,
        program: <?php echo json_encode($profile['program'] ?? ''); ?>
      };

      function setSelectMatch(select, value) {
        if (!select || !value) return false;
        let match = null;
        for (const opt of Array.from(select.options)) {
          if (opt.value === value || opt.text.trim() === value.trim()) { match = opt.value; break; }
        }
        if (match !== null) {
          select.value = match;
          select.dispatchEvent(new Event('change', { bubbles: true }));
          return true;
        }
        // Fallback: if no matching option exists yet, insert a synthetic one so value is shown and submitted
        const opt = new Option(value, value, true, true);
        select.add(opt);
        select.dispatchEvent(new Event('change', { bubbles: true }));
        return true;
      }

      function setWhenReady(id, value, tries = 25) {
        const el = document.getElementById(id);
        if (!el || !value) return;
        const ready = el.options && el.options.length > 0;
        if (!ready && tries > 0) {
          return setTimeout(() => setWhenReady(id, value, tries - 1), 120);
        }
        setSelectMatch(el, value);
      }

      function mirrorToHidden() {
        const map = [
          ['campus','campus_hidden'],
          ['academicLevel','academicLevel_hidden'],
          ['college','college_hidden'],
          ['department','department_hidden'],
          ['program','program_hidden']
        ];
        for (const [sid, hid] of map) {
          const sel = document.getElementById(sid);
          const hidEl = document.getElementById(hid);
          if (sel && hidEl) hidEl.value = sel.value || '';
        }
      }

      document.addEventListener('DOMContentLoaded', function() {
        // Order: campus -> college -> department, and academicLevel -> program
        setWhenReady('campus', prefill.campus);
        // After campus, populate and set college/department
        const campusEl = document.getElementById('campus');
        campusEl && campusEl.addEventListener('change', function() {
          setTimeout(() => setWhenReady('college', prefill.college), 160);
        }, { once: true });

        const collegeEl = document.getElementById('college');
        collegeEl && collegeEl.addEventListener('change', function() {
          setTimeout(() => setWhenReady('department', prefill.department), 160);
        });

        // Level can be set independently; program may depend on level and college
        setWhenReady('academicLevel', prefill.academicLevel);
        const levelEl = document.getElementById('academicLevel');
        levelEl && levelEl.addEventListener('change', function() {
          setTimeout(() => setWhenReady('program', prefill.program), 200);
        });

        // Final safety pass in case events didn't chain as expected
        setTimeout(() => {
          setWhenReady('college', prefill.college);
          setWhenReady('department', prefill.department);
          setWhenReady('program', prefill.program);
          mirrorToHidden();
        }, 800);

        // Also mirror on form submit to ensure latest values are posted
        const form = document.getElementById('submissionForm');
        form && form.addEventListener('submit', function() { mirrorToHidden(); });
      });
    })();
  </script>
</body>
</html>