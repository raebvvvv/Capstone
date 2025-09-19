<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PUP e-IPMO | About Us</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/about.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
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
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <section class="container py-5">
    <div class="row g-5 align-items-start">
      <div class="d-flex flex-row justify-content-between align-items-start w-100 flex-wrap main-intro-row">
        <div class="d-flex flex-row justify-content-between align-items-start w-100 flex-wrap">
          <div class="flex-grow-1" style="max-width: 60%;">
            <h1 class="fw-bold mb-2">Intellectual Property Management Office</h1>
            <p class="fw-bold mb-1" style="font-size:1.1rem;">The Intellectual Property Management Office (IPMO)</p>
            <p>
              of the Polytechnic University of the Philippines provides assistance to researchers to secure attractive returns in the long term from the bulk of valuable research outcomes and intellectual property. It also assists researchers in identifying partners within the academe, other businesses and investors to promote translation of research innovations into new products through advice and support for early-stage research and development.
            </p>
            <p>
              The IPMO provides a portfolio of new technologies created through University research for licensing or collaborative development.
            </p>
          </div>
          <div class="ms-5">  
            <h2 class="fw-bold">Office Hours</h2>
            <div class="mb-2">
              <div>Monday - Friday<br>8:00 AM - 8:00 PM</div>
              <div class="text-danger fw-bold mt-1">NO SATURDAY SERVICES.</div>
            </div>
            <div class="mt-3">
              <div><span class="fw-bold">Location:</span></div>
              <div>PUP Main Building<br>S407, South Wing, 4th Floor<br>Anonas St. Sta. Mesa, Manila</div>
            </div>
            <h4 class="fw-bold mt-5">Contact Information</h4>
            <div class="mb-2">
              <span class="me-3"><img src="<?php echo asset_url('Photos/Icons/Email-icon.png'); ?>" alt="email" style="width:20px;height:20px;vertical-align:middle;margin-right:4px;"> ipmo@pup.edu.ph</span><br>
              <span><img src="<?php echo asset_url('Photos/Icons/Landline-icon.png'); ?>" alt="phone" style="width:20px;height:20px;vertical-align:middle;margin-right:4px;"> (+632) 5335-1787</span>
            </div>
          </div>
        </div>
      </div>
  </div>

      <!-- Additional Sections with Padding -->
      <section class="mb-5 pb-3 border-start border-4 border-danger ps-3">
        <h2 class="fw-bold mb-3">IPMO Office and Center</h2>
        <h5 class="fw-bold mb-2">Innovations and Technology Support Office</h5>
        <p>The Innovations and Technology Support Office (ITSO) was created to reaffirm PUP's commitment of responding to the needs of time by propagating and protecting intellectual property rights. Intellectual properties are creations of human minds and just like any other form of property, intellectual property rights are duly recognized.</p>
        <h5 class="fw-bold mt-4 mb-2">Center for Technology Transfer and Enterprise Development</h5>
        <p>The Center for Technology Transfer and Enterprise Development (CTTED), is a dynamic center that facilitates and supports the development of start-up technologies based on University research. The Center also addresses the potential roles, considerations, and opportunities for university-affiliated inventors such as students, faculty members and administrative staff.</p>
      </section>

      <section class="mb-5 pb-3 border-start border-4 border-danger ps-3">
        <h2 class="fw-bold mb-3">IPMO Functions</h2>
        <ol class="mb-0">
          <li>Provides advice to researchers on Intellectual Property rights and protection;</li>
          <li>Provides assistance in the registration of intellectual property rights of University stakeholders;</li>
          <li>Conducts seminars and trainings on Intellectual Property and commercialization of research products;</li>
          <li>Provides assistance to researchers in securing attractive returns in the long term from the commercialization of valuable research outcomes and intellectual property;</li>
          <li>Assists researchers in identifying possible partners within the academe, other</li>
          <li>businesses and investors to promote translation of research innovations into new products through advice and support for early-stage research and development;</li>
          <li>Assists in facilitating material transfer agreements;</li>
          <li>Assesses University inventions/innovations with potential for commercialization;</li>
          <li>Provides a portfolio of new technologies created through University research for licensing or collaborative development;</li>
          <li>Markets inventions, innovations and discoveries to the industry;</li>
          <li>Assists in raising revenue for the University.</li>
        </ol>
      </section>

      <section class="mb-5 pb-3 border-start border-4 border-danger ps-3">
        <h2 class="fw-bold mb-3">IPMO History</h2>
        <p>The University Board of Regents approved the PUP Intellectual Property Policy on April 19, 2007. The monitoring and implementation of the said IP Policy was delegated to the University Legal Office. In October 2012, President Emanuel C. De Guzman issued a Special Order creating the Intellectual Property and Commercialization Office with Prof. Elmer G. De Jose as its first chief, in concurrent capacity as Chief of the Graduate School Research, Development and Production Center.</p>
      </section>
  </section>
  </main>

  <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>