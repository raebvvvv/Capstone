<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="css/register-student-custom.css">
  <link rel="stylesheet" href="css/main.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
  <img src="Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <main class="flex-grow-1 d-flex justify-content-center align-items-center py-4">
    <div class="register-card-custom">
      <div class="register-form-section-custom">
        <div class="form-section-title-custom mb-4">Student Registration</div>
        <form method="POST" action="#" enctype="multipart/form-data" autocomplete="off">
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lastName" name="lastName" required pattern="[A-Za-z\-]+" title="Only letters and hyphens (-) allowed">
            </div>
            <div class="col-md-5">
              <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="firstName" name="firstName" required pattern="[A-Za-z\- ]+" title="Only letters, spaces, and hyphens (-) allowed">
            </div>
            <div class="col-md-2">
              <label for="middleInitial" class="form-label">Middle Initial <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="middleInitial" name="middleInitial" maxlength="2" required pattern="[A-Za-z\-.]+" title="Only letters, hyphens (-), and dot (.) allowed">
            </div>
            <div class="col-md-2">
              <label for="suffix" class="form-label">Suffix</label>
              <input type="text" class="form-control" id="suffix" name="suffix" maxlength="8" placeholder="Jr., III" pattern="[A-Za-z0-9\-.]+" title="Only letters, numbers, hyphens (-), and dot (.) allowed">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-5">
              <label for="studentNumber" class="form-label">Student Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="studentNumber" name="studentNumber" required pattern="[A-Za-z0-9-]+" title="Only letters, numbers, and hyphens (-) allowed; no spaces">
            </div>
            <div class="col-md-7">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" name="email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Enter a valid email address (e.g., user@example.com)" placeholder="juankarlosdelacruz@iskolarngbayan.pup.edu.ph">
              <div class="email-note-custom mt-1" style="font-size:0.80em; color:#0d2956;">Personal email or webmail</div>
            </div>
          </div>
          <div class="row g-3 mb-1">
            <div class="col-md-6">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group mb-2">
                <input type="password" class="form-control rounded-start" id="password" name="password" required minlength="12" aria-describedby="togglePassword" />
                <button type="button" id="togglePassword" tabindex="-1" class="input-group-text rounded-end" style="background:transparent;border:none;outline:none;box-shadow:none;" aria-label="Show password">
                  <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.477 7-10 7S1 15.866 1 12 5.477 5 11 5s10 3.134 10 7z"/></svg>
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <label for="repassword" class="form-label">Re-enter Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="repassword" name="repassword" required minlength="12" placeholder="Password must match" disabled />
            </div>
          </div>
          <div class="mb-2" style="margin-top:-0.5rem;">
            <span class="password-rule" style="font-size:0.80em;">Must be <span class="fw-bold">at least 12 characters long</span></span><br />
            <span class="password-rule" style="font-size:0.80em;">Must include <span class="fw-bold">both uppercase and lowercase letters</span></span><br />
            <span class="password-rule" style="font-size:0.80em;">Must include <span class="fw-bold">at least one number and one character;</span><br />except: <span style="font-family:monospace;font-size:1em;">“ \\ &lt; &gt; ` ; | %</span></span>
          </div>
          <div class="mb-3 mt-4">
            <label for="cor" class="form-label">Upload COR <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="cor" name="cor" accept="application/pdf" required />
            <div class="cor-note-custom mt-1" style="font-size:0.80em;">Certificate of Registration <span class="fw-bold">(PDF format only, max 1MB)</span></div>
          </div>
          <button type="submit" class="btn register-btn-custom">Register</button>
        </form>
      </div>
      <div class="register-side-custom">
  <img src="Photos/account-registration-side (1).png" alt="Account Registration Design" />
      </div>
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
  <script>
  // View/hide password toggle
  document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    let visible = false;
    if (togglePassword) {
      togglePassword.addEventListener('click', function(e) {
        e.preventDefault();
        visible = !visible;
        passwordInput.type = visible ? 'text' : 'password';
        // Change icon (simple swap: open/closed eye)
        eyeIcon.innerHTML = visible
          ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0111 19c-5.523 0-10-3.134-10-7a9.978 9.978 0 013.582-5.997m3.11-2.13A9.956 9.956 0 0111 5c5.523 0 10 3.134 10 7 0 1.61-.635 3.122-1.75 4.415M15 12a3 3 0 11-6 0 3 3 0 016 0zm-6.364 6.364l12.728-12.728"/>'
          : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.477 7-10 7S1 15.866 1 12 5.477 5 11 5s10 3.134 10 7z"/>';
      });
    }

    // Validation patterns
    const patterns = {
      lastName: /^[A-Za-z-]+$/,
      firstName: /^[A-Za-z- ]+$/,
      middleInitial: /^[A-Za-z.-]+$/,
      suffix: /^[A-Za-z0-9.-]*$/
    };

    function showError(input, message) {
      input.classList.add('is-invalid');
      let feedback = input.parentNode.querySelector('.invalid-feedback');
      if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
      }
      feedback.textContent = message;
    }

    function clearError(input) {
      input.classList.remove('is-invalid');
      let feedback = input.parentNode.querySelector('.invalid-feedback');
      if (feedback) feedback.textContent = '';
    }

    function validateField(input, pattern, message) {
      if (input.value && !pattern.test(input.value)) {
        showError(input, message);
        return false;
      } else {
        clearError(input);
        return true;
      }
    }

    const form = document.querySelector('form');
    const lastName = document.getElementById('lastName');
    const firstName = document.getElementById('firstName');
    const middleInitial = document.getElementById('middleInitial');
    const suffix = document.getElementById('suffix');
    const studentNumber = document.getElementById('studentNumber');
    const email = document.getElementById('email');
    const repassword = document.getElementById('repassword');

    // Password validation rules
    function validatePassword(pw) {
      if (pw.length < 12) return 'Password must be at least 12 characters long.';
      if (!(/[A-Z]/.test(pw) && /[a-z]/.test(pw))) return 'Password must include both uppercase and lowercase letters.';
      if (!(/[0-9]/.test(pw))) return 'Password must include at least one number.';
      if (!(/[!@#$^&*()_+=\[\]{}:,.?/<>~]/.test(pw))) return 'Password must include at least one special character.';
      if (/[“\\<>`;|%]/.test(pw)) return 'Password contains forbidden characters: “ \\ < > ` ; | %';
      return '';
    }

    function validateRepassword() {
      const pwMsg = validatePassword(passwordInput.value);
      if (!repassword.value) {
        clearError(repassword);
        return true;
      }
      if (pwMsg) {
        clearError(repassword);
        return false;
      }
      if (repassword.value !== passwordInput.value) {
        showError(repassword, 'Passwords do not match.');
        return false;
      } else {
        clearError(repassword);
        return true;
      }
    }

    passwordInput.addEventListener('input', function() {
      const value = passwordInput.value;
      if (!value) {
        clearError(passwordInput);
        repassword.disabled = true;
        repassword.value = '';
        clearError(repassword);
        validateRepassword();
        return;
      }
      const msg = validatePassword(value);
      if (msg) {
        showError(passwordInput, msg);
        repassword.disabled = true;
        repassword.value = '';
        clearError(repassword);
      } else {
        clearError(passwordInput);
        repassword.disabled = false;
      }
      validateRepassword();
    });

    repassword.addEventListener('input', function() {
      validateRepassword();
    });

    // Email regex: common email rules, must have valid domain
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    email.addEventListener('input', function() {
      validateField(email, emailPattern, 'Enter a valid email address (e.g., user@example.com)');
    });

    const studentNumberPattern = /^[A-Za-z0-9-]+$/;
    studentNumber.addEventListener('input', function() {
      validateField(studentNumber, studentNumberPattern, 'Only letters, numbers, and hyphens (-) allowed; no spaces.');
    });

    function capitalizeWords(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    }

    function autoCapitalize(input, pattern, message) {
      let val = input.value;
      let cap = capitalizeWords(val);
      if (val !== cap) {
        input.value = cap;
      }
      validateField(input, pattern, message);
    }

    lastName.addEventListener('input', function() {
      autoCapitalize(lastName, patterns.lastName, 'Only letters and hyphens (-) allowed.');
    });
    firstName.addEventListener('input', function() {
      autoCapitalize(firstName, patterns.firstName, 'Only letters, spaces, and hyphens (-) allowed.');
    });
    middleInitial.addEventListener('input', function() {
      autoCapitalize(middleInitial, patterns.middleInitial, 'Only letters, hyphens (-), and dot (.) allowed.');
    });
    suffix.addEventListener('input', function() {
      autoCapitalize(suffix, patterns.suffix, 'Only letters, numbers, hyphens (-), and dot (.) allowed.');
    });

    const corInput = document.getElementById('cor');
    let corValid = true;
    form.addEventListener('submit', function(e) {
      let valid = true;
      valid &= validateField(lastName, patterns.lastName, 'Only letters and hyphens (-) allowed.');
      valid &= validateField(firstName, patterns.firstName, 'Only letters, spaces, and hyphens (-) allowed.');
      valid &= validateField(middleInitial, patterns.middleInitial, 'Only letters, hyphens (-), and dot (.) allowed.');
      valid &= validateField(suffix, patterns.suffix, 'Only letters, numbers, hyphens (-), and dot (.) allowed.');
      valid &= validateField(studentNumber, studentNumberPattern, 'Only letters, numbers, and hyphens (-) allowed; no spaces.');
      valid &= validateField(email, emailPattern, 'Enter a valid email address (e.g., user@example.com)');
      const pwMsg = validatePassword(passwordInput.value);
      if (pwMsg) {
        showError(passwordInput, pwMsg);
        valid = false;
      } else {
        clearError(passwordInput);
      }
      if (!validateRepassword()) {
        valid = false;
      }
      // COR validation: only PDF, max 1MB
      corValid = true;
      if (corInput.files.length === 0) {
        showError(corInput, 'Please upload your Certificate of Registration (PDF, max 1MB).');
        corValid = false;
      } else {
        const file = corInput.files[0];
        if (file.type !== 'application/pdf') {
          showError(corInput, 'Only PDF files are allowed.');
          corValid = false;
        } else if (file.size > 1048576) {
          showError(corInput, 'File size must not exceed 1MB.');
          corValid = false;
        } else {
          clearError(corInput);
        }
      }
      if (!valid || !corValid) {
        e.preventDefault();
      }
    });
  });
  </script>
