// Student profile page functionality
document.addEventListener('DOMContentLoaded', function() {
  // Sign out button handler
  const signOutBtn = document.getElementById('signOutBtn');
  if (signOutBtn) {
    signOutBtn.addEventListener('click', function() {
      // Create a form to submit POST request to logout.php
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '../../User/Beforelogin/logout.php';
      
      // Add CSRF token if available
      if (typeof csrfToken !== 'undefined') {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
      }
      
      // Append to body, submit, then remove
      document.body.appendChild(form);
      form.submit();
    });
  }

  // Profile editing functionality
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const form = document.getElementById('profileForm');
  
  if (!form || !editBtn || !saveBtn || !cancelBtn) return;
  
  const inputs = form.querySelectorAll('input:not([id="webmail"])');

  // Enable editing mode
  editBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = false);
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    editBtn.disabled = true; // Disable the button and keep it visible
  });

  // Cancel editing
  cancelBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    // Optionally reset fields to original values here
  });

  // Save profile changes
  saveBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    
    // Here you would normally add AJAX to save the profile data
    // For now, just show a success message
    alert('Profile updated successfully!');
  });
});