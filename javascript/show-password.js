// CSP-safe show/hide password toggle for login and other forms
// Works with a button having class .toggle-password and data-target="#inputId"
(function(){
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.toggle-password');
    if (!btn) return;
    const sel = btn.getAttribute('data-target');
    if (!sel) return;
    const input = document.querySelector(sel);
    if (!input) return;
    const isHidden = input.getAttribute('type') === 'password';
    input.setAttribute('type', isHidden ? 'text' : 'password');
    // swap icons if present (supports Font Awesome or inline SVG)
    const faIcon = btn.querySelector('i.fa');
    if (faIcon) {
      faIcon.classList.toggle('fa-eye');
      faIcon.classList.toggle('fa-eye-slash');
    }
    const svgEye = btn.querySelector('svg.icon-eye');
    const svgEyeOff = btn.querySelector('svg.icon-eye-off');
    if (svgEye && svgEyeOff) {
      if (isHidden) { // now visible
        svgEye.classList.add('d-none');
        svgEyeOff.classList.remove('d-none');
      } else { // now hidden
        svgEye.classList.remove('d-none');
        svgEyeOff.classList.add('d-none');
      }
    }
    // update accessible label
    btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    // keep focus on the input for better UX
    try { input.focus({ preventScroll: true }); } catch(_) { input.focus(); }
  });
})();
