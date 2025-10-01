// Auto-submit filter form on toggle for Has Notes
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.getElementById('hasNotesSwitch');
    if(!toggle) return;
    // Find the surrounding form
    var form = toggle.closest('form');
    if(!form) return;
    toggle.addEventListener('change', function(){
      try { form.submit(); } catch(_) {}
    });
  });
})();
