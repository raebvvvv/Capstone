// Employee profile editing - specialized version
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('profileForm');
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');

  if (!form || !editBtn || !saveBtn || !cancelBtn) return;

  // Allow editing for employee-specific fields including department
  const textEditableInputs = form.querySelectorAll('#homeAddress, #mobileNumber, #lastName, #firstName, #middleName, #suffix');
  const selectEditableInputs = form.querySelectorAll('#campus, #college, #program, #academicLevel, #department');
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

    // Re-initialize dropdown options from academicData
    try {
      const academicLevel = document.getElementById('academicLevel');
      const college = document.getElementById('college');
      const program = document.getElementById('program');
      const campus = document.getElementById('campus');
      const department = document.getElementById('department');

      // Clear dataset "locked" flags set by dropdown initializer
      [academicLevel, college, program, department].forEach(el => {
        if (el && el.dataset && el.dataset.locked) {
          try { delete el.dataset.locked; } catch(_) {}
        }
      });

      // Trigger population
      document.dispatchEvent(new Event('ipmo:form:show'));

      // Helpers for options
      const hasOption = (sel, val) => !!sel && Array.from(sel.options).some(o => o.value === val);
      const ensureOption = (sel, val) => {
        if (!sel || !val) return;
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = val;
        sel.appendChild(opt);
        sel.value = val;
      };

      // Restore current selections after population in a safe order
      if (academicLevel && originalValues.academicLevel) {
        academicLevel.value = originalValues.academicLevel;
        academicLevel.dispatchEvent(new Event('change')); // adjust dependent lists
      }

      if (college && originalValues.college) {
        college.value = originalValues.college;
        college.dispatchEvent(new Event('change')); // adjust dependent lists (dept/program for UG)
      }

      if (campus && originalValues.campus) {
        campus.value = originalValues.campus;
      }

      // Only restore program if enabled; if missing in dataset, append fallback option
      if (program && !program.disabled && originalValues.program) {
        if (hasOption(program, originalValues.program)) {
          program.value = originalValues.program;
        } else {
          // Add fallback so current value remains visible/editable
          ensureOption(program, originalValues.program);
        }
      }

      // Restore department; if not in options (college mapping missing), add fallback and enable
      if (department && originalValues.department) {
        if (hasOption(department, originalValues.department)) {
          department.value = originalValues.department;
        } else {
          ensureOption(department, originalValues.department);
          department.disabled = false;
        }
      }
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
        delay: 2000
    });
    bsToast.show();
  }

  // Form submission with validation
  form.addEventListener('submit', function(e) {
    clearErrors();
    let isValid = true;

    const mobile = document.getElementById('mobileNumber');
    const home = document.getElementById('homeAddress');
    const lastName = document.getElementById('lastName');
    const firstName = document.getElementById('firstName');
    const middleName = document.getElementById('middleName');
    const suffix = document.getElementById('suffix');
    const campusSel = document.getElementById('campus');
    const collegeSel = document.getElementById('college');
    const programSel = document.getElementById('program');
    const levelSel = document.getElementById('academicLevel');
    const departmentSel = document.getElementById('department');
    const mobileRegex = /^09\d{9}$/;
    
    if (!home.value.trim()) {
      showError(home, 'Home address is required.');
      isValid = false;
    }

    if (!mobileRegex.test(mobile.value.trim())) {
      showError(mobile, 'Mobile number must be 11 digits and start with 09.');
      isValid = false;
    }

    // Name validation
    // Allow letters with optional trailing dot per token, single spaces (e.g., "Ma. Criselle")
    const nameRegex = /^[A-Za-z]+(?:\.)?(?:\s[A-Za-z]+(?:\.)?)*$/;
    if (!nameRegex.test(firstName.value.trim())) { 
  showError(firstName, 'First name should contain letters, optional dots, and single spaces.'); 
      isValid = false; 
    }
    if (!nameRegex.test(lastName.value.trim())) { 
  showError(lastName, 'Last name should contain letters, optional dots, and single spaces.'); 
      isValid = false; 
    }
    if (middleName.value.trim() && !nameRegex.test(middleName.value.trim())) { 
  showError(middleName, 'Middle name should contain letters, optional dots, and single spaces.'); 
      isValid = false; 
    }

    // Suffix validation (if provided)
    if (suffix.value.trim()) {
      const validSuffixes = ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V'];
      if (!validSuffixes.includes(suffix.value.trim())) {
        showError(suffix, 'Please enter a valid suffix (Jr., Sr., I, II, III, IV, V) or leave blank.');
        isValid = false;
      }
    }

  // Dropdown validation (program optional). Allow 'N/A' sentinel for campus/college/department.
  const emptyOrNA = v => !v || v.trim() === '';
  if (emptyOrNA(campusSel.value)) { showError(campusSel, 'Please select a campus or choose N/A.'); isValid = false; }
  if (!levelSel.value) { showError(levelSel, 'Please select an academic level.'); isValid = false; }
  if (emptyOrNA(collegeSel.value)) { showError(collegeSel, 'Please select a college or choose N/A.'); isValid = false; }
  if (emptyOrNA(departmentSel.value)) { showError(departmentSel, 'Please select a department or choose N/A.'); isValid = false; }

    if (!isValid) {
      e.preventDefault();
      showToast('Please fix the errors', 'danger');
    }
  });

  // Show success toast if form was submitted successfully
  if (document.querySelector('.alert-success')) {
    showToast('Profile updated successfully');
  }
});
