// Profile page functionality
document.addEventListener('DOMContentLoaded', function() {
  // Sign out button
  const signOutBtn = document.getElementById('signOutBtn');
  if (signOutBtn) {
    signOutBtn.addEventListener('click', function() {
      // Redirect to the login page through proper logout
      window.location.href = '../../logout.php';
    });
  }

  // Profile editing functionality
  const editBtn = document.getElementById('editProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const form = document.getElementById('profileForm');
  
  if (!editBtn || !saveBtn || !cancelBtn || !form) return;
  
  const inputs = form.querySelectorAll('input:not([id="webmail"])');

  editBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = false);
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    editBtn.disabled = true; // Disable the button and keep it visible
  });

  cancelBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    // Optionally reset fields to original values here
  });

  saveBtn.addEventListener('click', function() {
    inputs.forEach(input => input.disabled = true);
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.disabled = false; // Enable the button again
    // Optionally add save logic here
    
    // Show success message (optional)
    alert('Profile updated successfully!');
  });
});