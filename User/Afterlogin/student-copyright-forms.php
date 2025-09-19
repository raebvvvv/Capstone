<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How do I apply for IP? | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/forms.css'); ?>">
</head>
<body>
  <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
  <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="after-landing.php">Home</a></li>
          <li class="nav-item"><a class="nav-link " href="after-about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="student-profile.php">My Profile</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      <!-- Terms & Conditions -->
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
        <h1 class="fw-bold">Submission Form <small class="text-danger fw-normal" style="font-size:.55em;">(Student)</small></h1>
        <p class="text-muted small">Please fill out the form below to submit your intellectual property for evaluation and processing. All fields marked with an asterisk (*) are required.</p>
        <div class="card shadow-sm">
          <div class="card-body">
            <!-- Student Information  -->
            <form id="submissionForm" class="mt-2" method="post" action="submit-form.php" enctype="multipart/form-data">
              <!-- NEW hidden acceptance flag -->
              <input type="hidden" name="accepted_terms" id="accepted_terms" value="">
              <h2 class="section-heading mb-3">Student Information</h2>
              <fieldset>
                <legend>Personal Details</legend>
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label required">First name</label>
                    <input type="text" name="first_name" class="form-control" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Middle name</label>
                    <input type="text" name="middle_name" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Last name</label>
                    <input type="text" name="last_name" class="form-control" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Student Number</label>
                    <input type="text" name="student_number" class="form-control" placeholder="20XX-XXXXX-MN-0" required>
                    <small class="text-muted">Format: YYYY-#####-AA-#</small>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Contact</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Home Address</label>
                    <input type="text" name="home_address" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Mobile Number</label>
                    <input type="tel" name="mobile_number" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Webmail Address</label>
                    <input type="email" name="webmail" class="form-control" required>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Academic Affiliation</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Campus</label>
                    <select name="campus" id="campus" class="form-select" required>
                      <option value="PUP Main (Sta. Mesa, Manila)" selected>PUP Main (Sta. Mesa, Manila)</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Academic Level</label>
                    <select name="academicLevel" id="academicLevel" class="form-select" required>
                      <option value="" disabled selected>Choose...</option>
                      <!-- Will be populated from JS -->
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">College</label>
                    <select name="college" id="college" class="form-select" required>
                      <option value="" disabled selected>Choose...</option>
                      <!-- Will be populated from JS -->
                    </select>
                  </div>
                </div>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label required">Program</label>
                    <select name="program" id="program" class="form-select" required>
                      <option value="" disabled selected>Choose...</option>
                      <!-- Will be populated from JS -->
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Work Classification</label>
                    <select name="workClassification" id="workClassification" class="form-select" required>
                      <option value="" disabled selected>Choose...</option>
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
                    <label class="form-label required">Author(s)</label>
                    <div id="authorsList" class="mb-2 small text-muted">No authors added yet.</div>
                    <div id="authorsHidden"></div>
                    <button type="button" id="addAuthorBtn" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authorModal">+ Add Author</button>
                    <small class="text-muted d-block mt-1">Use the button to add each author.</small>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Adviser</label>
                    <input type="text" name="adviser" class="form-control" placeholder="Add an Adviser" required>
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="checkbox" name="adviser_coauthor" id="adviserCoauthor">
                      <label class="form-check-label" for="adviserCoauthor">
                        Adviser is a Co-author
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Date Accomplished</label>
                    <input type="date" name="date_accomplished" class="form-control" required>
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
                    <label class="form-label required">Full Manuscript</label>
                    <input type="file" name="full_manuscript" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Notarized Co-Authorship</label>
                    <input type="file" name="notarized_coauthorship" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Approval Sheet (Thesis)</label>
                    <input type="file" name="approval_sheet" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Record of Copyright Application</label>
                    <input type="file" name="record_copyright" class="form-control" accept="application/pdf" required>
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
                <label class="form-label required">First name</label>
                <input type="text" name="first_name" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label required">Last name</label>
                <input type="text" name="last_name" class="form-control" required>
              </div>
              <div class="col-md-2">
                <label class="form-label required">Middle Initial</label>
                <input type="text" name="middle_initial" maxlength="1" class="form-control text-uppercase">
              </div>
              <div class="col-md-4">
                <label class="form-label required">PUP ID Number</label>
                <input type="text" name="student_id" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label required">Mobile Number</label>
                <input type="text" name="mobile" class="form-control">
              </div>
              <div class="col-md-8">
                <label class="form-label required">Home Address</label>
                <input type="text" name="home_address" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label required">Webmail Address</label>
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
 <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none">Privacy Statement</a>
    </div>
  </footer>
 <!-- Load external JS files compliant with Content Security Policy -->
 <script src="<?php echo asset_url('javascript/forms/terms-gating.js'); ?>"></script>
 <script src="<?php echo asset_url('javascript/forms/author-modal.js'); ?>"></script>
 <script src="<?php echo asset_url('javascript/forms/academic-dropdowns.js'); ?>"></script>
 <script src="<?php echo asset_url('javascript/forms/adviser-coauthor.js'); ?>"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>