<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="css/student-profile.css">
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <!-- Navbar (uniform across project) -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
  <img src="Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="after-landing.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="after-about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link active" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
      </div>
    </div>
  </nav>


  
  <!-- Main content -->
  <main class="container py-4">
    &nbsp;
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="d-flex align-items-center">
        <h1 class="fw-bold mb-0" style="font-size:2.5rem;">My Profile</h1>
        <button id="editProfileBtn" type="button" class="btn btn-primary btn-sm ms-3" style="background-color:#3b36ff;">
          Edit Profile
        </button>           
      </div>
      <button id="signOutBtn" type="button" class="btn btn-danger btn-sm px-4">
        Sign Out
      </button>
    </div>
    <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(Student)</p>
    <div class="bg-white rounded-3 shadow-sm p-4 mx-auto" style="max-width: 1100px;">
      <form id="profileForm">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" value="" disabled>
          </div>
          <div class="col-md-4">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" value="" disabled>
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Initial</label>
            <input type="text" class="form-control" id="middleInitial" value="" disabled>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Home Address</label>
          <input type="text" class="form-control" id="homeAddress" value="" disabled>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Student ID/Number</label>
            <input type="text" class="form-control" id="studentId" value="" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Mobile Number</label>
            <input type="text" class="form-control" id="mobileNumber" value="" disabled>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-3">
            <label class="form-label">Campus</label>
            <input type="text" class="form-control" id="campus" value="" disabled>
          </div>
          <div class="col-md-3">
            <label class="form-label">College</label>
            <input type="text" class="form-control" id="college" value="" disabled>
          </div>
          <div class="col-md-3">
            <label class="form-label">Department</label>
            <input type="text" class="form-control" id="department" value="" disabled>
          </div>
          <div class="col-md-3">
            <label class="form-label">Program</label>
            <input type="text" class="form-control" id="program" value="" disabled>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Webmail <span class="text-danger">*</span></label>
          <input type="email" class="form-control" id="webmail" value="" disabled>
        </div>
        <div class="mt-4">
          <button id="saveProfileBtn" type="button" class="btn btn-primary btn-sm" style="background-color:#3b36ff; display:none;">
            Save Changes
          </button>
          <button id="cancelEditBtn" type="button" class="btn btn-secondary btn-sm ms-2" style="display:none;">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </main>
  
  <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
document.getElementById('signOutBtn').addEventListener('click', function() {
  window.location.href = 'index.php';
});
document.addEventListener('DOMContentLoaded', function() {
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const form = document.getElementById('profileForm');
  const inputs = form.querySelectorAll('input:not([id="webmail"])');

  editBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = false);
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    editBtn.disabled = true; // Disable the button and keep it visible
  });

  cancelBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    // Optionally reset fields to original values here
  });

  saveBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    // Optionally add save logic here
  });
});
</script>
</body>
</html>
