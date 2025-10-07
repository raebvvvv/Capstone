// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const repasswordField = document.getElementById('repassword');
    const passwordField = document.getElementById('password');
    const passwordErrors = document.getElementById('passwordErrors');
    if (repasswordField) {
        repasswordField.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Auto-capitalize name fields as user types
    function capitalizeInput(element) {
        // Only apply to text-like inputs, never to <select> elements
        if (element && element.tagName === 'SELECT') return;
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
    // Exclude 'department' (it's a <select>) from auto-capitalization
    const nameFields = ['lastName', 'firstName', 'middleName', 'suffix'];
    nameFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            capitalizeInput(field);
        }
    });
    
    // Password visibility toggle functionality
    function setupPasswordToggle(toggleButtonId, passwordFieldId, eyeIconId) {
        const toggleButton = document.getElementById(toggleButtonId);
        const passwordField = document.getElementById(passwordFieldId);
        const eyeIcon = document.getElementById(eyeIconId);
        
        if (toggleButton && passwordField) {
            toggleButton.addEventListener('click', function() {
                const isPassword = passwordField.type === 'password';
                
                // Toggle password field type
                passwordField.type = isPassword ? 'text' : 'password';
                
                // Update button aria-label
                toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                
                // Update eye icon (if it exists)
                if (eyeIcon) {
                    if (isPassword) {
                        // Show "eye-off" icon (password is visible)
                        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
                    } else {
                        // Show normal "eye" icon (password is hidden)
                        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.477 7-10 7S1 15.866 1 12 5.477 5 11 5s10 3.134 10 7z"></path>';
                    }
                }
            });
        }
    }
    
    // Set up password toggles
    setupPasswordToggle('togglePassword', 'password', 'eyeIcon');
    setupPasswordToggle('toggleRepassword', 'repassword', 'eyeIconRepassword');

    // Dynamic password requirement feedback (mirror server-side rules)
    function getPasswordIssues(pwd) {
        const issues = [];
        if (pwd.length < 12) issues.push('Must be at least 12 characters long.');
        if (!/[a-z]/.test(pwd)) issues.push('Must contain at least one lowercase letter.');
        if (!/[A-Z]/.test(pwd)) issues.push('Must contain at least one uppercase letter.');
        if (!/\d/.test(pwd)) issues.push('Must contain at least one number.');
        if (!/[^a-zA-Z\d]/.test(pwd)) issues.push('Must contain at least one special character.');
        return issues;
    }

    function renderPasswordIssues(issues) {
        if (!passwordErrors) return;
        passwordErrors.innerHTML = '';
        if (!issues.length) {
            passwordErrors.classList.add('d-none');
            return;
        }
        issues.forEach(msg => {
            const li = document.createElement('li');
            li.textContent = msg;
            passwordErrors.appendChild(li);
        });
        passwordErrors.classList.remove('d-none');
    }

    if (passwordField) {
        const maybeRenderPasswordIssues = () => {
            const val = passwordField.value || '';
            if (!val.length) {
                if (passwordErrors) {
                    passwordErrors.classList.add('d-none');
                    passwordErrors.innerHTML = '';
                }
                return;
            }
            renderPasswordIssues(getPasswordIssues(val));
        };

        passwordField.addEventListener('input', maybeRenderPasswordIssues);
        // On load: only show if browser autofilled with a non-empty value
        maybeRenderPasswordIssues();
    }
});