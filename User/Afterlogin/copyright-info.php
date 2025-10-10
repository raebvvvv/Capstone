<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Information | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/copyright-info.css'); ?>">
</head>
<body>
  <!-- Navbar -->
 <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
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
          <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-application.php' : 'student-application.php'; ?>">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-profile.php' : 'student-profile.php'; ?>">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
      </div>
    </div>
  </nav>

  <!-- Back Button Below Navbar, scrolls with content, transparent background -->
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { 
      // Requirement: after-login back button should fallback to public index
      render_back_link('index.php'); 
    } ?>
  </div>

  <!-- Main Content -->
  <section class="container py-5">
    <div class="row">
      <div class="col-12">
        <h1 class="fw-bold mb-4">Understanding Copyright</h1>
      </div>
    </div>
    
    <div class="row">
      <!-- Sidebar Table of Contents -->
      <div class="col-lg-3">
        <div class="sidebar-toc">
          <h5>Table of Contents</h5>
          <ul class="toc-list">
            <li><a href="#introduction" class="toc-link">What is Copyright?</a></li>
            <li><a href="#pup-definition" class="toc-link">Copyright at PUP</a></li>
            <li><a href="#pup-services" class="toc-link">Copyright Services at PUP</a></li>
            <li><a href="#requirements" class="toc-link">Copyright Requirements: Originality and Fixation</a></li>
            <li><a href="#ownership" class="toc-link">Who is a Copyright Owner?</a></li>
            <li><a href="#rights" class="toc-link">What Rights Does Copyright Provide?</a></li>
            <li><a href="#duration" class="toc-link">How Long Does Copyright Protection Last?</a></li>
            <li><a href="#fair-use" class="toc-link">When Can I Use Works That Are Not Mine?</a></li>
            <li><a href="#ip-comparison" class="toc-link">Copyright vs. Other Intellectual Property Rights</a></li>
            <li><a href="#attribution" class="toc-link">Educational Resource Attribution</a></li>
          </ul>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col-lg-9">
        
        <!-- Introduction -->
        <div id="introduction" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">What is Copyright?</h3>
            <p class="lead">Copyright is the legal protection extended to the owner of the rights in an original work. "Original work" refers to every production in the literary, scientific and artistic domain.</p>
            <p>Among the literary and artistic works enumerated in the IP Code includes books and other writings, musical works, films, paintings and other works, and computer programs.</p>
            <p>Copyright laws grant authors, artists and other creators automatic protection for their literary and artistic creations, from the moment they create it.</p>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> This information is based on content from the <a href="https://www.ipophil.gov.ph/" target="_blank" class="text-decoration-none">Intellectual Property Office of the Philippines (IPOPhil)</a>. Content has been adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- PUP Definition -->
        <div id="pup-definition" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Copyright at PUP</h3>
            <div class="pup-highlight">
              <h5>PUP Citizens Charter</h5>
              <p class="mb-0">PUP's copyright service facilitates the process of copyright registration of <strong>Undergraduate and Graduate academic works</strong>, including dissertations, feasibility studies, design prototypes, computer programs and software, audiovisual, cinematography works, literary and creative works to ensure the intellectual property of students.</p>
            </div>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> This information is based on the <a href="https://drive.google.com/file/d/0B1BuDAuN0r8SbmFEU1RJUDFYU1E/view?resourcekey=0-ntyYkBAnyIFKdRJWhiKEcw" target="_blank" class="text-decoration-none">PUP Citizens Charter</a>. Content has been adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- PUP Services -->
        <div id="pup-services" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Copyright Services at PUP</h3>
            
            <div class="row">
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Academic Works Protected at PUP:</h5>
                <ul class="feature-list">
                  <li><span class="feature-icon">🎓</span>Undergraduate theses and projects</li>
                  <li><span class="feature-icon">📜</span>Graduate dissertations</li>
                  <li><span class="feature-icon">📊</span>Feasibility studies</li>
                  <li><span class="feature-icon">🔧</span>Design prototypes</li>
                  <li><span class="feature-icon">💻</span>Computer programs and software</li>
                  <li><span class="feature-icon">🎬</span>Audiovisual and cinematographic works</li>
                  <li><span class="feature-icon">✍️</span>Literary and creative works</li>
                </ul>
              </div>
              
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Why Use PUP e-IPMO?</h5>
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">⚡</span>
                      <div>
                        <strong>Streamlined Process</strong>
                        <p class="small text-muted mb-0">Simplified online application system</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🎯</span>
                      <div>
                        <strong>Academic Focus</strong>
                        <p class="small text-muted mb-0">Specialized for student and faculty works</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🛡️</span>
                      <div>
                        <strong>Legal Protection</strong>
                        <p class="small text-muted mb-0">Ensures intellectual property rights</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🤝</span>
                      <div>
                        <strong>University Support</strong>
                        <p class="small text-muted mb-0">Dedicated assistance throughout the process</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">👥</span>
                      <div>
                        <strong>Co-ownership</strong>
                        <p class="small text-muted mb-0">PUP becomes copyright co-owner as per university policy</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Copyright Requirements -->
        <div id="requirements" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Copyright Requirements: Originality and Fixation</h3>
            
            <div class="row">
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Original Works</h5>
                <p>Works are original when they are:</p>
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Independently created by a human author</li>
                  <li><span class="feature-icon">✓</span>Have a minimal degree of creativity</li>
                  <li><span class="feature-icon">✓</span>Created without copying from others</li>
                </ul>
                <div class="highlight-box" style="background: #fff3cd; border-color: #ffeaa7;">
                  <small><strong>Not Creative:</strong> Titles, names, short phrases, slogans, familiar symbols, mere variations of typography, and simple listings are generally not protected.</small>
                </div>
              </div>
              
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Fixed Works</h5>
                <p>A work is fixed when it is captured in a sufficiently permanent medium such that the work can be:</p>
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Perceived for more than a short time</li>
                  <li><span class="feature-icon">✓</span>Reproduced or communicated</li>
                  <li><span class="feature-icon">✓</span>Examples: written down, recorded, saved digitally</li>
                </ul>
                <div class="highlight-box" style="background: #d1ecf1; border-color: #bee5eb;">
                  <small><strong>Remember:</strong> Copyright protects expression, never ideas, procedures, methods, systems, processes, concepts, or principles.</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Copyright Ownership -->
        <div id="ownership" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Who is a Copyright Owner?</h3>
            <p><strong>Everyone is a copyright owner!</strong> Once you create an original work and fix it (like taking a photograph, writing a poem or blog, or recording a new song), you are automatically the author and owner.</p>
            
            <h5 class="fw-bold mb-3 mt-4" style="color: #198754;">Other Ways to Own Copyright:</h5>
            <ul class="feature-list">
              <li><span class="feature-icon">→</span><strong>Works Made for Hire:</strong> Works created by employees within the scope of employment are owned by the employer</li>
              <li><span class="feature-icon">→</span><strong>Contracts:</strong> Copyright ownership can be transferred through assignments</li>
              <li><span class="feature-icon">→</span><strong>Other Transfers:</strong> Wills, bequests, and other legal transfers</li>
            </ul>
          </div>
        </div>

        <!-- Copyright Rights -->
        <div id="rights" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">What Rights Does Copyright Provide?</h3>
            <p>Philippine copyright law provides copyright owners with the following <strong>exclusive rights</strong>:</p>
            
            <div class="row">
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Reproduce the work</li>
                  <li><span class="feature-icon">✓</span>Prepare derivative works</li>
                  <li><span class="feature-icon">✓</span>Distribute copies to the public</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Perform the work publicly</li>
                  <li><span class="feature-icon">✓</span>Display the work publicly</li>
                  <li><span class="feature-icon">✓</span>Digital audio transmission (for sound recordings)</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box" style="background: #d1ecf1; border-color: #bee5eb;">
              <strong>Important:</strong> Copyright also provides the owner the right to authorize others to exercise these exclusive rights, subject to certain statutory limitations.
            </div>
          </div>
        </div>

        <!-- Duration -->
        <div id="duration" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">How Long Does Copyright Protection Last?</h3>
            <p>The length of copyright protection depends on when a work was created:</p>
            
            <div class="table-responsive">
              <table class="table info-table">
                <thead>
                  <tr>
                    <th>Type of Work</th>
                    <th>Copyright Duration</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Works created on or after January 1, 1998 (under current Philippine IP Code)</td>
                    <td>Life of the author plus 50 years</td>
                  </tr>
                  <tr>
                    <td>Joint works</td>
                    <td>50 years after the last surviving author's death</td>
                  </tr>
                  <tr>
                    <td>Works made for hire, anonymous, or pseudonymous works</td>
                    <td>50 years from publication or creation</td>
                  </tr>
                  <tr>
                    <td>Works created before 1998</td>
                    <td>Different timeframe (varies)</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Fair Use -->
        <div id="fair-use" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">When Can I Use Works That Are Not Mine?</h3>
            
            <h5 class="fw-bold mb-3" style="color: #198754;">Legal Ways to Use Others' Works:</h5>
            <div class="row">
              <div class="col-md-6">
                <h6 class="fw-bold">With Permission:</h6>
                <ul class="feature-list">
                  <li><span class="feature-icon">→</span>Buying or licensing works</li>
                  <li><span class="feature-icon">→</span>Getting permission from the owner</li>
                  <li><span class="feature-icon">→</span>Using contractual agreements</li>
                </ul>
              </div>
              
              <div class="col-md-6">
                <h6 class="fw-bold">Copyright Exceptions:</h6>
                <ul class="feature-list">
                  <li><span class="feature-icon">→</span>Fair use</li>
                  <li><span class="feature-icon">→</span>First sale doctrine</li>
                  <li><span class="feature-icon">→</span>Library and archive reproductions</li>
                  <li><span class="feature-icon">→</span>Certain performances and displays</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box" style="background: #d4edda; border-color: #c3e6cb;">
              <h6 class="fw-bold">Public Domain Works</h6>
              <p class="mb-0">You can freely use works in the public domain - these are works that are never protected by copyright (like facts or discoveries) or works whose copyright protection has expired. For Philippine works, this includes works published before certain dates under previous copyright laws.</p>
            </div>
          </div>
        </div>

              

        <!-- Other IP Types -->
        <div id="ip-comparison" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Copyright vs. Other Intellectual Property Rights</h3>
            <p>Intellectual property (IP) protects creations of the mind. Copyright is one type of IP, but there are others:</p>
            
            <div class="table-responsive">
              <table class="table info-table">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>What It Protects</th>
                    <th>Examples</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="copyright-highlight">
                    <td><strong>Copyright</strong></td>
                    <td>Original works of authorship</td>
                    <td>Books, music, art, software, films</td>
                  </tr>
                  <tr>
                    <td><strong>Patents</strong></td>
                    <td>Inventions and processes</td>
                    <td>Utility patents, design patents, plant patents</td>
                  </tr>
                  <tr>
                    <td><strong>Trademarks</strong></td>
                    <td>Source identifiers for goods/services</td>
                    <td>Brand names, logos, slogans</td>
                  </tr>
                  <tr>
                    <td><strong>Trade Secrets</strong></td>
                    <td>Valuable commercial information kept secret</td>
                    <td>Formulas, processes, customer lists</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        

        <!-- Footer Attribution -->
        <div id="attribution" class="info-card content-section">
          <div class="card-body text-center">
            <h6 class="fw-bold mb-3">Educational Resource Attribution</h6>
            <p class="small text-muted mb-3"><strong>Important Academic Disclaimer:</strong> This educational content is compiled from multiple authoritative sources and has been adapted for educational purposes at the Polytechnic University of the Philippines e-IPMO system. While we strive for accuracy, users should consult official sources for the most current information. This compilation constitutes transformative educational use under applicable fair use provisions.</p>
            
            <div class="row text-start">
              <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2" style="color: #900c0c;">Philippine Authorities:</h6>
                <p class="small text-muted mb-1">
                  <strong>IPOPhil:</strong><br>
                  <a href="https://www.ipophil.gov.ph/" target="_blank" class="text-decoration-none">Intellectual Property Office of the Philippines</a>
                </p>
                <p class="small text-muted mb-0">
                  <strong>PUP Citizens Charter:</strong><br>
                  <a href="https://drive.google.com/file/d/0B1BuDAuN0r8SbmFEU1RJUDFYU1E/view?resourcekey=0-ntyYkBAnyIFKdRJWhiKEcw" target="_blank" class="text-decoration-none">Official PUP Document</a>
                </p>
              </div>
              
              <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2" style="color: #900c0c;">International Sources:</h6>
                <p class="small text-muted mb-1">
                  <strong>U.S. Copyright Office:</strong><br>
                  <a href="https://www.copyright.gov/what-is-copyright/" target="_blank" class="text-decoration-none">What is Copyright?</a>
                </p>
                <p class="small text-muted mb-0">
                  <strong>WIPO:</strong><br>
                  <a href="https://www.wipo.int/copyright/en/" target="_blank" class="text-decoration-none">World Intellectual Property Organization</a>
                </p>
              </div>
              
              <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2" style="color: #900c0c;">Additional Resources:</h6>
                <p class="small text-muted mb-1">
                  For Philippine copyright registration:<br>
                  <a href="https://www.ipophil.gov.ph/" target="_blank" class="text-decoration-none">ipophil.gov.ph</a>
                </p>
                <p class="small text-muted mb-0">
                  For international copyright info:<br>
                  <a href="https://www.copyright.gov/" target="_blank" class="text-decoration-none">copyright.gov</a>
                </p>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
  <script>
    // Initialize first link as active on page load
    document.addEventListener('DOMContentLoaded', function() {
      const firstLink = document.querySelector('.toc-link');
      if (firstLink) {
        firstLink.classList.add('active');
      }
    });

    // Smooth scrolling for table of contents
    document.querySelectorAll('.toc-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          
          // Update active state immediately on click and keep it
          document.querySelectorAll('.toc-link').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>