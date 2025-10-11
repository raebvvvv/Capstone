// Extracted from admin/ticket.php inline script for CSP compliance
// All logic wrapped to avoid global leakage except needed functions attached intentionally.

(function(){
  'use strict';

  // CSP-safe Modal API wrapper: uses Bootstrap when available; otherwise falls back to minimal show/hide
  const ModalApi = (function(){
    function cleanupArtifacts(){
      document.querySelectorAll('.modal-backdrop').forEach(el=>el.remove());
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('overflow');
      document.body.style.removeProperty('padding-right');
    }
    function makeBackdrop(){
      const bd = document.createElement('div');
      bd.className = 'modal-backdrop fade show';
      document.body.appendChild(bd);
      return bd;
    }
    function show(el){
      try {
        if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
          new bootstrap.Modal(el).show();
          return;
        }
      } catch(_) {}
      // Fallback in strict CSP
      el.style.display = 'block';
      el.removeAttribute('aria-hidden');
      el.setAttribute('aria-modal','true');
      el.classList.add('show');
      makeBackdrop();
      document.body.classList.add('modal-open');
      el.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn=>{
        btn.addEventListener('click', ()=> hide(el), { once:true });
      });
    }
    function hide(el){
      try {
        if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
          const inst = bootstrap.Modal.getInstance(el);
          if (inst) { inst.hide(); return; }
        }
      } catch(_) {}
      el.classList.remove('show');
      el.style.display = 'none';
      el.setAttribute('aria-hidden','true');
      cleanupArtifacts();
    }
    return { show, hide };
  })();

  // Utility escape
  function escapeHTML(str){ return String(str).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];}); }
  window.escapeHTML = escapeHTML; // if needed elsewhere

  let currentDetails = {}; let currentAuthorIndex = null; let pendingApproveRow = null;

  // Dynamically add a Comments button to a row (used when a Pending row becomes Incomplete / Awaiting Review)
  function ensureCommentsButton(row){
    if(!row) return;
    const actionDiv = row.querySelector('.action-btn-group');
    if(!actionDiv) return;
    if(actionDiv.querySelector('.btn-comments')) return; // Already present
    const viewBtn = actionDiv.querySelector('.btn-view');
    const commentsBtn = document.createElement('a');
    commentsBtn.href = '#';
    commentsBtn.className = 'btn btn-comments btn-sm rounded-pill px-3';
    commentsBtn.textContent = 'Comments';
    if(viewBtn && viewBtn.nextSibling){
      actionDiv.insertBefore(commentsBtn, viewBtn.nextSibling);
    } else if(viewBtn){
      actionDiv.appendChild(commentsBtn);
    } else {
      actionDiv.insertBefore(commentsBtn, actionDiv.firstChild);
    }
  }

  function renderDetails(details, editMode){
    // Force read-only view: ignore edit mode
    editMode = false;
    const v = (x,d='') => (x===undefined||x===null?d:x);
    // File attachments (prefer detailed list if provided)
    const detailedList = Array.isArray(details.files_list) ? details.files_list : [];
    const legacyMap = details.files || {};
    function formatSize(bytes){ if(!bytes && bytes!==0) return ''; const units=['B','KB','MB','GB']; let i=0; let v=bytes; while(v>=1024 && i<units.length-1){ v/=1024; i++; } return v.toFixed(v>=10||i===0?0:1)+' '+units[i]; }
    const rows = (detailedList.length ? detailedList : Object.keys(legacyMap).map(k=>({ label: k.replace(/[-_]/g,' ').replace(/\b\w/g,c=>c.toUpperCase()), url: legacyMap[k], type:k })))
      .map(file=>{
        const hasUrl = !!file.url;
        const safeUrl = hasUrl ? file.url : '#';
        const disabledAttrs = hasUrl ? '' : 'class="disabled" aria-disabled="true" tabindex="-1"';
        const sizePart = file.size ? `<span class="text-muted ms-2 small">${formatSize(file.size)}</span>` : '';
  // Show a badge only when verified; omit badge entirely if unverified
  const verifiedBadge = (file.verified===1 || file.verified===true) ? `<span class="badge bg-success ms-2">Verified</span>` : '';
        const label = escapeHTML(file.label || file.type || 'File');
        return `<li class="d-flex justify-content-between align-items-center mb-2 flex-wrap"><div><span>${label}</span>${sizePart}${verifiedBadge}</div><div class="d-flex gap-2 mt-2 mt-sm-0"><a class="btn btn-download btn-sm" href="${safeUrl}" ${hasUrl? 'download': disabledAttrs}>Download File</a><a class="btn btn-view-file btn-sm" href="${safeUrl}" target="_blank" rel="noopener" ${disabledAttrs}>View File</a></div></li>`;
      }).join('');
    const attachmentsHTML = `<ul class="list-unstyled mb-0">${rows || '<li class="text-muted fst-italic">No files uploaded.</li>'}</ul>`;

    if(!Array.isArray(details.additionalAuthors)){
      const raw=(details.authorName||'').trim();
      const names = raw ? raw.split(/\s*,\s*|\s*;\s*|\s*\n\s*/).filter(Boolean):[];
      const primary = details.studentName || names.shift() || '';
      details.studentName = primary;
      details.additionalAuthors = names.filter(n=>n && n!==primary).map(n=>({ name:n, studentNumber:'', email:'', address:'', phone:'', campus:'', department:'', college:'', program:'' }));
    }
    if(!details.additionalAuthors) details.additionalAuthors=[];

    const allAuthors = Array.isArray(details.additionalAuthors) ? details.additionalAuthors : [];
    const isAdv = (a)=> (a && (a.is_adviser===1 || a.is_adviser===true || a.is_adviser==='1'));
    const advisersInAuthors = allAuthors.filter(isAdv);
    const coauthors = allAuthors.slice(); // keep all for listing; badge advisers inline

    const coauthorsListHTML = coauthors.map((a)=>{
      const badge = isAdv(a) ? '<span class="badge bg-warning text-dark ms-2">Adviser</span>' : '';
      const btn = isAdv(a) ? '' : `<button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2 author-view-btn" data-author-index="${allAuthors.indexOf(a)}">View Details</button>`;
      return `<div class="author-entry"><span class="author-name">${a.name||'—'}</span>${badge}${btn}</div>`;
    }).join('');
    // If adviser is not a co-author, show a concise line under Document Information
    const adviserName = (details.adviser || '').trim();
    const adviserInlineHTML = (!advisersInAuthors.length && adviserName)
      ? `<p><strong>Adviser:</strong> ${escapeHTML(adviserName)}</p>`
      : '';
    const modalBody = document.getElementById('detailsModalBody');
    if(!modalBody) return;

    // Determine user type from Request ID prefix (ERID = Employee, SRID = Student)
    const rid = String(v(details.request_id, ''));
    const isEmployee = /^ERID-/i.test(rid);
  const infoHeader = isEmployee ? 'Employee Information' : 'Student Information';
  const idLabel = isEmployee ? 'Employee ID/Number' : 'Student Number';
  // Resolve department from possible keys (server may return different naming)
  const deptVal = (details.department || details.employee_department || '').toString().trim();

  modalBody.innerHTML = `<div><h5>${infoHeader}</h5>`+
      `<p><strong>Name:</strong> ${editMode? `<input type='text' id='editStudentName' value='${escapeHTML(v(details.studentName,''))}' />` : escapeHTML(v(details.studentName,'—'))}</p>`+
      `<p><strong>${idLabel}:</strong> ${editMode? `<input type='text' id='editStudentNumber' value='${escapeHTML(v(details.studentNumber,''))}' />` : escapeHTML(v(details.studentNumber,'—'))}</p>`+
      `<p><strong>Email Address:</strong> ${editMode? `<input type='email' id='editEmail' value='${escapeHTML(v(details.email,''))}' />` : escapeHTML(v(details.email,'—'))}</p>`+
      `<p><strong>Home Address:</strong> ${editMode? `<input type='text' id='editHomeAddress' value='${escapeHTML(v(details.homeAddress,''))}' />` : escapeHTML(v(details.homeAddress,'—'))}</p>`+
      `<p><strong>Campus:</strong> ${editMode? `<input type='text' id='editCampus' value='${escapeHTML(v(details.campus,''))}' />` : escapeHTML(v(details.campus,'—'))}</p>`+
  `<p><strong>College:</strong> ${editMode? `<input type='text' id='editCollege' value='${escapeHTML(v(details.college,''))}' />` : escapeHTML(v(details.college,'—'))}</p>`+
  `${isEmployee && deptVal ? `<p><strong>Department:</strong> ${escapeHTML(deptVal)}</p>` : ''}`+
      `<p><strong>Program:</strong> ${editMode? `<input type='text' id='editProgram' value='${escapeHTML(v(details.program,''))}' />` : escapeHTML(v(details.program,'—'))}</p></div>`+
  (function(){
    // Compute academic level with client-side fallback across possible keys
    var level = details.academicLevel || details.academic_level || details.level || '';
    if (!level || /^employee$/i.test(level)) {
      // Try to infer lightly from program if still missing
      var prog = (details.program||'').toLowerCase();
      if (!prog || /not\s*stud/i.test(prog) || prog==='n/a' || prog==='na') level = 'Not Studying';
      else if (prog.indexOf('open') !== -1) level = 'Open University';
      else if (/doctor|doctoral|doctorate|phd|ph\.d|master|postgrad/i.test(prog)) level = 'Graduate School';
      else level = 'Undergraduate';
    }
    return `<p><strong>Academic Level:</strong> ${escapeHTML(level || '—')}</p>`;
  })()+
  `<div><h5>Document Information</h5>`+
      `<p><strong>Title:</strong> ${editMode? `<input type='text' id='editDocumentTitle' value='${escapeHTML(v(details.documentTitle,''))}' />` : escapeHTML(v(details.documentTitle,'—'))}</p>`+
  `<p><strong>Type (Work Classification):</strong> ${escapeHTML(v(details.workClassification,'—'))}</p>`+
  `<p><strong>Author/s Full name/s:</strong> ${escapeHTML(v(details.studentName,'—'))}</p>`+
  adviserInlineHTML+
  `${coauthorsListHTML ? `<div class="mt-2"><div class="fw-semibold mb-1">Additional Author(s)</div>${coauthorsListHTML}</div>`: ''}`+
      `<p class="mt-2"><strong>Date Accomplished:</strong> ${editMode
        ? `<input type='date' id='editAccomplishmentDate' value='${escapeHTML(v(details.accomplishmentDate,''))}' />`
        : (function(x){ try{ if(!x) return '—'; const d=new Date(String(x).trim()); return isNaN(d)? escapeHTML(x): d.toLocaleDateString('en-PH',{month:'long', day:'2-digit', year:'numeric'});}catch(_){ return escapeHTML(x||'—'); } })(v(details.accomplishmentDate,''))
      }</p></div>`+
      `<div class="mt-3"><h5>Uploaded Files</h5>${attachmentsHTML}</div>`;

    // No edit/save controls in read-only mode
    document.querySelectorAll('.author-view-btn').forEach(btn=>{
      btn.addEventListener('click',()=>{ const idx=parseInt(btn.getAttribute('data-author-index'),10); openAuthorModal(idx); });
    });
  }
  function showDetailsModal(row){
    const tr = row && row.tagName==='TR' ? row : (row.closest && row.closest('tr'));
    if(!tr) return;
    const nameEl = tr.querySelector('td:nth-child(2) .fw-semibold');
    const numEl = tr.querySelector('td:nth-child(2) .student-subtext');
    const programText = (tr.querySelector('.col-program')?.textContent || '').trim();
    const reqDateText = (tr.querySelector('td:nth-child(5)')?.textContent || '').trim();
    const details = {
      studentName: nameEl ? nameEl.textContent.trim() : '',
      studentNumber: numEl ? numEl.textContent.trim() : '',
      email:'', homeAddress:'', campus:'', department:'', college:'',
      program: programText,
      documentTitle:'', authorName:'', accomplishmentDate: reqDateText
    };
    // Populate request_id from the table (first column) for ERID/SRID detection in fallback
    try {
      const ridCell = tr.querySelector('td:nth-child(1)');
      if (ridCell) {
        details.request_id = (ridCell.textContent || '').trim();
      }
    } catch(_) {
      /* noop */
    }
    currentDetails = details;
    renderDetails(details,false);
    const detailsModalEl = document.getElementById('detailsModal');
    if(detailsModalEl) ModalApi.show(detailsModalEl);
  }
  function renderAuthorModal(author, edit){
    const body = document.getElementById('authorInfoBody');
    if(!body) return;
    const v=(x)=> (x??'');
    if(!edit){
      const isAdv = (author && (author.is_adviser===1 || author.is_adviser===true || author.is_adviser==='1'));
      // Determine if this is an employee context based on the current submission's request ID
      const rid = String(currentDetails.request_id || '');
      const isEmployee = /^ERID-/i.test(rid);
      const idLabel = isEmployee ? 'Employee ID/Number' : 'Student Number';
      
      body.innerHTML = `<div class="mb-3"><strong>Name:</strong> ${author.name || '—'} ${isAdv? '<span class="badge bg-warning text-dark ms-2">Adviser</span>':''}</div>`+
        `<div class="mb-2"><strong>Role:</strong> ${isAdv ? 'Adviser' : 'Co-Author'}</div>`+
        `<div class="mb-2"><strong>${idLabel}:</strong> ${author.studentNumber || ''}</div>`+
        `<div class="mb-2"><strong>Email Address:</strong> ${author.email || ''}</div>`+
        `<div class="mb-2"><strong>Home Address:</strong> ${author.address || ''}</div>`+
        `<div class="mb-2"><strong>Phone Number:</strong> ${author.phone || ''}</div>`;
    } else {
      // Split name for editing convenience; attempt First [Middle ...] Last
      const splitName = (n)=>{
        const parts = String(n||'').trim().split(/\s+/).filter(Boolean);
        if(parts.length<=1) return { first: parts[0]||'', middle:'', last:'' };
        if(parts.length===2) return { first: parts[0], middle:'', last: parts[1] };
        const first = parts.shift();
        const last = parts.pop();
        const middle = parts.join(' ');
        return { first, middle, last };
      };
      const nm = splitName(author.name||'');
      // Determine if this is an employee context based on the current submission's request ID
      const rid = String(currentDetails.request_id || '');
      const isEmployee = /^ERID-/i.test(rid);
      const idLabel = isEmployee ? 'Employee ID/Number' : 'Student Number';
      
      body.innerHTML = `<div class="row g-2">`+
        `<div class="col-12 col-md-4"><label class="form-label">First Name</label><input class="form-control" id="editAuthorFirst" value="${escapeHTML(v(nm.first))}"></div>`+
        `<div class="col-12 col-md-4"><label class="form-label">Middle Name</label><input class="form-control" id="editAuthorMiddle" value="${escapeHTML(v(nm.middle))}"></div>`+
        `<div class="col-12 col-md-4"><label class="form-label">Last Name</label><input class="form-control" id="editAuthorLast" value="${escapeHTML(v(nm.last))}"></div>`+
        `</div>`+
        `<div class="mb-2 mt-2"><label class="form-label">${idLabel}</label><input class="form-control" id="editAuthorStudNoInput" value="${escapeHTML(v(author.studentNumber))}"></div>`+
        `<div class="mb-2"><label class="form-label">Email Address</label><input type="email" class="form-control" id="editAuthorEmailInput" value="${escapeHTML(v(author.email))}"></div>`+
        `<div class="mb-2"><label class="form-label">Home Address</label><input class="form-control" id="editAuthorAddressInput" value="${escapeHTML(v(author.address))}"></div>`+
        `<div class="mb-2"><label class="form-label">Phone Number</label><input class="form-control" id="editAuthorPhoneInput" value="${escapeHTML(v(author.phone))}"></div>`;
    }
    // Author modal is read-only; no edit/save controls
  }
  function openAuthorModal(index){ 
    currentAuthorIndex = index;
    const author = (currentDetails.additionalAuthors || [])[index] || {};
    renderAuthorModal(author,false);
    const authorEl = document.getElementById('authorInfoModal');
    if(authorEl) ModalApi.show(authorEl);
  }
  window.showDetailsModal = showDetailsModal; // if needed by HTML (currently triggered via listeners)

  function selectRemark(remark){ const dd=document.getElementById('remarksDropdown'); if(dd) dd.textContent=remark; }
  function selectRemarkActive(remark){ const dd=document.getElementById('remarksDropdownActive'); if(dd) dd.textContent=remark; }

  document.addEventListener('DOMContentLoaded', function(){
    // Global handlers for Author modal edit/save (use currentAuthorIndex)
    // Remove author edit/save wiring (read-only)
    // Density toggle removed; always use optimized layout

    // Open details by clicking Request ID only
    document.addEventListener('click', async e=>{
      const link = e.target.closest('.open-details');
      if(link){
        e.preventDefault();
        const tr = link.closest('tr');
        const reqId = (link.getAttribute('data-request-id') || '').trim() || (tr?.querySelector('td')?.textContent.trim() || '');
        if(!reqId){ showDetailsModal(tr); return; }
        try {
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          const res = await fetch('../admin/fetch_submission_details.php',{ method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: reqId }) });
          let data = {};
          try { data = await res.json(); } catch(_) {}
          if(data && data.success){
            currentDetails = data;
            renderDetails(currentDetails,false);
            const detailsModalEl = document.getElementById('detailsModal');
            if(detailsModalEl) ModalApi.show(detailsModalEl);
          } else {
            // Retry via GET (read-only) to avoid CSRF-related failures
            const res2 = await fetch(`../admin/fetch_submission_details.php?request_id=${encodeURIComponent(reqId)}`, { method:'GET', credentials:'same-origin' });
            let data2 = {};
            try { data2 = await res2.json(); } catch(_) {}
            if(data2 && data2.success){
              currentDetails = data2;
              renderDetails(currentDetails,false);
              const detailsModalEl = document.getElementById('detailsModal');
              if(detailsModalEl) ModalApi.show(detailsModalEl);
            } else {
              console.warn('Details fetch failed', data || data2);
              showDetailsModal(tr);
            }
          }
        } catch(err){
          try {
            const res2 = await fetch(`../admin/fetch_submission_details.php?request_id=${encodeURIComponent(reqId)}`, { method:'GET', credentials:'same-origin' });
            const data2 = await res2.json();
            if(data2 && data2.success){
              currentDetails = data2;
              renderDetails(currentDetails,false);
              const detailsModalEl = document.getElementById('detailsModal');
              if(detailsModalEl) ModalApi.show(detailsModalEl);
            } else {
              console.error('Details fetch GET error', err, data2);
              showDetailsModal(tr);
            }
          } catch(err2){
            console.error('Details fetch error', err, err2);
            showDetailsModal(tr);
          }
        }
      }
    });

    // Remove details edit/save wiring (read-only)

    // Incomplete (pending) single-modal flow
    document.querySelectorAll('#pending .btn-incomplete').forEach(btn=>{ 
      btn.addEventListener('click', async e=>{ 
        e.preventDefault(); 
        const modal = document.getElementById('incompleteModal');
        if(!modal) return; 
        const reqId = btn.closest('tr')?.querySelector('td')?.textContent.trim() || '';
        modal.setAttribute('data-request-id', reqId);
        // Show and fetch documents
        const filesSection = document.getElementById('incompletePendingFilesSection');
        const fileList = document.getElementById('incompletePendingFileList');
        if(filesSection && fileList){
          filesSection.classList.remove('d-none');
          fileList.innerHTML = '<div class="text-muted fst-italic">Loading documents...</div>';
          try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('../admin/fetch_submission_files.php',{ method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: reqId }) });
            const data = await res.json();
            if(!data.success || !data.files.length){ fileList.innerHTML = '<div class="text-muted small">No documents found.</div>'; }
            else {
              fileList.innerHTML = data.files.map(f=>`<div class="form-check mb-1"><input class="form-check-input incomplete-pending-file-checkbox" type="checkbox" value="${f.document_id}" id="pfile_${f.document_id}" data-doc-type="${f.doc_type}"><label class="form-check-label small" for="pfile_${f.document_id}">${f.doc_type.replace(/_/g,' ')}</label></div>`).join('');
            }
          } catch(err){ console.error('fetch pending docs error', err); fileList.innerHTML = '<div class="text-danger small">Failed to load documents.</div>'; }
        }
        ModalApi.show(modal);
      });
    });
    document.getElementById('incompletePendingSelectAll')?.addEventListener('change', e=>{
      const checked = e.target.checked; document.querySelectorAll('.incomplete-pending-file-checkbox').forEach(cb=> cb.checked = checked);
    });
    const confirmIncompleteBtn = document.getElementById('confirmIncompleteBtn');
    if(confirmIncompleteBtn){ confirmIncompleteBtn.addEventListener('click', async ()=>{
      const modal = document.getElementById('incompleteModal');
      if(!modal) return;
      const requestId = modal.getAttribute('data-request-id') || '';
      const remark = document.getElementById('remarksDropdown')?.textContent.trim() || '';
      const comment = document.getElementById('incompleteTextarea')?.value.trim() || '';
  const affected = Array.from(document.querySelectorAll('.incomplete-pending-file-checkbox:checked')).map(cb=> cb.getAttribute('data-doc-type'));
      if(!affected || affected.length === 0){
        alert('Please select at least one document to unlock for re-upload.');
        return;
      }
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('../admin/set_incomplete.php',{ method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: requestId, remark, comment, file_ids: [], affected_doc_types: affected }) });
        // Better error handling: check HTTP status first, then try to parse JSON.
        if(!res.ok){
          const text = await res.text().catch(()=>'(no body)');
          console.error('set_incomplete server error', res.status, text);
          alert('Save failed: server returned ' + res.status + '\n' + text);
          return;
        }
        let data;
        try{
          data = await res.json();
        }catch(parseErr){
          const txt = await res.text().catch(()=>'(no body)');
          console.error('set_incomplete invalid JSON', parseErr, txt);
          alert('Save failed: server returned invalid JSON:\n' + txt);
          return;
        }
        console.debug('[set_incomplete pending single] payload', { request_id: requestId, remark, comment, affected });
        console.debug('[set_incomplete pending single] response', data);
        if(data.success){
          ModalApi.hide(modal);
          const row = Array.from(document.querySelectorAll('#pending tbody tr')).find(tr=> (tr.querySelector('td')?.textContent.trim()||'') === requestId);
            if(row){
              const cells = row.querySelectorAll('td');
              // Remarks column is 5th (index 4) in Pending tab; show Awaiting Review only when files were selected
              if(cells[4]){
                if(affected.length > 0){
                  cells[4].innerHTML = '<span class="status-badge status-awaiting">Awaiting Review</span>';
                } else {
                  cells[4].innerHTML = '<span class="status-badge status-pending">For Evaluation</span>';
                }
              }
              row.classList.add('table-warning');
              if(comment) row.setAttribute('data-admin-comment', comment);
              if(remark && remark.toLowerCase() !== 'remarks') row.setAttribute('data-incomplete-remark', remark);
              if(affected.length) row.setAttribute('data-resubmit-files', affected.join('|'));
              ensureCommentsButton(row);
            }
          alert('Marked as Incomplete successfully.');
          // Reset modal fields (dropdown label, textarea, checkboxes) after success
          const dd = document.getElementById('remarksDropdown');
          if(dd) dd.textContent = 'Remarks';
          const ta = document.getElementById('incompleteTextarea');
          if(ta) ta.value = '';
          const selectAll = document.getElementById('incompletePendingSelectAll');
          if(selectAll) selectAll.checked = false;
          document.querySelectorAll('.incomplete-pending-file-checkbox').forEach(cb=> cb.checked = false);
          const fileList = document.getElementById('incompletePendingFileList');
          if(fileList) fileList.innerHTML = '<div class="text-muted fst-italic">Select affected document(s)</div>';
        } else {
          alert('Failed to mark incomplete: ' + (data.error || 'Unknown error'));
        }
  } catch(err){ console.error('set_incomplete pending error', err); alert('Network error during save: ' + (err && err.message ? err.message : String(err))); }
    }); }

    // Functions for multi-step incomplete workflow
    // Legacy multi-step functions removed (openIncompleteFileSelection) now replaced by inline flow.

    // Remove old select-all and next logic (single modal now)

    // Removed save confirmation modal listener (single modal flow replacement)

    // Remark dropdown choices (now data-driven, no inline handlers)
    document.addEventListener('click', e=>{
      const choice = e.target.closest('.remark-choice');
      if(choice){ e.preventDefault(); selectRemark(choice.getAttribute('data-remark')||''); }
      const choiceActive = e.target.closest('.remark-choice-active');
      if(choiceActive){ e.preventDefault(); selectRemarkActive(choiceActive.getAttribute('data-remark')||''); }
    });

    // Complete buttons (Approved tab)
    document.addEventListener('click', e=>{
      const completeBtn = e.target.closest('.btn-complete');
      if(!completeBtn) return;
      e.preventDefault();
      // Show complete modal and set hidden input
      const reqId = completeBtn.getAttribute('data-request-id') || '';
      const hiddenInput = document.getElementById('complete_ticket_id');
      if(hiddenInput) hiddenInput.value = reqId;
      const completeModalEl = document.getElementById('completeModal');
      if(completeModalEl) ModalApi.show(completeModalEl);
    });

    // Approve flow
    const approveCommentModalEl = document.getElementById('approveCommentModal');
    const successModalEl = document.getElementById('successModal');

    // Intercept Complete modal confirm (form submit) to perform AJAX completion and dynamic row move
    document.addEventListener('submit', async e=>{
      const form = e.target.closest('#completeModal form');
      if(!form) return; // not our form
      e.preventDefault();
      const requestId = document.getElementById('complete_ticket_id')?.value.trim() || '';
      const comment = document.getElementById('complete_comments')?.value.trim() || '';
      if(!requestId){ ModalApi.hide(document.getElementById('completeModal')); return; }
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const payload = { request_id: requestId, comment };
  console.debug('[complete_request] sending', payload, 'csrf:', csrf);
          const res = await fetch('../admin/complete_request.php',{
            method:'POST',
            credentials:'same-origin',
            headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},
            body: JSON.stringify(payload)
          });
        let data = {};
        try { data = await res.json(); } catch(parseErr){
          console.error('complete_request parse error', parseErr);
          try {
            const txt = await res.text();
            data = { error: 'Non-JSON response', detail: txt?.slice(0,500) };
          } catch(_) {}
        }
  console.debug('[complete_request] response status', res.status, 'body', data);
        if(!res.ok){
          console.error('Complete request HTTP error', res.status, data);
          const extra = (data && (data.phase || data.detail))
            ? (`\n${data.phase ? ('Phase: ' + data.phase + '\n') : ''}${data.detail ? ('Detail: ' + data.detail) : ''}`)
            : '';
          alert('Failed to complete: ' + (data.error || ('HTTP ' + res.status)) + extra);
          return;
        }
        if(!data.success && !data.idempotent){
          console.warn('Complete request failure payload', data);
          let extra = '';
          if(data.current_status){ extra = ' (current status: '+data.current_status+')'; }
          if(data.detail){ extra += '\nDetail: '+data.detail; }
          alert('Failed to complete: ' + (data.error || 'Unknown error') + extra);
          return;
        }
    // Treat idempotent as success if already completed
    // Find the row in any tab (approved preferred) and move to completed
        let row = Array.from(document.querySelectorAll('#approved tbody tr, #pending tbody tr')).find(tr=> (tr.querySelector('td')?.textContent.trim()||'') === requestId);
        if(row){
          const cells = row.querySelectorAll('td');
          // Capture values based on current column structure:
          // [0]=Request ID, [1]=Name, [2]=User, [3]=Request Date, [4]=Status/Remarks, [5]=Notes, [6]=Action
          const reqLink = cells[0]?.querySelector('a.open-details');
          const requestIdText = (reqLink?.getAttribute('data-request-id') || cells[0]?.textContent || requestId).trim();
          const nameText = (cells[1]?.querySelector('.fw-semibold')?.textContent || cells[1]?.textContent || '').trim();
          const studIdText = (cells[1]?.querySelector('.student-subtext')?.textContent || '').trim();
          const userLabel = (cells[2]?.textContent || '').trim();
          const reqDateText = (cells[3]?.textContent || '').trim();
          const notesHTML = (cells[5]?.innerHTML || '<span class="text-muted small">None</span>');
          const rowData = { requestId: requestIdText, studentName: nameText, studentId: studIdText, userLabel, requestDate: reqDateText, notesHTML };
          // Completed tab rule: show Comments only when a completion-time comment is provided.
          const completionComment = (document.getElementById('complete_comments')?.value || '').trim();
          const shouldShowComments = !!completionComment;
          row.remove();
          const completedTbody = document.querySelector('#completed tbody');
          if(completedTbody){
            // Remove placeholder if present
            const placeholder = completedTbody.querySelector('tr td[colspan]');
            if(placeholder && /no completed requests/i.test(placeholder.textContent)){ placeholder.parentElement.remove(); }
            const newRow = document.createElement('tr');
            const commentsBtnHTML = shouldShowComments ? '<a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>' : '';
            newRow.innerHTML = `
              <td><a href=\"#\" class=\"open-details\" data-request-id=\"${escapeHTML(rowData.requestId)}\"><span style=\"font-weight:600;\">${escapeHTML(rowData.requestId)}</span></a></td>
              <td><div><a href=\"#\" class=\"open-details\" data-request-id=\"${escapeHTML(rowData.requestId)}\">${escapeHTML(rowData.studentName)}</a><div class=\"student-subtext text-muted small\">${escapeHTML(rowData.studentId)}</div></div></td>
              <td>${escapeHTML(rowData.userLabel)}</td>
              <td>${escapeHTML(rowData.requestDate)}</td>
              <td><span class=\"status-badge status-completed\">Completed</span></td>
              <td>${rowData.notesHTML}</td>
              <td>
                <div class="action-btn-group">
                  <a href="#" class="btn btn-success btn-sm rounded-pill px-3 btn-view-certificate" data-request-id="${escapeHTML(rowData.requestId)}">View Certificate</a>
                  ${commentsBtnHTML}
                </div>
              </td>`;
            // Set only completion-time comment on the completed row
            if (completionComment) newRow.setAttribute('data-admin-comment', completionComment);
            completedTbody.prepend(newRow);
          }
        }
        // Reset & close modal
        const ta = document.getElementById('complete_comments'); if(ta) ta.value='';
        document.getElementById('complete_ticket_id').value='';
        ModalApi.hide(document.getElementById('completeModal'));
        alert('Request marked as Completed.');
      } catch(err){ console.error('complete_request error', err); alert('Network error completing request'); }
    });

    document.querySelectorAll('.btn-approve').forEach(btn=>{
      btn.addEventListener('click', e=>{
        e.preventDefault();
  pendingApproveRow = btn.closest('tr');
  const hiddenInput = btn.closest('form')?.querySelector('input[name="approve_ticket_id"]');
        const requestId = hiddenInput ? hiddenInput.value : '';
        const reqInput = document.getElementById('approveRequestId');
        if(reqInput) reqInput.value = requestId;
        const commentText = document.getElementById('approveCommentText');
        if(commentText) commentText.value = '';
        if(approveCommentModalEl) ModalApi.show(approveCommentModalEl);
      });
    });

    const approveConfirmBtn = document.getElementById('approveCommentConfirmBtn');
    if(approveConfirmBtn){ approveConfirmBtn.addEventListener('click', async ()=>{
      const reqInput = document.getElementById('approveRequestId');
      const requestId = reqInput ? reqInput.value : '';
      const commentEl = document.getElementById('approveCommentText');
      const comment = commentEl ? commentEl.value.trim() : '';
      if(!requestId) return;
      try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const res = await fetch('../approve_request.php', { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-Token': csrfToken }, body: JSON.stringify({ request_id: requestId, comment }) });
  const data = await res.json();
  console.debug('[approve_request] payload', { request_id: requestId, comment });
  console.debug('[approve_request] response', data);
  if(data.success){
          if(approveCommentModalEl) ModalApi.hide(approveCommentModalEl);
          if(pendingApproveRow){
            const cells = pendingApproveRow.querySelectorAll('td');
            // Column layout in Pending: [0]=Request ID, [1]=Name, [2]=User, [3]=Request Date, [4]=Remarks, [5]=Notes, [6]=Action
            const reqLink = cells[0]?.querySelector('a.open-details');
            const reqIdText = (reqLink?.getAttribute('data-request-id') || cells[0]?.textContent || requestId).trim();
            const nameText = (cells[1]?.querySelector('.fw-semibold')?.textContent || cells[1]?.textContent || '').trim();
            const studIdText = (cells[1]?.querySelector('.student-subtext')?.textContent || '').trim();
            const userLabel = (cells[2]?.textContent || '').trim();
            const reqDateText = (cells[3]?.textContent || '').trim();
            const notesHTML = (cells[5]?.innerHTML || '<span class="text-muted small">None</span>');
            const rowData = { requestId: reqIdText, studentName: nameText, studentId: studIdText, userLabel, requestDate: reqDateText, notesHTML };
            // For Approved row, use ONLY the Approve-time comment; do not carry Pending comments forward
            const approveTimeComment = (comment || '').trim();
            const carriedComment = approveTimeComment;
            // Approved tab rule: show Comments button only when there is an approval-time comment
            const shouldShowComments = !!approveTimeComment;
            pendingApproveRow.remove();
            const approvedTbody = document.querySelector('#approved tbody');
            if(approvedTbody){
              const newRow = document.createElement('tr');
              // Build Comments button only if there is a comment (respect visibility rule)
              const commentsBtnHTML = shouldShowComments ? `<a href="#" class="btn btn-comments btn-sm rounded-pill px-3">Comments</a>` : '';
              newRow.innerHTML = `
                <td><a href="#" class="open-details" data-request-id="${escapeHTML(rowData.requestId)}">${escapeHTML(rowData.requestId)}</a></td>
                <td>
                  <div>
                    <span class="fw-semibold">${escapeHTML(rowData.studentName)}</span>
                    <div class="student-subtext text-muted small">${escapeHTML(rowData.studentId)}</div>
                  </div>
                </td>
                <td>${escapeHTML(rowData.userLabel)}</td>
                <td>${escapeHTML(rowData.requestDate)}</td>
                <td>In-Review</td>
                <td>${rowData.notesHTML}</td>
                <td>
                  <div class="action-btn-group">
                    ${commentsBtnHTML}
                    <button class="btn btn-success btn-sm rounded-pill px-3 me-1 btn-complete" data-request-id="${escapeHTML(rowData.requestId)}">Complete</button>
                    <span class="btn btn-incomplete btn-incomplete-active btn-sm rounded-pill px-3">Incomplete</span>
                  </div>
                </td>`;
              if(carriedComment) newRow.setAttribute('data-admin-comment', carriedComment);
              approvedTbody.prepend(newRow);
            }
          }
          pendingApproveRow = null;
          if(successModalEl) ModalApi.show(successModalEl);
        } else {
          const phase = data.phase ? ` (phase: ${data.phase})` : '';
          let detail = '';
          if(data.detail){ detail = `\nDetail: ${data.detail}`; }
          alert((data.error || 'Approval failed') + phase + detail);
        }
      } catch(err){ console.error('Approval network/parse error', err); alert('Network error'); }
    }); }

    // View Certificate from Completed tab
    document.addEventListener('click', async e=>{
      const trigger = e.target.closest('.btn-view-certificate');
      if(!trigger) return;
      e.preventDefault();
      const requestId = trigger.getAttribute('data-request-id') || '';
      const modalEl = document.getElementById('certificateModal');
      if(!modalEl) return;
      const body = modalEl.querySelector('.modal-body');
      if(body) body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('../admin/fetch_submission_details.php', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: requestId }) });
        const data = await res.json();
        if(!data.success){
          if(body) body.innerHTML = '<div class="text-danger">Failed to load certificate.</div>';
        } else {
          // Render a simple certificate preview: re-use server-side rendering if available or compose HTML
          const title = (data.documentTitle || data.title || 'Certificate');
          const owner = data.studentName || '';
          const code = data.request_id || requestId;
          // Updated: use dedicated certificate endpoint (server generates PDF via FPDI)
          const iframeId = 'certFrame_' + Date.now();
          const html = `<div class="certificate-preview"><iframe id="${iframeId}" src="view_certificate.php?id=${encodeURIComponent(code)}" width="100%" height="500px" style="border:none;" loading="lazy" referrerpolicy="no-referrer"></iframe><div class="small text-muted mt-2" id="${iframeId}_status">Loading certificate...</div></div>`;
          if(body) body.innerHTML = html;
          const dl = modalEl.querySelector('#downloadCertificateBtn');
          if(dl) dl.href = 'view_certificate.php?id=' + encodeURIComponent(code) + '&mode=download';
          // Fallback detection: if iframe blocked by CSP/X-Frame-Options, offer new-tab link
          setTimeout(()=>{
            const iframe = document.getElementById(iframeId);
            const statusEl = document.getElementById(iframeId + '_status');
            if(!iframe) return;
            let loaded = false;
            try {
              if (iframe.contentDocument || iframe.contentWindow?.document) {
                loaded = true;
              }
            } catch(_) { /* blocked */ }
            if(!loaded) {
              if(statusEl) statusEl.innerHTML = 'Embedded preview blocked. <a href="view_certificate.php?id='+encodeURIComponent(code)+'" target="_blank" rel="noopener">Open in new tab</a>.';
            } else if(statusEl) {
              statusEl.textContent = '';
            }
          }, 1200);
        }
      } catch(err){
        if(body) body.innerHTML = '<div class="text-danger">Network error.</div>';
      }
      try {
        if(window.bootstrap && window.bootstrap.Modal){
          window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
      } catch(_){ modalEl.style.display = 'block'; }
    });

    // Incomplete Active (Approved tab) - keep row in Approved, update in place (no resubmission flow)
    let approvedIncompleteTargetRow = null;
    document.addEventListener('click', e=>{
      const trigger = e.target.closest('.btn-incomplete-active');
      if(!trigger) return; e.preventDefault();
      approvedIncompleteTargetRow = trigger.closest('tr');
      const modal = document.getElementById('incompleteActiveModal');
      if(modal && approvedIncompleteTargetRow){
        const reqId = (approvedIncompleteTargetRow.querySelector('td')?.textContent || '').trim();
        modal.setAttribute('data-request-id', reqId);
        // Prepare files section (fetch list)
        const filesSection = document.getElementById('incompleteActiveFilesSection');
        const fileList = document.getElementById('incompleteActiveFileList');
        if(filesSection && fileList){
          filesSection.classList.remove('d-none');
          fileList.innerHTML = '<div class="text-muted fst-italic">Loading documents...</div>';
          (async ()=>{
            try {
              const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
              const res = await fetch('../admin/fetch_submission_files.php',{ method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: reqId }) });
              const data = await res.json();
              if(!data.success || !Array.isArray(data.files) || !data.files.length){
                fileList.innerHTML = '<div class="text-muted small">No documents found.</div>';
              } else {
                fileList.innerHTML = data.files.map(f=>`<div class="form-check mb-1"><input class="form-check-input incomplete-active-file-checkbox" type="checkbox" value="${f.document_id}" id="actfile_${f.document_id}" data-doc-type="${f.doc_type}"><label class="form-check-label small" for="actfile_${f.document_id}">${f.doc_type.replace(/_/g,' ')}</label></div>`).join('');
              }
            } catch(err){
              console.error('fetch approved docs error', err);
              fileList.innerHTML = '<div class="text-danger small">Failed to load documents.</div>';
            }
          })();
        }
        ModalApi.show(modal);
      }
    });
    // Select All for approved incomplete modal
    document.getElementById('incompleteActiveSelectAll')?.addEventListener('change', e=>{
      const checked = e.target.checked; document.querySelectorAll('.incomplete-active-file-checkbox').forEach(cb=> cb.checked = checked);
    });
    const confirmIncompleteActiveBtn = document.getElementById('confirmIncompleteActiveBtn');
    if(confirmIncompleteActiveBtn){
      confirmIncompleteActiveBtn.addEventListener('click', async ()=>{
        const modal = document.getElementById('incompleteActiveModal');
        if(!modal || !approvedIncompleteTargetRow) return;
        const requestId = modal.getAttribute('data-request-id') || '';
        const remark = (document.getElementById('remarksDropdownActive')?.textContent || '').trim();
        const comment = (document.getElementById('incompleteTextareaActive')?.value || '').trim();
  const affected = Array.from(document.querySelectorAll('.incomplete-active-file-checkbox:checked')).map(cb=> cb.getAttribute('data-doc-type'));
        if(!affected || affected.length === 0){
          alert('Please select at least one document to unlock for re-upload.');
          return;
        }
        if(!requestId){ ModalApi.hide(modal); return; }
        try {
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          const res = await fetch('../admin/mark_incomplete_approved.php', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':csrf}, body: JSON.stringify({ request_id: requestId, remark, comment, affected_files: affected }) });
          const data = await res.json();
          console.debug('[mark_incomplete_approved] payload', { request_id: requestId, remark, comment });
          console.debug('[mark_incomplete_approved] response', data);
          if(data.success){
            // Update row in place: keep in Approved; set status and flagged files
            const cells = approvedIncompleteTargetRow.querySelectorAll('td');
            // Approved tab columns: [0]=Request ID, [1]=Name, [2]=User, [3]=Request Date, [4]=Status, [5]=Notes, [6]=Action
            if (cells[4]){ cells[4].innerHTML = '<span class="status-badge status-awaiting">Awaiting Review</span>'; }
            approvedIncompleteTargetRow.classList.add('table-warning');
            if(comment) approvedIncompleteTargetRow.setAttribute('data-admin-comment', comment);
            if(remark && remark.toLowerCase() !== 'remarks') approvedIncompleteTargetRow.setAttribute('data-incomplete-remark', remark);
            if(affected.length){ approvedIncompleteTargetRow.setAttribute('data-resubmit-files', affected.join('|')); }
            // Approved tab: show Comments button only if there's an approval-time comment
            const existingBtn = approvedIncompleteTargetRow.querySelector('.btn-comments');
            if (comment) {
              if (!existingBtn) ensureCommentsButton(approvedIncompleteTargetRow);
            } else if (existingBtn) {
              existingBtn.remove();
            }
            alert('Marked as Incomplete. The request remains in Approved and is flagged for resubmission.');
            // Reset active modal fields
            const ddA = document.getElementById('remarksDropdownActive');
            if(ddA) ddA.textContent = 'Remarks';
            const taA = document.getElementById('incompleteTextareaActive');
            if(taA) taA.value = '';
            const selectAllA = document.getElementById('incompleteActiveSelectAll');
            if(selectAllA) selectAllA.checked = false;
            document.querySelectorAll('.incomplete-active-file-checkbox').forEach(cb=> cb.checked = false);
            const fileListA = document.getElementById('incompleteActiveFileList');
            if(fileListA) fileListA.innerHTML = '<div class="text-muted fst-italic">Select affected document(s)</div>';
          } else {
            alert('Failed to mark Incomplete: ' + (data.error || 'Unknown error'));
          }
        } catch(err){
          console.error('Error marking approved row incomplete', err);
          alert('Network error while marking Incomplete');
        } finally { ModalApi.hide(modal); }
      });
    }

    // Comments (open modal)
    document.addEventListener('click', e=>{
      const trigger = e.target.closest('.btn-comments');
      if(!trigger) return; e.preventDefault();
      let text='';
      const tr = trigger.closest('tr');
      if(tr){
        const adminComment = (tr.getAttribute('data-admin-comment')||'').trim();
        const inApproved = !!tr.closest('#approved');
        const inCompleted = !!tr.closest('#completed');
        if (inApproved) {
          // Approved: show only approval-time comment if present
          text = adminComment || 'No remarks available.';
        } else if (inCompleted) {
          // Completed: show only completion-time comment if present
          text = adminComment || 'No remarks available.';
        } else {
          // Pending: show composite of pending remark/resubmit/admin comment if any
          const incompleteRemark = tr.getAttribute('data-incomplete-remark') || '';
          const resubmit = tr.getAttribute('data-resubmit-files') || '';
          const resubmitList = resubmit ? resubmit.split('|').filter(Boolean).map(s=> s.replace(/_/g,' ')) : [];
          if(incompleteRemark || resubmitList.length || adminComment){
            const parts = [];
            if(incompleteRemark){ parts.push(`Issue: ${incompleteRemark}`); }
            if(resubmitList.length){ parts.push(`File(s) to re-upload: ${resubmitList.join(', ')}`); }
            if(adminComment){ parts.push(`Comment: ${adminComment}`); }
            text = parts.join('\n');
          } else {
            text = 'For Evaluation';
          }
        }
      }
      if(!text) text = 'No remarks available.';
      const ta = document.getElementById('comment_text'); if(ta) ta.value = text;
      const cm = document.getElementById('commentModal'); if(cm) ModalApi.show(cm);
    });

    // Row expander ('More') removed

    // Initialization scan: Add Comments button for any pending rows already in 'Awaiting Review'
  // Ensure rows already marked 'Awaiting Review' in Pending have a Comments button
  document.querySelectorAll('#pending tbody tr').forEach(tr=>{ const cells = tr.querySelectorAll('td'); const remark = (cells[4]?.textContent || '').trim().toLowerCase(); if(remark === 'awaiting review'){ ensureCommentsButton(tr); } });
    // Optional cleanup: Approved tab should show Comments button only if an approval-time comment exists
    document.querySelectorAll('#approved tbody tr').forEach(tr=>{
      const adminComment = (tr.getAttribute('data-admin-comment')||'').trim();
      const btn = tr.querySelector('.btn-comments');
      if (adminComment) {
        if (!btn) ensureCommentsButton(tr);
      } else if (btn) {
        btn.remove();
      }
    });

    // Table sorting
    function sortTable(tbody, key, dir){
      const table = tbody.closest('table');
      const rows = Array.from(tbody.querySelectorAll('tr'));
      // Determine column index dynamically based on header data-sort
      const headers = Array.from(table?.querySelectorAll('thead th') || []);
      const colIndex = headers.findIndex(h => (h.getAttribute('data-sort')||'') === key);
      const nth = (colIndex >= 0 ? (colIndex + 1) : null);

      function cellText(tr, n){
        if (n === null) return tr.textContent.trim();
        const td = tr.querySelector('td:nth-child(' + n + ')');
        return (td ? td.textContent : '').trim();
      }

      function parseDateYmdHms(text){
        const s = String(text).trim();
        // Expect: YYYY-MM-DD HH:MM:SS (or with 'T' separator)
        const m = s.match(/^\s*(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})\s*$/);
        if(!m) return NaN;
        const y = +m[1], mo = +m[2]-1, d = +m[3], hh = +m[4], mm = +m[5], ss = +m[6];
        // Use UTC to avoid TZ differences when comparing
        return Date.UTC(y, mo, d, hh, mm, ss);
      }

      const getVal = (tr)=>{
        switch(key){
          case 'program': return (tr.querySelector('.col-program')?.textContent||'').trim();
          case 'date': {
            const t = cellText(tr, nth ?? 5);
            const ts = parseDateYmdHms(t);
            return Number.isFinite(ts) ? ts : t.toLowerCase();
          }
          default:
            return cellText(tr, nth).toLowerCase();
        }
      };

      rows.sort((a,b)=>{
        const va = getVal(a);
        const vb = getVal(b);
        // Numeric compare if both numbers (timestamps), else string
        const numa = typeof va === 'number' && Number.isFinite(va);
        const numb = typeof vb === 'number' && Number.isFinite(vb);
        let cmp = 0;
        if (numa && numb) {
          cmp = va === vb ? 0 : (va > vb ? 1 : -1);
        } else {
          const sa = String(va);
          const sb = String(vb);
          cmp = sa === sb ? 0 : (sa > sb ? 1 : -1);
        }
        return dir==='asc' ? cmp : -cmp;
      });
      rows.forEach(r=> tbody.appendChild(r));
    }
    document.querySelectorAll('table thead th.sortable').forEach(th=>{
      th.setAttribute('role','button');
      th.addEventListener('click', ()=>{
        const table = th.closest('table');
        const tbody = table?.querySelector('tbody');
        const key = th.getAttribute('data-sort')||'';
        const current = th.getAttribute('aria-sort');
        const dir = current==='asc' ? 'desc' : 'asc';
        // reset others
        th.parentElement.querySelectorAll('th.sortable').forEach(h=> h.removeAttribute('aria-sort'));
        th.setAttribute('aria-sort', dir);
        if(tbody && key){ sortTable(tbody, key, dir); }
      });
    });
  });
})();
