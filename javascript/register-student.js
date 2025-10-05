(function(){
  'use strict';
  const form       = document.getElementById('studentRegForm');
  if(!form) return;
  const password   = document.getElementById('password');
  const repassword = document.getElementById('repassword');
  const togglePass = document.getElementById('togglePassword');
  const toggleRepass = document.getElementById('toggleRepassword');
  const pwdErrorsList = document.getElementById('passwordErrors');

  // Show/Hide password
  if(togglePass && password){
    togglePass.addEventListener('click', () => {
      const type = password.type === 'password' ? 'text' : 'password';
      password.type = type;
    });
  }
  if(toggleRepass && repassword){
    toggleRepass.addEventListener('click', () => {
      const type = repassword.type === 'password' ? 'text' : 'password';
      repassword.type = type;
    });
  }

  // Utility: collect password errors
  function getPasswordErrors(value){
    const errs = [];
    if(!value || value.length < 12) errs.push('Must be at least 12 characters long.');
    if(!/[a-z]/.test(value)) errs.push('Must contain at least one lowercase letter.');
    if(!/[A-Z]/.test(value)) errs.push('Must contain at least one uppercase letter.');
    if(!/\d/.test(value)) errs.push('Must contain at least one number.');
    if(!/[^a-zA-Z0-9]/.test(value)) errs.push('Must contain at least one special character.');
    return errs;
  }

  // Live validate password as user types
  if(password){
    password.addEventListener('input', () => {
      if(!pwdErrorsList) return;
      const errs = getPasswordErrors(password.value);
      if(errs.length){
        pwdErrorsList.innerHTML = errs.map(e => `<li>${e}</li>`).join('');
        pwdErrorsList.classList.remove('d-none');
      } else {
        pwdErrorsList.innerHTML = '';
        pwdErrorsList.classList.add('d-none');
      }
    });
  }

  // Auto-capitalize name fields as user types
  function capitalizeInput(element) {
    element.addEventListener('input', function(e) {
      const words = this.value.toLowerCase().split(' ');
      const capitalizedWords = words.map(word => {
        if (word.length > 0) {
          return word.charAt(0).toUpperCase() + word.slice(1);
        }
        return word;
      });
      this.value = capitalizedWords.join(' ');
    });
  }
  
  // Apply auto-capitalization to name fields
  const nameFields = ['lastName', 'firstName', 'middleName', 'suffix'];
  nameFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      capitalizeInput(field);
    }
  });

  // Show validation feedback on submit and prevent refresh if invalid
  form.addEventListener('submit', function (event) {
    let blockSubmit = false;

    // Native validation
    if (!form.checkValidity()) {
      blockSubmit = true;
      document.querySelectorAll('#studentRegForm input').forEach(input => {
        if (!input.checkValidity()) {
          input.classList.add('is-invalid');
        } else {
          input.classList.remove('is-invalid');
        }
      });
    }

    // Password policy check
    if (password) {
      const errs = getPasswordErrors(password.value);
      if (errs.length) {
        blockSubmit = true;
        if(pwdErrorsList){
          pwdErrorsList.innerHTML = errs.map(e => `<li>${e}</li>`).join('');
          pwdErrorsList.classList.remove('d-none');
        }
        password.classList.add('is-invalid');
      } else if(pwdErrorsList){
        pwdErrorsList.innerHTML = '';
        pwdErrorsList.classList.add('d-none');
        password.classList.remove('is-invalid');
      }
    }

    // Password match check
    if (password && repassword && password.value !== repassword.value) {
      blockSubmit = true;
      repassword.setCustomValidity('Passwords do not match');
      repassword.classList.add('is-invalid');
    } else if (repassword) {
      repassword.setCustomValidity('');
      repassword.classList.remove('is-invalid');
    }

    form.classList.add('was-validated');
    if(blockSubmit){
      event.preventDefault();
      event.stopPropagation();
    }
  });

  function capitalizeWords(str) {
    return str
      .toLowerCase()
      .replace(/\b\w/g, c => c.toUpperCase());
  }

  const firstNameEl = document.getElementById('firstName');
  const lastNameEl = document.getElementById('lastName');
  const middleNameEl = document.getElementById('middleName');

  if(firstNameEl){ firstNameEl.addEventListener('blur', function(){ this.value = capitalizeWords(this.value || ''); }); }
  if(lastNameEl){ lastNameEl.addEventListener('blur', function(){ this.value = capitalizeWords(this.value || ''); }); }
  if(middleNameEl){ middleNameEl.addEventListener('blur', function(){ this.value = capitalizeWords(this.value || ''); }); }
})();
