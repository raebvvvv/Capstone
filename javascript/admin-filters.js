// Auto-submit filter form on toggle for Has Notes
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.getElementById('hasNotesSwitch');
    var form = toggle ? toggle.closest('form') : null;
    var activeTabInput = document.getElementById('activeTabInput');
    var tabs = document.querySelectorAll('#requestTabs a[data-bs-toggle="tab"]');

    // Keep hidden input in sync when user clicks tabs
    if (tabs && activeTabInput) {
      tabs.forEach(function(tab){
        tab.addEventListener('shown.bs.tab', function(e){
          var href = e.target.getAttribute('href') || '';
          var id = href.startsWith('#') ? href.substring(1) : href;
          if (id === 'pending' || id === 'approved' || id === 'completed') {
            activeTabInput.value = id;
          }
        });
      });
    }

    // Auto-submit filter form on toggle; ensure tab field is present
    if (toggle && form) {
      toggle.addEventListener('change', function(){
        try { form.submit(); } catch(_) {}
      });
    }
  });
})();
