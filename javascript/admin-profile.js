(function(){
  'use strict';
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const profileModalEl = document.getElementById('adminProfileModal');
  const passwordForm = document.getElementById('profileChangePasswordForm');
  const alertBox = document.getElementById('profileChangePassAlert');
  const nameInput = document.getElementById('profileAdminName');
  const emailInput = document.getElementById('profileAdminEmail');
  const profileForm = document.getElementById('profileInfoForm');
  const editBtn = document.getElementById('profileEditBtn');
  const cancelBtn = document.getElementById('profileCancelBtn');
  const saveBtn = document.getElementById('profileSaveBtn');
  let originalProfile = { name: '', email: '' };

  // Populate name/email from data attributes on button or body
  function populateProfile(){
  if(!nameInput || !emailInput) return;
    // Source order of precedence: modal data attributes -> triggering button -> body dataset
    const source = profileModalEl || document.querySelector('[data-admin-name]') || document.body;
    const adminName = source?.getAttribute('data-admin-name') || document.body.getAttribute('data-admin-name');
    const adminEmail = source?.getAttribute('data-admin-email') || document.body.getAttribute('data-admin-email');
  if(adminName && nameInput.tagName === 'INPUT') nameInput.value = adminName;
  if(adminEmail && emailInput.tagName === 'INPUT') emailInput.value = adminEmail;
  // Capture originals for cancel logic
  originalProfile.name = nameInput.value;
  originalProfile.email = emailInput.value;
  }

  if(profileModalEl){
    profileModalEl.addEventListener('show.bs.modal', populateProfile);
  }

  function enterEditMode(){
    if(!nameInput || !emailInput) return;
    originalProfile.name = nameInput.value;
    originalProfile.email = emailInput.value;
    nameInput.disabled = false;
    emailInput.disabled = false;
    editBtn?.classList.add('d-none');
    cancelBtn?.classList.remove('d-none');
    saveBtn?.classList.remove('d-none');
    nameInput.focus();
  }

  function exitEditMode(revert){
    if(!nameInput || !emailInput) return;
    if(revert){
      nameInput.value = originalProfile.name;
      emailInput.value = originalProfile.email;
    }
    nameInput.disabled = true;
    emailInput.disabled = true;
    editBtn?.classList.remove('d-none');
    cancelBtn?.classList.add('d-none');
    saveBtn?.classList.add('d-none');
  }

  editBtn?.addEventListener('click', enterEditMode);
  cancelBtn?.addEventListener('click', () => exitEditMode(true));

  function showAlert(msg, type='danger'){
    if(!alertBox) return;
    alertBox.className = 'alert alert-' + type;
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
  }
  function hideAlert(){ if(alertBox){ alertBox.classList.add('d-none'); } }

  if(profileForm){
    profileForm.addEventListener('submit', async function(e){
      e.preventDefault();
      hideAlert();
      const nameVal = nameInput.value.trim();
      const emailVal = emailInput.value.trim();
      if(!nameVal || !emailVal) return showAlert('Name and Email are required.');
      try {
  // Try admin-specific endpoint first
  const res = await fetch('admin_update_profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
          body: JSON.stringify({ name: nameVal, email: emailVal })
        });
        let data; const text = await res.text();
        try { data = JSON.parse(text); } catch { data = { success:false, error:text.trim() || 'Unexpected response' }; }
        if(!res.ok) return showAlert(data.error || ('Error ' + res.status));
        if(data.success){
          showAlert('Profile updated successfully.', 'success');
          // Update header data attributes so reopen reflects new values
          const header = document.querySelector('header[data-admin-name]');
          if(header){ header.setAttribute('data-admin-name', data.name); header.setAttribute('data-admin-email', data.email); }
          originalProfile.name = data.name;
          originalProfile.email = data.email;
          exitEditMode(false);
        } else {
          showAlert(data.error || 'Failed to update profile.');
        }
      } catch(err){
        showAlert('Network error while updating profile.');
      }
    });
  }

  if(passwordForm){
    passwordForm.addEventListener('submit', async function(e){
      e.preventDefault();
      hideAlert();
      const cur = document.getElementById('profileCurrentPassword').value.trim();
      const pass = document.getElementById('profileNewPassword').value.trim();
      const conf = document.getElementById('profileConfirmPassword').value.trim();

      if(!cur || !pass || !conf) return showAlert('Please fill in all fields.');
      if(pass.length < 8) return showAlert('New password must be at least 8 characters.');
      if(pass !== conf) return showAlert('New password and confirmation do not match.');
      if(cur === pass) return showAlert('New password must be different from current password.');

      try {
  const res = await fetch('admin_change_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
          body: JSON.stringify({ current_password: cur, new_password: pass })
        });
        let data; const text = await res.text();
        try { data = JSON.parse(text); } catch { data = { success:false, error:text.trim() || 'Unexpected response' }; }
        if(!res.ok) return showAlert(data.error || ('Error ' + res.status));
        if(data.success){
          showAlert('Password updated successfully.', 'success');
          document.getElementById('profileCurrentPassword').value='';
          document.getElementById('profileNewPassword').value='';
          document.getElementById('profileConfirmPassword').value='';
        } else {
          showAlert(data.error || 'Failed to update password.');
        }
      } catch(err){
        showAlert('Network error. Please try again.');
      }
    });
  }
})();
