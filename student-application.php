<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="Photos/pup-logo.png">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/student-application.css">
</head>
<body>
  <!-- Navbar (uniform across project) -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
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
          <li class="nav-item"><a class="nav-link nav-bold" href="after-landing.php">Home</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="after-about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link nav-bold active-page" aria-current="page" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <main class="container py-4">
    <h1 class="fw-bold mb-2 mt-4" style="font-size:2.5rem;">My Application</h1>
    <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(Student)</p>
    <div class="d-flex flex-wrap mb-4">
      <button class="category-btn active">Patent/Industrial Design/Utility Model/Trademark</button>
      <button class="category-btn">Copyright</button>
    </div>
    <ul class="nav nav-tabs mb-3" id="applicationTabs">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#pending">Pending</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#approved">Approved</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#completed">Completed</a></li>
    </ul>
    <div class="tab-content mt-3">
      <!-- Pending Tab -->
      <div class="tab-pane fade show active" id="pending">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  You have not applied for anything yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Approved Tab -->
      <div class="tab-pane fade" id="approved">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  You have not applied for anything yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Completed Tab -->
      <div class="tab-pane fade" id="completed">
        <div class="table-responsive">
          <table class="table align-middle bg-white mb-0">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Student Number / Employee ID</th>
                <th>Title of Work</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  You have not applied for anything yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
  
  <!-- Footer (consistent across all pages) -->
  <footer class="text-center mt-5">
    <p>
      For any questions or concerns, feel free to contact us via:<br>
      📧 ipmo@pup.edu.ph &nbsp; 📞 (+632) 5335-1787
    </p>
    <small>
      © 2025 Polytechnic University of the Philippines | 
      <a href="https://www.pup.edu.ph/terms" class="footer-link-blue" target="_blank">Terms of Service</a> | 
      <a href="https://www.pup.edu.ph/privacy" class="footer-link-blue" target="_blank">Privacy Statement</a>
    </small>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categoryBtns = document.querySelectorAll('.category-btn');
      const categoryContents = document.querySelectorAll('.category-content');

      categoryBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
          // Remove active from all buttons
          categoryBtns.forEach(b => b.classList.remove('active'));
          // Add active to clicked button
          btn.classList.add('active');
          // Hide all category contents
          categoryContents.forEach(c => c.style.display = 'none');
          // Show the selected category content
          categoryContents[idx].style.display = 'block';
        });
      });

      // Show only the first category by default
      categoryContents.forEach((c, i) => c.style.display = i === 0 ? 'block' : 'none');
    });
  </script>
</body>
</html>
