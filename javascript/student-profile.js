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

  // Only allow editing Home Address and Mobile Number
  const editableInputs = form.querySelectorAll('#homeAddress, #mobileNumber');
  let originalValues = {};

  // Enable edit mode
  editBtn.addEventListener('click', () => {
    editableInputs.forEach(input => {
      originalValues[input.name] = input.value;
      input.readOnly = false;
      try { input.removeAttribute('readonly'); } catch (_) {}
      input.classList.remove('bg-light');
    });

    // Ensure all other inputs remain readOnly and gray
    form.querySelectorAll('input').forEach(input => {
      if (![...editableInputs].includes(input)) {
        input.readOnly = true;
        input.classList.add('bg-light');
      }
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
      try { input.setAttribute('readonly', 'readonly'); } catch (_) {}
      input.classList.add('bg-light');
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
    const home = document.getElementById('homeAddress');
    const mobileRegex = /^09\d{9}$/; // PH format
    
    if (!home.value.trim()) {
      showError(home, 'Home address is required.');
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
