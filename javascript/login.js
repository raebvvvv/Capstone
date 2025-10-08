// login.js - handles admin login page JS without inline scripts (CSP compliant)
(function(){
  'use strict';
  // Inject CSRF token into window for potential future AJAX (if needed)
  try {
    var meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
      window.__CSRF_TOKEN__ = meta.getAttribute('content') || ''; 
    }
  } catch(e) { /* ignore */ }

  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  ready(function(){
    var btn = document.querySelector('.toggle-password');
    if(btn){
      btn.addEventListener('click', function(){
        var targetSel = btn.getAttribute('data-target');
        var input = document.querySelector(targetSel);
        if(!input) return;
        var isPw = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPw ? 'text' : 'password');
        var eye = btn.querySelector('.icon-eye');
        var eyeOff = btn.querySelector('.icon-eye-off');
        if(eye) eye.classList.toggle('d-none', !isPw);
        if(eyeOff) eyeOff.classList.toggle('d-none', isPw);
      });
    }
  });
})();
