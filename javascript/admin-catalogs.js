(() => {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
  const api = (action, params = {}, method = 'GET') => {
    if (method === 'GET') {
      const qs = new URLSearchParams({ action, ...params }).toString();
      return fetch('./catalogs_api.php?' + qs, { credentials: 'same-origin' }).then(r => r.json());
    }
    const form = new FormData();
    form.set('action', action);
    form.set('csrf_token', csrf);
    Object.entries(params).forEach(([k,v]) => form.set(k, v == null ? '' : v));
    return fetch('./catalogs_api.php', { method: 'POST', body: form, headers: { 'X-CSRF-Token': csrf } }).then(r => r.json());
  };

  const tables = {
    campus: document.querySelector('#tableCampus tbody'),
    level: document.querySelector('#tableLevel tbody'),
    college: document.querySelector('#tableCollege tbody'),
    department: document.querySelector('#tableDepartment tbody'),
    program: document.querySelector('#tableProgram tbody'),
    document: document.querySelector('#tableDocument tbody'),
  };

  const parentMeta = {
    campus: null,
    level: null,
    college: { label: 'Campus', entity: 'campus' },
    department: { label: 'College', entity: 'college' },
    program: { label: 'College', entity: 'college' },
    document: null,
  };

  const modalEl = document.getElementById('entityModal');
  const modal = new bootstrap.Modal(modalEl);
  const form = document.getElementById('entityForm');
  const entityId = document.getElementById('entityId');
  const entityName = document.getElementById('entityName');
  const entityDisplayName = document.getElementById('entityDisplayName');
  const entityCode = document.getElementById('entityCode');
  const entityParentRow = document.getElementById('entityParentRow');
  const entityParent = document.getElementById('entityParent');
  const entityParentLabel = document.getElementById('entityParentLabel');
  const modalTitle = document.getElementById('entityModalLabel');
  const submitBtn = document.getElementById('entitySubmitBtn');

  function rowHtml(rec, entity) {
    const parentName = rec.campus_id_name || rec.college_id_name || '';
    const parentCell = (entity === 'college' || entity === 'department' || entity === 'program') ? `<td>${escapeHtml(parentName || '')}</td>` : '';
    const cols = {
      campus: ['name','code'],
      level: ['name','code'],
      college: ['name','code'],
      department: ['name','code'],
      program: ['name','code'],
      document: ['name','code','role']
    }[entity];
    const cells = cols.map(c => `<td>${escapeHtml(rec[c] || '')}</td>`).join('');
    return `<tr data-id="${rec.id}">
      ${cells}
      ${parentCell}
      <td>
        <button class="btn btn-sm btn-outline-secondary me-1" data-action="edit" data-entity="${entity}">Edit</button>
        <button class="btn btn-sm btn-outline-danger" data-action="delete" data-entity="${entity}">Delete</button>
      </td>
    </tr>`;
  }

  function escapeHtml(str){
    return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s]));
  }

  async function loadList(entity) {
    const tbody = tables[entity];
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="4">Loading...</td></tr>';
    const res = await api('list', { entity });
    if (!res.ok) { tbody.innerHTML = `<tr><td colspan="4" class="text-danger">${escapeHtml(res.error || 'Failed to load')}</td></tr>`; return; }
    if (!Array.isArray(res.data) || res.data.length === 0) { tbody.innerHTML = '<tr><td colspan="4" class="text-muted">No records</td></tr>'; return; }
    // For entities with parent, fetch parent names map
    let parentMap = {};
    if (parentMeta[entity]) {
      const resParents = await api('parents', { entity });
      if (resParents.ok) {
        resParents.data.forEach(p => { parentMap[p.id] = p.name + (p.code ? ` (${p.code})` : ''); });
      }
    }
    tbody.innerHTML = res.data.map(r => {
      if (entity === 'college') r.campus_id_name = parentMap[r.campus_id] || '';
      if (entity === 'department') r.college_id_name = parentMap[r.college_id] || '';
      if (entity === 'program') r.college_id_name = parentMap[r.college_id] || '';
      return rowHtml(r, entity);
    }).join('');
  }

  // Load all tabs initially
  ['campus','level','college','department','program','document'].forEach(loadList);

  // Add buttons
  document.getElementById('btnAddCampus').addEventListener('click', () => openModal('campus'));
  document.getElementById('btnAddLevel').addEventListener('click', () => openModal('level'));
  document.getElementById('btnAddCollege').addEventListener('click', () => openModal('college'));
  document.getElementById('btnAddDepartment').addEventListener('click', () => openModal('department'));
  document.getElementById('btnAddProgram').addEventListener('click', () => openModal('program'));
  document.getElementById('btnAddDocument').addEventListener('click', () => openModal('document'));

  async function openModal(entity, rec = null) {
    entityId.value = rec ? rec.id : '';
    entityName.value = entity;
    entityDisplayName.value = rec ? (rec.name || '') : '';
    entityCode.value = rec ? (rec.code || '') : '';
    const pm = parentMeta[entity];
    if (pm) {
      entityParentRow.classList.remove('d-none');
      entityParentLabel.textContent = pm.label;
      entityParent.innerHTML = '';
      const res = await api('parents', { entity });
      if (res.ok) {
        res.data.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name + (p.code ? ` (${p.code})` : '');
          entityParent.appendChild(opt);
        });
      }
      if (rec) {
        if (entity === 'college') entityParent.value = rec.campus_id || '';
        if (entity === 'department' || entity === 'program') entityParent.value = rec.college_id || '';
      } else {
        entityParent.selectedIndex = 0;
      }
    } else {
      entityParentRow.classList.add('d-none');
      entityParent.innerHTML = '';
    }
    // Role selector for documents
    let roleSelect = modalEl.querySelector('#entityRole');
    if (entity === 'document') {
      if (!roleSelect) {
        const roleGroup = document.createElement('div');
        roleGroup.className = 'mb-3';
        roleGroup.innerHTML = `
          <label class="form-label">Role</label>
          <select class="form-select" name="role" id="entityRole">
            <option value="student">Student</option>
            <option value="employee">Employee</option>
            <option value="both">Both</option>
          </select>`;
        entityParentRow.parentElement.insertBefore(roleGroup, entityParentRow);
        roleSelect = roleGroup.querySelector('#entityRole');
      }
      roleSelect.closest('.mb-3').classList.remove('d-none');
      roleSelect.value = rec ? (rec.role || 'both') : 'both';
    } else if (roleSelect) {
      roleSelect.closest('.mb-3').classList.add('d-none');
    }
    modalTitle.textContent = (rec ? 'Edit ' : 'Add ') + entity.charAt(0).toUpperCase() + entity.slice(1);
    submitBtn.textContent = rec ? 'Update' : 'Create';
    modal.show();
  }

  // Row actions (edit/delete)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    const entity = btn.getAttribute('data-entity');
    const tr = btn.closest('tr');
    const id = parseInt(tr?.getAttribute('data-id'), 10) || 0;
    if (btn.getAttribute('data-action') === 'edit') {
      // fetch list to get record details
      const res = await api('list', { entity });
      if (!res.ok) return alert(res.error || 'Failed');
      const rec = (res.data || []).find(r => parseInt(r.id, 10) === id);
      if (!rec) return alert('Record not found');
      openModal(entity, rec);
    } else if (btn.getAttribute('data-action') === 'delete') {
      if (!confirm('Delete this record?')) return;
      const res = await api('delete', { entity, id }, 'POST');
      if (!res.ok) return alert(res.error || 'Failed');
      loadList(entity);
    }
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const entity = entityName.value;
    const payload = {
      entity,
      id: entityId.value || '',
      name: entityDisplayName.value.trim(),
      code: entityCode.value.trim(),
    };
    const pm = parentMeta[entity];
    if (pm) payload.parent_id = entityParent.value || '';
    if (entity === 'document') {
      const roleEl = modalEl.querySelector('#entityRole');
      if (roleEl) payload.role = roleEl.value;
    }
    const action = payload.id ? 'update' : 'create';
    submitBtn.disabled = true; submitBtn.textContent = 'Saving...';
    try {
      const res = await api(action, payload, 'POST');
      if (!res.ok) return alert(res.error || 'Failed');
      modal.hide();
      loadList(entity);
    } finally {
      submitBtn.disabled = false; submitBtn.textContent = payload.id ? 'Update' : 'Create';
    }
  });
})();
