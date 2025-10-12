// Renders document upload inputs dynamically based on the public catalogs API
// Contract:
// - Requires window.CATALOGS_API_URL to be defined or defaults to '/catalogs_public_api.php'
// - Expects a container element with id 'dynamicDocuments' inside the Upload Documents fieldset
// - Will create <div class="col-md-4"> blocks with <label> and <input type="file"> per document
// - Uses name attribute equal to a normalized key derived from document name (lower_snake_case)
(function(){
  const container = document.getElementById('dynamicDocuments');
  if (!container) return;

  const role = (window.USER_ROLE || 'student');
  const base = (window.CATALOGS_API_URL 
    || (window.location.pathname.toLowerCase().includes('/capstone/')
        ? '/Capstone/catalogs_public_api.php'
        : '/catalogs_public_api.php'));
  const url = `${base}?entity=document&role=${encodeURIComponent(role)}`;

  function normKey(s){
    s = (s || '').toString().toLowerCase().trim();
    s = s.replace(/[^a-z0-9]+/g,'_');
    s = s.replace(/^_+|_+$/g,'');
    return s || 'document';
  }

  const fallbackDocs = {
    student: [
      'Manuscript (PDF)',
      'Abstract (PDF)',
      'Plagiarism Report (PDF)'
    ],
    employee: [
      'Manuscript (PDF)',
      'Abstract (PDF)',
      'Notarized Co-Authorship (PDF)'
    ],
    both: [
      'Manuscript (PDF)'
    ]
  };

  fetch(url, { credentials: 'same-origin', cache: 'no-store' })
    .then(r => r.ok ? r.json() : Promise.reject(new Error('Failed to fetch documents')))
    .then(resp => {
      let list = (resp && resp.ok && Array.isArray(resp.data)) ? resp.data : [];
      container.innerHTML = '';
      if (!Array.isArray(list) || list.length === 0) {
        // Fallback to a minimal default set so form remains usable
        const names = (fallbackDocs[role] || fallbackDocs.both);
        list = names.map(n => ({ name: n }));
      }
      const frag = document.createDocumentFragment();
      list.forEach(item => {
        const name = item.name || 'Document';
        const key = normKey(name);
        const isOptional = (role === 'employee' && /notarized\s*co-?authorship/i.test(name));

        const col = document.createElement('div');
        col.className = 'col-md-4';

        const label = document.createElement('label');
        label.className = 'form-label';
        label.appendChild(document.createTextNode(name));
        if (isOptional) {
          const badge = document.createElement('span');
          badge.className = 'badge bg-secondary align-middle ms-1';
          badge.textContent = 'Optional';
          label.appendChild(badge);
        }

        const input = document.createElement('input');
        input.type = 'file';
        input.name = key;
        input.className = 'form-control';
        input.setAttribute('accept','application/pdf');
        if (!isOptional) input.setAttribute('required','');

        col.appendChild(label);
        col.appendChild(input);
        frag.appendChild(col);
      });
      container.appendChild(frag);
    })
    .catch(err => {
      console.error('Dynamic documents failed:', err);
      container.innerHTML = '<div class="col-12"><div class="alert alert-warning small mb-0">Unable to load document requirements from server. You can still upload core files like Manuscript and Abstract.</div></div>';
    });
})();
