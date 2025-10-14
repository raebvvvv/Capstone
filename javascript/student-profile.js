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

  // Allow editing Home Address, Mobile Number, and Name fields; handle selects separately
  // Include suffix so it becomes editable (was previously omitted, leaving it locked)
  const textEditableInputs = form.querySelectorAll('#homeAddress, #mobileNumber, #lastName, #firstName, #middleName, #suffix');
  const selectEditableInputs = form.querySelectorAll('#campus, #college, #program, #academicLevel');
  let originalValues = {};

  // Enable edit mode
  editBtn.addEventListener('click', () => {
    // unlock text inputs
    textEditableInputs.forEach(input => {
      originalValues[input.id] = input.value;
      input.readOnly = false;
      try { input.removeAttribute('readonly'); } catch (_) {}
      input.classList.remove('bg-light');
    });

    // unlock selects
    selectEditableInputs.forEach(select => {
      originalValues[select.id] = select.value;
      select.disabled = false;
      select.classList.remove('bg-light');
    });

    // Re-initialize dropdown options from dynamic catalogs (event listener repopulates & preserves values)
    try {
      document.dispatchEvent(new Event('ipmo:form:show'));
      // restore current selections after population
      const academicLevel = document.getElementById('academicLevel');
      const college = document.getElementById('college');
      const program = document.getElementById('program');
      const campus = document.getElementById('campus');
      if (academicLevel && originalValues.academicLevel) academicLevel.value = originalValues.academicLevel;
      if (academicLevel) academicLevel.dispatchEvent(new Event('change')); // adjust dependent lists
      if (college && originalValues.college) college.value = originalValues.college;
      if (program && originalValues.program) program.value = originalValues.program;
      if (campus && originalValues.campus) campus.value = originalValues.campus;
    } catch(_) {}

    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
  });

  // Cancel edit mode
  cancelBtn.addEventListener('click', () => {
    // restore text inputs
    textEditableInputs.forEach(input => {
      input.value = originalValues[input.id] || input.value;
      input.readOnly = true;
      try { input.setAttribute('readonly', 'readonly'); } catch (_) {}
      input.classList.add('bg-light');
    });
    // restore selects
    selectEditableInputs.forEach(select => {
      const prev = originalValues[select.id];
      if (typeof prev !== 'undefined') select.value = prev;
      select.disabled = true;
      select.classList.add('bg-light');
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
    const lastName = document.getElementById('lastName');
    const firstName = document.getElementById('firstName');
    const middleName = document.getElementById('middleName');
  const campusSel = document.getElementById('campus');
    const collegeSel = document.getElementById('college');
    const programSel = document.getElementById('program');
    const levelSel = document.getElementById('academicLevel');
  const suffixInput = document.getElementById('suffix');
    const mobileRegex = /^09\d{9}$/; // PH format
    
    if (!home.value.trim()) {
      showError(home, 'Home address is required.');
      isValid = false;
    }

    if (!mobileRegex.test(mobile.value.trim())) {
      showError(mobile, 'Mobile number must be 11 digits and start with 09.');
      isValid = false;
    }

    // Basic client-side checks for names and dropdowns
    // Allow letters with optional trailing dot per token, single spaces (e.g., "Ma. Criselle")
    const nameRegex = /^[A-Za-z]+(?:\.)?(?:\s[A-Za-z]+(?:\.)?)*$/;
  if (!nameRegex.test(firstName.value.trim())) { showError(firstName, 'First name should contain letters, optional dots, and single spaces.'); isValid = false; }
  if (!nameRegex.test(lastName.value.trim())) { showError(lastName, 'Last name should contain letters, optional dots, and single spaces.'); isValid = false; }
  if (middleName.value.trim() && !nameRegex.test(middleName.value.trim())) { showError(middleName, 'Middle name should contain letters, optional dots, and single spaces.'); isValid = false; }
  // Treat 'N/A' as acceptable sentinel for campus/college/program
  const emptyOrNA = v => !v || v.trim() === '';
  if (emptyOrNA(campusSel.value)) { showError(campusSel, 'Please select a campus or choose N/A.'); isValid = false; }
    if (!levelSel.value) { showError(levelSel, 'Please select an academic level.'); isValid = false; }
  if (emptyOrNA(collegeSel.value)) { showError(collegeSel, 'Please select a college or choose N/A.'); isValid = false; }
  if (emptyOrNA(programSel.value)) { showError(programSel, 'Please select a program or choose N/A.'); isValid = false; }

    // Suffix optional validation: letters and periods only up to 10 chars
    if (suffixInput && suffixInput.value.trim()) {
      if (!/^[A-Za-z.]{1,10}$/.test(suffixInput.value.trim())) {
        showError(suffixInput, 'Suffix can contain letters and periods only (max 10 characters).');
        isValid = false;
      }
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
