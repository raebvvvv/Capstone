// student-profile.js
document.addEventListener('DOMContentLoaded', () => {
  // --- Logout ---
  const signOutBtn = document.getElementById('signOutBtn');
  if (signOutBtn) {
    signOutBtn.addEventListener('click', () => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '../../User/Beforelogin/logout.php';

      if (typeof csrfToken !== 'undefined') {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
      }

      document.body.appendChild(form);
      form.submit();
    });
  }

  // --- Profile editing ---
  const form = document.getElementById('profileForm');
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');

  if (!form || !editBtn || !saveBtn || !cancelBtn) return;

  // Check edit restrictions
  if (typeof nextEditAllowed !== 'undefined' && nextEditAllowed) {
    const now = new Date();
    const nextDate = new Date(nextEditAllowed);

    if (now < nextDate) {
      // Disable editing if not yet allowed
      editBtn.disabled = true;
      
      // Add subtle tooltip using Bootstrap
      const tooltip = new bootstrap.Tooltip(editBtn, {
        title: `Profile editing available after ${nextDate.toLocaleDateString()}`,
        placement: 'right'
      });
    }
  }

  // Select all inputs EXCEPT those with class 'lock'
  const editableInputs = form.querySelectorAll('input:not(.lock)');
  let originalValues = {};

  // Enable edit mode
  editBtn.addEventListener('click', () => {
    editableInputs.forEach(input => {
      originalValues[input.name] = input.value;
      input.readOnly = false;
    });

    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
  });

  // Cancel edit mode
  cancelBtn.addEventListener('click', () => {
    editableInputs.forEach(input => {
      input.value = originalValues[input.name] || '';
      input.readOnly = true;
    });

    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.style.display = 'inline-block';
  });

  // If editing is locked, add tooltip
  if (editBtn.disabled) {
    const tooltip = new bootstrap.Tooltip(editBtn, {
      title: 'Profile can only be edited once every 30 days',
      placement: 'bottom'
    });
  }


  // Helper to clear previous error messages
  function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
  }

  // Helper to show error under input
  function showError(input, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-danger small mt-1';
    errorDiv.innerText = message;
    input.insertAdjacentElement('afterend', errorDiv);
  }

  // Toast notification helper
  function showToast(message, type = 'success') {
    const toast = document.getElementById('profileToast');
    toast.classList.remove('bg-success', 'bg-danger');
    toast.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
    toast.querySelector('.toast-body').textContent = message;
    
    const bsToast = new bootstrap.Toast(toast, {
        delay: 2000  // Show for 2 seconds instead of default 5 seconds
    });
    bsToast.show();
  }

  // Form submission with validation
  form.addEventListener('submit', function(e) {
    clearErrors(); // remove old errors
    let isValid = true;

    const mobile = document.getElementById('mobileNumber');
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const middleInitial = document.getElementById('middleInitial');
    const suffix = document.getElementById('suffix');

    const nameRegex = /^[A-Za-z\s]+$/;
    const mobileRegex = /^09\d{9}$/; // PH format
    const validSuffixes = ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V'];

    if (!nameRegex.test(firstName.value.trim())) {
      showError(firstName, 'First name should only contain letters.');
      isValid = false;
    }

    if (!nameRegex.test(lastName.value.trim())) {
      showError(lastName, 'Last name should only contain letters.');
      isValid = false;
    }

    if (middleInitial.value && !/^[A-Z]$/.test(middleInitial.value.trim())) {
      showError(middleInitial, 'Middle initial must be a single uppercase letter.');
      isValid = false;
    }

    if (suffix.value && !validSuffixes.includes(suffix.value.trim())) {
      showError(suffix, 'Valid suffix: Jr., Sr., I, II, III, IV, V or leave blank.');
      isValid = false;
    }

    if (!mobileRegex.test(mobile.value.trim())) {
      showError(mobile, 'Mobile number must be 11 digits and start with 09.');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault(); // stop form submission if invalid
      showToast('Please fix the errors', 'danger');
    }
  });

  // After successful save
  if (document.querySelector('.badge.bg-success')) {
    showToast('Profile updated successfully');
  }
});
