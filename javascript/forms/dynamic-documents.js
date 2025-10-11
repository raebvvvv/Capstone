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
  const base = (window.CATALOGS_API_URL || '/catalogs_public_api.php');
  const url = `${base}?entity=document&role=${encodeURIComponent(role)}`;

  function normKey(s){
    s = (s || '').toString().toLowerCase().trim();
    s = s.replace(/[^a-z0-9]+/g,'_');
    s = s.replace(/^_+|_+$/g,'');
    return s || 'document';
  }

  fetch(url, { credentials: 'same-origin' })
    .then(r => r.ok ? r.json() : Promise.reject(new Error('Failed to fetch documents')))
    .then(resp => {
      const list = (resp && resp.ok && Array.isArray(resp.data)) ? resp.data : [];
      container.innerHTML = '';
      if (!Array.isArray(list) || list.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info small mb-0">No document requirements are configured yet. Please contact the administrator.</div></div>';
        return;
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
    });
})();