<script>
// Allowed patterns
const patterns = {
  lastName: /^[A-Za-z-]+$/,
  firstName: /^[A-Za-z- ]+$/,
  middleInitial: /^[A-Za-z.-]+$/,
  suffix: /^[A-Za-z0-9.-]*$/
};

function showError(input, message) {
  input.classList.add('is-invalid');
  let feedback = input.parentNode.querySelector('.invalid-feedback');
  if (!feedback) {
    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    input.parentNode.appendChild(feedback);
  }
  feedback.textContent = message;
}

function clearError(input) {
  input.classList.remove('is-invalid');
  let feedback = input.parentNode.querySelector('.invalid-feedback');
  if (feedback) feedback.textContent = '';
}

function validateField(input, pattern, message) {
  if (input.value && !pattern.test(input.value)) {
    showError(input, message);
    return false;
  } else {
    clearError(input);
    return true;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');

  const lastName = document.getElementById('lastName');
  const firstName = document.getElementById('firstName');
  const middleInitial = document.getElementById('middleInitial');
  const suffix = document.getElementById('suffix');
  const studentNumber = document.getElementById('studentNumber');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const repassword = document.getElementById('repassword');

  // Password validation rules
  function validatePassword(pw) {
    // At least 12 characters
    if (pw.length < 12) return 'Password must be at least 12 characters long.';
    // Both uppercase and lowercase
    if (!(/[A-Z]/.test(pw) && /[a-z]/.test(pw))) return 'Password must include both uppercase and lowercase letters.';
    // At least one number
    if (!(/[0-9]/.test(pw))) return 'Password must include at least one number.';
    // At least one special character (not forbidden)
    if (!(/[!@#$^&*()_+=\[\]{}:,.?/<>~]/.test(pw))) return 'Password must include at least one special character.';
    // Forbidden characters
    if (/[“\\<>`;|%]/.test(pw)) return 'Password contains forbidden characters: “ \\ < > ` ; | %';
    return '';
  }


  function validateRepassword() {
    const pwMsg = validatePassword(password.value);
    if (!repassword.value) {
      clearError(repassword);
      return true;
    }
    if (pwMsg) {
      clearError(repassword);
      return false;
    }
    if (repassword.value !== password.value) {
      showError(repassword, 'Passwords do not match.');
      return false;
    } else {
      clearError(repassword);
      return true;
    }
  }

  password.addEventListener('input', function() {
    const msg = validatePassword(password.value);
    if (msg) {
      showError(password, msg);
      repassword.disabled = true;
      repassword.value = '';
      clearError(repassword);
    } else {
      clearError(password);
      repassword.disabled = false;
    }
    // Also re-validate repassword on password change
    validateRepassword();
  });

  repassword.addEventListener('input', function() {
    validateRepassword();
  });

  // Email regex: common email rules, must have valid domain
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  // Email validation
  email.addEventListener('input', function() {
    validateField(email, emailPattern, 'Enter a valid email address (e.g., user@example.com)');
  });

  const studentNumberPattern = /^[A-Za-z0-9-]+$/;
  // Student number validation
  studentNumber.addEventListener('input', function() {
    validateField(studentNumber, studentNumberPattern, 'Only letters, numbers, and hyphens (-) allowed; no spaces.');
  });


  function capitalizeWords(str) {
    return str.replace(/\b\w/g, c => c.toUpperCase());
  }

  function autoCapitalize(input, pattern, message) {
    let val = input.value;
    let cap = capitalizeWords(val);
    if (val !== cap) {
      input.value = cap;
    }
    validateField(input, pattern, message);
  }

  lastName.addEventListener('input', function() {
    autoCapitalize(lastName, patterns.lastName, 'Only letters and hyphens (-) allowed.');
  });
  firstName.addEventListener('input', function() {
    autoCapitalize(firstName, patterns.firstName, 'Only letters, spaces, and hyphens (-) allowed.');
  });
  middleInitial.addEventListener('input', function() {
    autoCapitalize(middleInitial, patterns.middleInitial, 'Only letters, hyphens (-), and dot (.) allowed.');
  });
  suffix.addEventListener('input', function() {
    autoCapitalize(suffix, patterns.suffix, 'Only letters, numbers, hyphens (-), and dot (.) allowed.');
  });

  // On submit validation
  form.addEventListener('submit', function(e) {
    let valid = true;
    valid &= validateField(lastName, patterns.lastName, 'Only letters and hyphens (-) allowed.');
    valid &= validateField(firstName, patterns.firstName, 'Only letters, spaces, and hyphens (-) allowed.');
    valid &= validateField(middleInitial, patterns.middleInitial, 'Only letters, hyphens (-), and dot (.) allowed.');
    valid &= validateField(suffix, patterns.suffix, 'Only letters, numbers, hyphens (-), and dot (.) allowed.');
  valid &= validateField(studentNumber, studentNumberPattern, 'Only letters, numbers, and hyphens (-) allowed; no spaces.');
  valid &= validateField(email, emailPattern, 'Enter a valid email address (e.g., user@example.com)');
    // Password validation
    const pwMsg = validatePassword(password.value);
    if (pwMsg) {
      showError(password, pwMsg);
      valid = false;
    } else {
      clearError(password);
    }
    // Repassword must match and password must be valid
    if (!validateRepassword()) {
      valid = false;
    }
    if (!valid) {
      e.preventDefault();
    }
  });
});
</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
