// Logic extracted from inline script in admin/completed_applications.php to satisfy CSP.
(function(){
  function throttle(fn, delay){ let t=0; return function(...args){ const now=Date.now(); if(now-t>delay){ t=now; fn.apply(this,args);} }; }
  const backdrop = document.getElementById('dropdown-backdrop');
  if(!backdrop) return; // abort if page structure not present
  const othersBtn = document.getElementById('othersBtn');
  if(othersBtn){
    othersBtn.addEventListener('click', () => {
      const bar = document.getElementById('filtersBar');
      if(!bar) return; bar.style.display = (bar.style.display === 'none' || bar.style.display === '') ? 'flex' : 'none';
    });
  }
  const closeAllMiniMenus = () => { document.querySelectorAll('.ipapp-mini-menu').forEach(m => { m.style.display = 'none'; m.classList.remove('menu-active'); }); backdrop.classList.remove('active'); };
  document.querySelectorAll('.ipapp-mini-btn').forEach(btn => {
    btn.addEventListener('click', function(e){
      e.stopPropagation();
      const targetId = this.getAttribute('data-target');
      const menu = document.getElementById(targetId);
      if(!menu) return;
      const isOpen = menu.style.display === 'block';
      closeAllMiniMenus();
      if(isOpen) return;
      backdrop.classList.add('active');
      menu.style.display = 'block';
      menu.classList.add('menu-active');
    });
  });
  document.addEventListener('click', function(e){
    if(!e.target.closest('.ipapp-mini-dropdown')){
      document.querySelectorAll('.ipapp-mini-menu').forEach(menu => menu.classList.remove('menu-active'));
      backdrop.classList.remove('active');
    }
  });
  document.querySelectorAll('.ipapp-mini-menu .dropdown-item').forEach(item => {
    item.addEventListener('click', function(){
      const menu = this.closest('.ipapp-mini-menu');
      if(!menu) return;
      const btn = document.querySelector('.ipapp-mini-btn[data-target="'+ menu.id +'"]');
      const selectedText = this.textContent.trim();
      if(btn) btn.textContent = selectedText + ' \u25BC';
      closeAllMiniMenus();
    });
  });
  const allTimeBtn = document.getElementById('allTimeBtn');
  const allTimeDropdown = document.getElementById('allTimeDropdownMenu');
  if(allTimeBtn && allTimeDropdown){
    allTimeBtn.addEventListener('click', function(e){
      e.stopPropagation();
      const isOpen = allTimeDropdown.classList.contains('menu-active');
      closeAllMiniMenus();
      if(isOpen){
        allTimeDropdown.classList.remove('menu-active');
        allTimeDropdown.style.display = 'none';
        return;
      }
      backdrop.classList.add('active');
      const rect = this.getBoundingClientRect();
      allTimeDropdown.style.left = rect.left + 'px';
      allTimeDropdown.style.top = (rect.bottom + window.scrollY) + 'px';
      allTimeDropdown.style.display = 'block';
      allTimeDropdown.classList.add('menu-active');
    });
    allTimeDropdown.addEventListener('click', e => e.stopPropagation());
    allTimeDropdown.querySelectorAll('.dropdown-item').forEach(btn => {
      btn.addEventListener('click', function(){
        const range = this.getAttribute('data-range');
        closeAllMiniMenus();
        const calendarSection = document.getElementById('calendarSection');
        if(calendarSection) calendarSection.style.display = (range === 'custom') ? 'block' : 'none';
      });
    });
  }
  backdrop.addEventListener('click', function(){
    if(allTimeDropdown){
      allTimeDropdown.classList.remove('menu-active');
      allTimeDropdown.style.display = 'none';
    }
    closeAllMiniMenus();
  });
  document.body.addEventListener('click', function(){
    if(allTimeDropdown){
      allTimeDropdown.classList.remove('menu-active');
      allTimeDropdown.style.display = 'none';
    }
    closeAllMiniMenus();
  });
  document.querySelectorAll('.ipapp-mini-menu').forEach(m => m.addEventListener('click', e => e.stopPropagation()));

  // Delegated click for description (details modal)
  document.addEventListener('click', function(e){
    const link = e.target.closest('.ipapp-desc-link');
    if(!link) return;
    e.preventDefault();
    const payload = link.getAttribute('data-details') || '';
    let details = null;
    try { details = payload ? JSON.parse(atob(payload)) : null; } catch(err){ details = null; }
    const v = (x,d='') => (x===undefined||x===null||x==='')?d:x;
    const s = details?.student || {}; const dDoc = details?.document || {}; const files = Array.isArray(details?.files)?details.files:[];
    const filesHTML = files.map(f => {
      const url = f.url || ''; const safeHref = url || '#';
      const disabled = url ? '' : 'aria-disabled="true" tabindex="-1"';
      const dl = url ? 'download' : disabled; const view = url ? 'target="_blank" rel="noopener"' : disabled;
      return `<div class="g-file-row"><span>${f.label}</span><div class="g-chip-group"><a class="g-chip" href="${safeHref}" ${dl}>Download File</a><a class="g-chip g-chip-view" href="${safeHref}" ${view}>View File</a></div></div>`;
    }).join('');
    const html = `<div class="g-section"><h4 class="g-section-title">Student Information</h4>
      <div class="g-line"><strong>Name:</strong> ${v(s.name)}</div>
      <div class="g-line"><strong>Student Number:</strong> ${v(s.number)}</div>
      <div class="g-line"><strong>Email Address:</strong> ${s.email ? `<a href=\"mailto:${s.email}\">${s.email}</a>` : ''}</div>
      <div class="g-line"><strong>Home Address:</strong> ${v(s.homeAddress)}</div>
      <div class="g-line"><strong>Campus:</strong> ${v(s.campus)}</div>
      <div class="g-line"><strong>Department:</strong> ${v(s.department)}</div>
      <div class="g-line"><strong>College:</strong> ${v(s.college)}</div>
      <div class="g-line"><strong>Program:</strong> ${v(s.program)}</div>
    </div><div class="g-section"><h4 class="g-section-title">Document Information</h4>
      <div class="g-line"><strong>Title:</strong> ${v(dDoc.title)}</div>
      <div class="g-line"><strong>Author/s Full name/s:</strong> ${v(dDoc.author)}</div>
      <div class="g-line"><strong>Date Accomplished for the Work:</strong> ${v(dDoc.dateAccomplished)}</div>
      <div class="g-line"><strong>Application Date Accomplished:</strong> ${v(dDoc.applicationDate)}</div>
    </div><div class="g-files">${filesHTML}</div>`;
    const body = document.getElementById('gmodalBody');
    if(body){ body.innerHTML = html; }
    const modalEl = document.getElementById('detailsGModal');
    if(window.bootstrap && modalEl){ new bootstrap.Modal(modalEl).show(); }
  });

  // View certificate button
  document.addEventListener('click', function(e){
    const btn = e.target.closest('#gViewCertBtn');
    if(!btn) return; e.preventDefault();
    const detailsInst = window.bootstrap ? bootstrap.Modal.getInstance(document.getElementById('detailsGModal')) : null;
    if(detailsInst) detailsInst.hide();
    const cert = document.getElementById('certificateModalCA');
    if(window.bootstrap && cert){ new bootstrap.Modal(cert).show(); }
  });

  // Search filter logic
  function applyFilter(){
    const searchEl = document.getElementById('ipappSearch');
    if(!searchEl) return;
    const q = (searchEl.value || '').trim().toLowerCase();
    const items = document.querySelectorAll('#ipappList .ipapp-list-item');
    if(!q){ items.forEach(it=>it.style.display=''); return; }
    items.forEach(it => {
      const hay = (it.dataset.description + ' ' + it.dataset.name + ' ' + it.dataset.date).toLowerCase();
      it.style.display = hay.includes(q) ? '' : 'none';
    });
  }
  const searchInput = document.getElementById('ipappSearch');
  if(searchInput){ searchInput.addEventListener('input', throttle(applyFilter, 150)); }
  const searchBtn = document.getElementById('ipappSearchBtn');
  if(searchBtn){ searchBtn.addEventListener('click', applyFilter); }
})();
