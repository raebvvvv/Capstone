<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Industrial Property Information | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/copyright-info.css'); ?>">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
  <a class="navbar-brand d-flex align-items-center" href="<?php echo asset_url('index.php'); ?>">
        <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('index.php'); ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Back Button Below Navbar, scrolls with content, transparent background -->
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { 
  render_back_link('index.php'); 
    } ?>
  </div>

  <!-- Main Content -->
  <section class="container py-5">
    <div class="row">
      <div class="col-12">
        <h1 class="fw-bold mb-4">Understanding Industrial Property</h1>
      </div>
    </div>
    
    <div class="row">
      <!-- Sidebar Table of Contents -->
      <div class="col-lg-3">
        <div class="sidebar-toc">
          <h5>Table of Contents</h5>
          <ul class="toc-list">
            <li><a href="#introduction" class="toc-link">What is Industrial Property?</a></li>
            <li><a href="#pup-definition" class="toc-link">Industrial Property at PUP</a></li>
            <li><a href="#pup-services" class="toc-link">Industrial Property Services at PUP</a></li>
            <li class="toc-main-section">
              <a href="#types" class="toc-link">Types of Industrial Property</a>
              <ul class="toc-subcategory-main">
                <li class="toc-section-header">
                  <span class="toc-category-title">🔬 Technical Innovations</span>
                  <ul class="toc-subcategory">
                    <li><a href="#patents" class="toc-link toc-sub">Patents & Utility Models</a></li>
                  </ul>
                </li>
                <li class="toc-section-header">
                  <span class="toc-category-title">🏷️ Commercial Identifiers</span>
                  <ul class="toc-subcategory">
                    <li><a href="#trademarks" class="toc-link toc-sub">Trademarks & Industrial Designs</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li><a href="#application" class="toc-link">Application Process</a></li>
            <li><a href="#duration" class="toc-link">Protection Duration</a></li>
            <li><a href="#attribution" class="toc-link">Educational Resource Attribution</a></li>
          </ul>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col-lg-9">
        
        <!-- Introduction -->
        <div id="introduction" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">What is Industrial Property?</h3>
            <p class="lead">Industrial property is one of the two main types of intellectual property, covering innovations, industrial designs, trademarks, service marks, geographical indications, and other commercial identifiers.</p>
            <p>Industrial property includes patents for inventions, utility models for minor innovations, trademarks for brand identification, and industrial designs for aesthetic aspects of products. These rights help protect business interests and encourage innovation in commerce and industry.</p>
            <p>Unlike copyright (which protects artistic and literary works), industrial property focuses on protecting inventions, brands, and commercial designs that have practical applications in business and industry.</p>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> This information is based on content from the <a href="https://www.wipo.int/en/web/patents" target="_blank" class="text-decoration-none">World Intellectual Property Organization (WIPO)</a>. Content has been adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- PUP Definition -->
        <div id="pup-definition" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Industrial Property at PUP</h3>
            <div class="pup-highlight">
              <h5>PUP Citizens Charter</h5>
              <p class="mb-0">PUP's industrial property services facilitate the process of registration for <strong>patents, utility models, trademarks, and industrial designs</strong> developed by students, faculty, and researchers. This ensures the protection of innovations, brands, and commercial designs created within the university.</p>
            </div>
            
            <div class="highlight-box">
            <strong>Source Attribution:</strong> This information is based on the <a href="https://drive.google.com/file/d/0B1BuDAuN0r8SbmFEU1RJUDFYU1E/view?resourcekey=0-ntyYkBAnyIFKdRJWhiKEcw" target="_blank" class="text-decoration-none">PUP Citizens Charter</a>. Content has been adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- PUP Services -->
        <div id="pup-services" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Industrial Property Services at PUP</h3>
            
            <div class="row">
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Industrial Property Types at PUP:</h5>
                <ul class="feature-list">
                  <li><span class="feature-icon">🔬</span>Patents (Major Inventions)</li>
                  <li><span class="feature-icon">⚙️</span>Utility Models (Minor Innovations)</li>
                  <li><span class="feature-icon">🏷️</span>Trademarks (Brand Names & Logos)</li>
                  <li><span class="feature-icon">🎨</span>Industrial Designs (Product Aesthetics)</li>
                  <li><span class="feature-icon">💡</span>Technical Solutions</li>
                  <li><span class="feature-icon">🔧</span>Engineering Innovations</li>
                  <li><span class="feature-icon">📱</span>Commercial Applications</li>
                </ul>
              </div>
              
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Why Use PUP e-IPMO?</h5>
                <div class="row">
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">⚡</span>
                      <div>
                        <strong>Expert Guidance</strong>
                        <p class="small text-muted mb-0">Specialized support for IP applications</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🎯</span>
                      <div>
                        <strong>Academic Focus</strong>
                        <p class="small text-muted mb-0">Tailored for university innovations & brands</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🛡️</span>
                      <div>
                        <strong>Legal Protection</strong>
                        <p class="small text-muted mb-0">Comprehensive industrial property rights</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-3">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">🤝</span>
                      <div>
                        <strong>University Support</strong>
                        <p class="small text-muted mb-0">End-to-end application assistance</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex align-items-start">
                      <span class="feature-icon me-2">👥</span>
                      <div>
                        <strong>Collaborative Approach</strong>
                        <p class="small text-muted mb-0">Partnership between inventors and university</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Types of Industrial Property -->
        <div id="types" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Types of Industrial Property</h3>
            <p>Industrial property encompasses four main categories, each protecting different aspects of commercial and industrial innovation:</p>
            
            <div class="row">
              <div class="col-md-6 mb-4">
                <div class="highlight-box tech-innovation-box" style="background: #f8f9fa; border-color: #dee2e6; border-left: 4px solid #6c757d;">
                  <h5 class="fw-bold mb-2" style="color: #495057;">🔬 Technical Innovations</h5>
                  <ul class="feature-list">
                    <li><span class="feature-icon">🔬</span><strong>Patents:</strong> Major inventions & processes</li>
                    <li><span class="feature-icon">⚙️</span><strong>Utility Models:</strong> Minor improvements & innovations</li>
                  </ul>
                </div>
              </div>
              
              <div class="col-md-6 mb-4">
                <div class="highlight-box commercial-id-box" style="background: #f8f9fa; border-color: #dee2e6; border-left: 4px solid #6c757d;">
                  <h5 class="fw-bold mb-2" style="color: #495057;">🏷️ Commercial Identifiers</h5>
                  <ul class="feature-list">
                    <li><span class="feature-icon">🏷️</span><strong>Trademarks:</strong> &nbsp; Brand names, logos, slogans</li>
                    <li><span class="feature-icon">🎨</span><strong>Industrial Designs:</strong> Product appearance & aesthetics</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="highlight-box" style="background: #d1ecf1; border-color: #bee5eb;">
              <strong>Note:</strong> All these categories can be processed through PUP e-IPMO services and provide different types of commercial protection.
            </div>
          </div>
        </div>

        <!-- Patents & Utility Models -->
        <div id="patents" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Patents & Utility Models</h3>
            
            <div class="row">
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Patents & Utility Models</h5>
                <div class="mb-3">
                  <h6 class="fw-bold">Patents:</h6>
                  <ul class="feature-list">
                    <li><span class="feature-icon">✓</span>New inventions with high inventive step</li>
                    <li><span class="feature-icon">✓</span>20-year protection period</li>
                    <li><span class="feature-icon">✓</span>Complex technical innovations</li>
                  </ul>
                </div>
                <div>
                  <h6 class="fw-bold">Utility Models:</h6>
                  <ul class="feature-list">
                    <li><span class="feature-icon">✓</span>Minor improvements to existing inventions</li>
                    <li><span class="feature-icon">✓</span>7-year protection period</li>
                    <li><span class="feature-icon">✓</span>Faster registration process</li>
                  </ul>
                </div>
              </div>
              
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Trademarks & Industrial Designs</h5>
                <div class="mb-3">
                  <h6 class="fw-bold">Trademarks:</h6>
                  <ul class="feature-list">
                    <li><span class="feature-icon">✓</span>Brand names, logos, slogans</li>
                    <li><span class="feature-icon">✓</span>10-year renewable protection</li>
                    <li><span class="feature-icon">✓</span>Market identification and branding</li>
                  </ul>
                </div>
                <div>
                  <h6 class="fw-bold">Industrial Designs:</h6>
                  <ul class="feature-list">
                    <li><span class="feature-icon">✓</span>Aesthetic aspects of products</li>
                    <li><span class="feature-icon">✓</span>5-year renewable protection</li>
                    <li><span class="feature-icon">✓</span>Visual design and appearance</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="highlight-box" style="background: #d1ecf1; border-color: #bee5eb;">
              <strong>Note:</strong> All these categories fall under intellectual property protection and can be processed through PUP e-IPMO services.
            </div>
          </div>
        </div>

        <!-- Patent Requirements -->
        <div id="requirements" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Patent Requirements</h3>
            
            <div class="row">
              <div class="col-md-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Novelty</h5>
                <p>The invention must be new and not disclosed anywhere in the world before the filing date.</p>
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Not previously known</li>
                  <li><span class="feature-icon">✓</span>Not publicly available</li>
                  <li><span class="feature-icon">✓</span>Original creation</li>
                </ul>
              </div>
              
              <div class="col-md-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Inventive Step</h5>
                <p>The invention must not be obvious to a person skilled in the relevant field.</p>
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Non-obvious solution</li>
                  <li><span class="feature-icon">✓</span>Technical advancement</li>
                  <li><span class="feature-icon">✓</span>Creative problem-solving</li>
                </ul>
              </div>
              
              <div class="col-md-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Industrial Application</h5>
                <p>The invention must be capable of being made or used in industry.</p>
                <ul class="feature-list">
                  <li><span class="feature-icon">✓</span>Practical utility</li>
                  <li><span class="feature-icon">✓</span>Commercial viability</li>
                  <li><span class="feature-icon">✓</span>Real-world application</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> These patent requirements are based on standards from the <a href="https://www.ipophil.gov.ph/services/patent/" target="_blank" class="text-decoration-none">Intellectual Property Office of the Philippines (IPOPhil)</a> and international patent law principles. Content has been adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- Application Process -->
        <div id="application" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Industrial Property Application Process</h3>
            
            <div class="row">
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">General Application Steps:</h5>
                <ul class="feature-list">
                  <li><span class="feature-icon">1</span><strong>Prior Search:</strong> Research existing IP and publications</li>
                  <li><span class="feature-icon">2</span><strong>Application Preparation:</strong> Detailed description and documentation</li>
                  <li><span class="feature-icon">3</span><strong>Filing:</strong> Submit application to IPOPhil</li>
                  <li><span class="feature-icon">4</span><strong>Examination:</strong> Technical and legal review</li>
                  <li><span class="feature-icon">5</span><strong>Grant/Registration:</strong> IP rights awarded if approved</li>
                </ul>
              </div>
              
              <div class="col-md-6">
                <h5 class="fw-bold mb-3" style="color: #198754;">Required Documents:</h5>
                <ul class="feature-list">
                  <li><span class="feature-icon">📄</span>Patent application form</li>
                  <li><span class="feature-icon">📝</span>Detailed specification</li>
                  <li><span class="feature-icon">🎯</span>Patent claims</li>
                  <li><span class="feature-icon">📊</span>Drawings (if applicable)</li>
                  <li><span class="feature-icon">📋</span>Abstract</li>
                  <li><span class="feature-icon">💰</span>Filing fees</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> Process information based on <a href="https://www.ipophil.gov.ph/patent/" target="_blank" class="text-decoration-none">IPOPhil Patent Guidelines</a>. Content adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- Duration -->
        <div id="duration" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Industrial Property Protection Duration</h3>
            
            <div class="table-responsive">
              <table class="table info-table">
                <thead>
                  <tr>
                    <th>Type of IP Protection</th>
                    <th>Protection Duration</th>
                    <th>Renewal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="patent-highlight">
                    <td><strong>Patents</strong></td>
                    <td>20 years from filing date</td>
                    <td>Annual maintenance fees required (per Philippine IP Code Section 54)</td>
                  </tr>
                  <tr>
                    <td><strong>Utility Models</strong></td>
                    <td>7 years from filing date</td>
                    <td>Not renewable (per Philippine IP Code Section 109.3)</td>
                  </tr>
                  <tr>
                    <td><strong>Trademarks</strong></td>
                    <td>10 years from registration</td>
                    <td>Renewable indefinitely (per Philippine IP Code Section 145)</td>
                  </tr>
                  <tr>
                    <td><strong>Industrial Designs</strong></td>
                    <td>5 years from registration</td>
                    <td>Renewable for two 5-year periods (per Philippine IP Code Section 118)</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Benefits -->
        <div id="benefits" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">Benefits of Industrial Property Protection</h3>
            
            <div class="row">
              <div class="col-lg-6 mb-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Economic Benefits:</h5>
                <ul class="feature-list small">
                  <li><span class="feature-icon">💰</span><strong>Licensing Revenue:</strong> Generate income through licensing</li>
                  <li><span class="feature-icon">📈</span><strong>Market Advantage:</strong> Exclusive exploitation rights</li>
                  <li><span class="feature-icon">🏪</span><strong>Commercial Value:</strong> Enhanced business value</li>
                  <li><span class="feature-icon">🛡️</span><strong>Brand Protection:</strong> Safeguard from infringement</li>
                  <li><span class="feature-icon">🤝</span><strong>Partnership Opportunities:</strong> Attract investors</li>
                </ul>
              </div>
              
              <div class="col-lg-6 mb-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Strategic Benefits:</h5>
                <ul class="feature-list small">
                  <li><span class="feature-icon">🛡️</span><strong>Legal Protection:</strong> Prevent unauthorized use</li>
                  <li><span class="feature-icon">🏆</span><strong>Recognition:</strong> Innovation acknowledgment</li>
                  <li><span class="feature-icon">📚</span><strong>Knowledge Sharing:</strong> Technology advancement</li>
                  <li><span class="feature-icon">🌐</span><strong>International Protection:</strong> Extend to other countries</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box">
              <strong>Source Attribution:</strong> Benefits information compiled from <a href="https://www.wipo.int/en/web/patents" target="_blank" class="text-decoration-none">WIPO Patent Resources</a> and <a href="https://www.ipophil.gov.ph/services/patent/" target="_blank" class="text-decoration-none">IPOPhil Guidelines</a>. Content adapted for educational purposes at PUP e-IPMO.
            </div>
          </div>
        </div>

        <!-- International System -->
        <div id="international" class="info-card content-section">
          <div class="card-body">
            <h3 class="section-title">International Patent System</h3>
            
            <div class="row">
              <div class="col-lg-6 mb-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">PCT System:</h5>
                <p class="small">The Patent Cooperation Treaty (PCT) enables inventors to seek patent protection internationally.</p>
                <ul class="feature-list small">
                  <li><span class="feature-icon">🌍</span>Single international application</li>
                  <li><span class="feature-icon">🔍</span>International search & examination</li>
                  <li><span class="feature-icon">📋</span>Streamlined multi-country process</li>
                  <li><span class="feature-icon">⏰</span>Extended national phase entry time</li>
                </ul>
              </div>
              
              <div class="col-lg-6 mb-4">
                <h5 class="fw-bold mb-3" style="color: #198754;">Regional Systems:</h5>
                <ul class="feature-list small">
                  <li><span class="feature-icon">🇪🇺</span><strong>EPO:</strong> European Patent Office</li>
                  <li><span class="feature-icon">🇺🇸</span><strong>USPTO:</strong> US Patent Office</li>
                  <li><span class="feature-icon">🇯🇵</span><strong>JPO:</strong> Japan Patent Office</li>
                  <li><span class="feature-icon">🇨🇳</span><strong>CNIPA:</strong> China National IP</li>
                </ul>
              </div>
            </div>
            
            <div class="highlight-box" style="background: #d4edda; border-color: #c3e6cb;">
              <strong>Philippines & International Patents:</strong> The Philippines is a member of WIPO and follows international standards for patent protection. Filipino inventors can file both domestic and international patent applications.
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
                  <a href="https://www.ipophil.gov.ph/patent/" target="_blank" class="text-decoration-none">Intellectual Property Office of the Philippines</a>
                </p>
                <p class="small text-muted mb-0">
                  <strong>PUP Citizens Charter:</strong><br>
                  <a href="https://drive.google.com/file/d/0B1BuDAuN0r8SbmFEU1RJUDFYU1E/view?resourcekey=0-ntyYkBAnyIFKdRJWhiKEcw" target="_blank" class="text-decoration-none">Official PUP Document</a>
                </p>
              </div>
              
              <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2" style="color: #900c0c;">International Sources:</h6>
                <p class="small text-muted mb-1">
                  <strong>WIPO:</strong><br>
                  <a href="https://www.wipo.int/en/web/patents" target="_blank" class="text-decoration-none">World Intellectual Property Organization</a>
                </p>
                <p class="small text-muted mb-0">
                  <strong>Philippine IP Code:</strong><br>
                  <a href="https://www.ipophil.gov.ph/resources/republic-acts-and-related-laws/" target="_blank" class="text-decoration-none">Republic Act No. 8293</a>
                </p>
              </div>
              
              <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2" style="color: #900c0c;">Additional Resources:</h6>
                <p class="small text-muted mb-1">
                  For Philippine patent registration:<br>
                  <a href="https://www.ipophil.gov.ph/" target="_blank" class="text-decoration-none">ipophil.gov.ph</a>
                </p>
                <p class="small text-muted mb-0">
                  For international patent info:<br>
                  <a href="https://www.wipo.int/" target="_blank" class="text-decoration-none">wipo.int</a>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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