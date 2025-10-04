// Auto-submit filter form on toggle for Has Notes
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    try {
      var paramsDbg = new URLSearchParams(window.location.search || '');
      if (paramsDbg.get('debug') === '1') {
        var dbg = {
          search: paramsDbg.get('search') || '',
          tab: paramsDbg.get('tab') || '',
          has_notes: paramsDbg.get('has_notes') || '',
        };
        // Count rows per tab to understand visibility
        function countRows(id){
          var tbody = document.querySelector('#'+id+' tbody');
          if (!tbody) return 0;
          var rows = Array.from(tbody.querySelectorAll('tr'));
          var count = rows.filter(function(tr){
            return !/No\s+\w+\s+requests\./i.test((tr.textContent||'').trim());
          }).length;
          return count;
        }
        console.group('[Admin Search Debug]');
        console.log('GET params', dbg);
        console.log('Counts', {
          pending: countRows('pending'),
          approved: countRows('approved'),
          completed: countRows('completed')
        });
        var activeLink = document.querySelector('#requestTabs .nav-link.active');
        console.log('Active tab (client)', activeLink ? activeLink.textContent.trim() : '(none)');
        console.groupEnd();
      }
    } catch(_) {}
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

    // Ensure searches land on Pending by default (server will render Pending active),
    // so users see the most likely matches first.
    var searchInput = form ? form.querySelector('input[name="search"]') : null;
    if (form && searchInput) {
      form.addEventListener('submit', function(e){
        var hidden = document.getElementById('activeTabInput');
        if (hidden && (searchInput.value || '').trim() !== '') {
          hidden.value = 'pending';
        }
        // Build URL explicitly and navigate (prevents odd browser behaviors)
        try {
          e.preventDefault();
          var base = (form.getAttribute('action') || window.location.pathname);
          var params = new URLSearchParams(window.location.search || '');
          params.set('search', searchInput.value.trim());
          params.set('tab', 'pending');
          if (toggle && toggle.checked) { params.set('has_notes','1'); } else { params.delete('has_notes'); }
          // Preserve debug flag if present
          var isDebug = new URLSearchParams(window.location.search || '').get('debug');
          if (isDebug === '1') { params.set('debug','1'); }
          window.location.assign(base + '?' + params.toString());
        } catch(_) { /* fall back to normal submit */ form.submit(); }
      });
    }

    // After a search, if the current tab has no results, switch to the first tab that has any rows
    try {
      var params = new URLSearchParams(window.location.search || '');
      var hasSearch = params.has('search') && (params.get('search') || '').trim() !== '';
      if (hasSearch) {
        var order = ['pending','approved','completed'];
        function hasRows(id){
          var tbody = document.querySelector('#'+id+' tbody');
          if (!tbody) return false;
          var rows = Array.from(tbody.querySelectorAll('tr'));
          if (rows.length === 0) return false;
          // Treat a single "No ... requests." row as empty
          return rows.some(function(tr){ return !/No\s+\w+\s+requests\./i.test((tr.textContent||'').trim()); });
        }
        var activeLink = document.querySelector('#requestTabs .nav-link.active');
        var activeId = activeLink ? (activeLink.getAttribute('href')||'').replace('#','') : 'pending';
        if (!hasRows(activeId)) {
          var next = order.find(function(id){ return hasRows(id); });
          if (next && next !== activeId) {
            var link = document.querySelector('#requestTabs a[href="#'+next+'"]');
            if (link) {
              try { if (window.bootstrap && bootstrap.Tab) { new bootstrap.Tab(link).show(); } else { link.click(); } } catch(_) {}
              var hidden = document.getElementById('activeTabInput'); if (hidden) hidden.value = next;
            }
          }
        }
      }
    } catch(_) {}
  });
})();
