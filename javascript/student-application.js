// student-application.js
// Global delegated handler (CSP-safe) for comments & remarks in case buttons
// are added after DOMContentLoaded or existing listener misses them.
document.addEventListener('click', function(e){
  const commentBtn = e.target.closest('.btn-comments');
  if(!commentBtn) return;
  e.preventDefault();
  const modalEl = document.getElementById('commentModal');
  const textarea = document.getElementById('comment_text');
  if(!modalEl || !textarea){
    console.warn('[student-application] Comment modal elements not found');
    return;
  }
  const tr = commentBtn.closest('tr');
  const text = commentBtn.getAttribute('data-admin-comment') || (tr ? tr.getAttribute('data-admin-comment') : '') || '';
  console.debug('[student-application] Opening comment modal with text:', text);
  textarea.value = text || 'No comment available.';
  try {
    if(window.bootstrap && window.bootstrap.Modal){
      const inst = window.bootstrap.Modal.getOrCreateInstance(modalEl);
      inst.show();
    } else {
      console.warn('[student-application] Bootstrap modal library not detected, fallback display');
      modalEl.style.display='block';
    }
  } catch(err){
    console.error('[student-application] Failed to show modal', err);
    modalEl.style.display='block';
  }
});
document.addEventListener('DOMContentLoaded', function () {
  // Reupload state cache persists across modal openings this session
  // Structure: { submissionCode: { locked:boolean, done:Set<string>, sig:string } }
  window._reuploadState = window._reuploadState || {};

  const LS_KEY = 'reuploadStateV1';
  try {
    const raw = localStorage.getItem(LS_KEY);
    if(raw){
      const parsed = JSON.parse(raw);
      Object.entries(parsed).forEach(([code,val])=>{
        window._reuploadState[code] = { locked: !!val.locked, done: new Set(val.done||[]), sig: val.sig || '' };
      });
    }
  } catch(e){ /* ignore */ }

  function persistState(){
    try {
      const serializable = {};
      Object.entries(window._reuploadState).forEach(([code,val])=>{
        serializable[code] = { locked: !!val.locked, done: Array.from(val.done||[]), sig: val.sig || '' };
      });
      localStorage.setItem(LS_KEY, JSON.stringify(serializable));
    } catch(e){ /* ignore */ }
  }
  // --- View submission details ---
  const viewButtons = document.querySelectorAll('.view-details-btn');
  const detailsModalEl = document.getElementById('submissionDetailsModal');
  const detailsContent = document.getElementById('submissionDetailsContent');

  if (viewButtons.length > 0 && detailsModalEl && detailsContent) {
    viewButtons.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();

  const submissionCode = btn.getAttribute('data-id');
        if (!submissionCode) return;

        const modal = bootstrap.Modal.getOrCreateInstance(detailsModalEl);

        // Show spinner while loading
        detailsContent.innerHTML =
          '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        modal.show();

        // Use 'code' instead of 'id'
  const resubmitRaw = btn.getAttribute('data-resubmit-files') || '';
        // Prefer JSON + shared renderer for speed and consistency
        fetch('fetch_submission_details_user.php?code=' + encodeURIComponent(submissionCode), { credentials: 'same-origin' })
          .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP '+r.status)))
          .then((data) => {
            if(data && data.success && typeof window.renderSubmissionDetails === 'function'){
              const container = document.createElement('div');
              detailsContent.innerHTML = '';
              detailsContent.appendChild(container);
              const opts = { role: 'user' };
              if(resubmitRaw){ opts.flaggedTypes = resubmitRaw; }
              window.renderSubmissionDetails(container, data, { role: 'user' });

              // Provide a host for reupload controls (placed BEFORE Notes)
              const host = document.createElement('div');
              host.id = 'reuploadControls';
              host.className = 'w-75 mt-3 mx-auto';
              detailsContent.appendChild(host);

              // Notes section: only show in Pending tab
              const inPendingTabForNotes = !!btn.closest('#pending');
              if(inPendingTabForNotes){
                const notesWrap = document.createElement('div');
                notesWrap.className = 'mt-3';
                const notes = Array.isArray(data.notes) ? data.notes : [];
                const canNote = !!data.canNote;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const subCode = data.submissionCode || '';
                const noteCount = notes.length;
                const inCompletedTab = !!btn.closest('#completed');
                let notesListHTML = '';
                if(notes.length){
                  notesListHTML = '<ul class="list-group mb-2" id="notesList">' + notes.map(n=>{
                    const created = String(n.created_at||'');
                    const txt = String(n.note||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
                    return `<li class="list-group-item py-2"><div class="small text-muted">${created}</div><div class="mt-1" style="white-space:pre-wrap; word-wrap:break-word;">${txt}</div></li>`;
                  }).join('') + '</ul>';
                } else {
                  notesListHTML = '<div class="text-muted small mb-2" id="notesEmpty">No notes yet.</div><ul class="list-group mb-2 d-none" id="notesList"></ul>';
                }
                const formHTML = canNote ? (
                  `<form id="noteForm" class="card border-0">
                    <div class="card-body p-2">
                      <div class="mb-2">
                        <label for="noteText" class="form-label small mb-1">Add a note to the IPMO</label>
                        <textarea id="noteText" name="note" rows="3" class="form-control form-control-sm" maxlength="1000" placeholder="Be clear and concise (max 1000 chars)"></textarea>
                        <div class="form-text">Max 1000 characters. Avoid personal data. Keep it relevant to your application.</div>
                      </div>
                      <input type="hidden" name="submission_code" value="${subCode}">
                      <input type="hidden" name="csrf_token" value="${csrf}">
                      <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary" id="noteSubmitBtn">Send Note</button>
                        <span class="small text-muted" id="noteHint"></span>
                      </div>
                    </div>
                  </form>`
                ) : (inCompletedTab ? '' : '<div class="alert alert-warning small p-2 mb-0">Notes are disabled for this submission status.</div>');

                notesWrap.innerHTML = `
                  <div class="d-flex flex-column align-items-center">
                    <div class="w-75">
                      <a href="#" class="notes-toggle small text-decoration-none" aria-expanded="false" aria-controls="notesSection">
                        <span class="toggle-icon" aria-hidden="true">+</span>
                        <span class="toggle-text">Notes ${noteCount ? '('+noteCount+')' : ''}</span>
                      </a>
                      <div id="notesSection" class="mt-2 d-none" role="region" aria-label="Notes">
                        ${notesListHTML}
                        ${formHTML}
                      </div>
                    </div>
                  </div>`;
                detailsContent.appendChild(notesWrap);
              }
            } else {
              // Fallback: use legacy HTML endpoint
              return fetch('view-submission.php?code=' + encodeURIComponent(submissionCode) + '&modal=1')
                .then(resp => resp.text())
                .then(html => { detailsContent.innerHTML = html; });
            }
            const controlHost = detailsContent.querySelector('#reuploadControls');
            const inPendingTab = !!btn.closest('#pending');
            const inApprovedTab = !!btn.closest('#approved');
            const inCompletedTab = !!btn.closest('#completed');
            const cachedState = window._reuploadState[submissionCode];

            // If server indicates resubmission is needed, always unlock and show upload controls (new cycle)
            if(resubmitRaw){
              // affected_doc_types stored pipe-separated (type identifiers)
              const parts = resubmitRaw.split('|').map(p=>p.trim()).filter(Boolean);
              const signature = parts.slice().sort().join('|');

              // Attempt to highlight matching document rows/links if present in details markup
              try {
                const lowerSet = new Set(parts.map(p=>p.toLowerCase()));
                // Heuristic: look for elements with data-doc-type OR list items containing doc type text
                const docNodes = detailsContent.querySelectorAll('[data-doc-type], li, tr');
                docNodes.forEach(node => {
                  let key = '';
                  if(node.hasAttribute && node.hasAttribute('data-doc-type')) {
                    key = (node.getAttribute('data-doc-type')||'').toLowerCase();
                  } else {
                    key = (node.textContent||'').trim().toLowerCase();
                  }
                  if(!key) return;
                  for(const wanted of lowerSet){
                    if(key.includes(wanted)) {
                      node.classList.add('reupload-highlight');
                      break;
                    }
                  }
                });
                // Inject minimal style (CSP safe if style-src allows inline; if not, could be moved to CSS file)
                if(!document.getElementById('reuploadHighlightStyle')){
                  const style = document.createElement('style');
                  style.id = 'reuploadHighlightStyle';
                  style.textContent = '.reupload-highlight { background: #fff3cd !important; transition: background .3s; }';
                  document.head.appendChild(style);
                }
              } catch(highlightErr){
                console.warn('[student-application] highlight failed', highlightErr);
              }

              // Build reupload controls ONLY in Pending tab
              if(controlHost && parts && parts.length && inPendingTab){
                // Always treat presence of resubmitRaw as a NEW cycle even if signature matches previous.
                let state = { locked:false, done:new Set(), sig: signature };
                window._reuploadState[submissionCode] = state;
                persistState();
                const friendly = (t)=> t.replace(/_/g,' ').replace(/\b\w/g,m=>m.toUpperCase());
                const formHtml = parts.map(pt=>`<div class="mb-2 d-flex align-items-center gap-2 reupload-row" data-doc="${pt}">
                  <span class="badge bg-secondary doc-status" title="Not selected">✗</span>
                  <span class="small flex-grow-1">${friendly(pt)}</span>
                  <input type="file" accept="application/pdf" class="form-control form-control-sm reupload-input" data-doc-type="${pt}" style="max-width:260px;">
                </div>`).join('');
                controlHost.innerHTML = `<div class="card border-warning"><div class="card-body p-2">
                  <h6 class="fw-bold mb-2 text-warning">Re-upload Files</h6>
                  <div class="small mb-2">Select replacement PDFs, then click Submit to upload. A check mark will appear once uploaded.</div>
                  ${formHtml}
                  <div class="d-flex align-items-center gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-warning" id="reuploadSubmitBtn"><span class="submit-text">Submit</span></button>
                    <div class="small text-muted" id="reuploadProgress" style="display:none;">Uploading...</div>
                  </div>
                  <div class="form-text mt-2">Max size 5MB each. PDF only.</div>
                </div></div>`;

                // Fresh cycle -> no pre-marked done docs

                // Mark selection changes only
                controlHost.addEventListener('change', function(ev){
                  const input = ev.target.closest('.reupload-input');
                  if(!input) return;
                  const row = input.closest('.reupload-row');
                  const badge = row.querySelector('.doc-status');
                  const file = input.files && input.files[0];
                  if(!file){
                    badge.textContent = '✗';
                    badge.className = 'badge bg-secondary doc-status';
                    badge.title = 'Not selected';
                    return;
                  }
                  if(file.size > 5*1024*1024){
                    alert('File exceeds 5MB limit.');
                    input.value='';
                    badge.textContent = '✗';
                    badge.className = 'badge bg-danger doc-status';
                    badge.title = 'File too large';
                    return;
                  }
                  badge.textContent = '!';
                  badge.className = 'badge bg-info doc-status';
                  badge.title = 'Ready to upload';
                });

                // Batch upload on submit
                controlHost.addEventListener('click', function(ev){
                  const btnSubmit = ev.target.closest('#reuploadSubmitBtn');
                  if(!btnSubmit) return;
                  const inputs = [...controlHost.querySelectorAll('.reupload-input')].filter(i=>i.files && i.files[0]);
                  if(!inputs.length){
                    alert('No files selected.');
                    return;
                  }
                  btnSubmit.disabled = true;
                  const progress = controlHost.querySelector('#reuploadProgress');
                  progress.style.display='inline';
                  progress.textContent = 'Uploading 0/' + inputs.length + '...';
                  let done=0, successCount=0, failCount=0;

                  let lastResponse = null;
                  const uploadNext = (idx)=>{
                    if(idx>=inputs.length){
                      progress.textContent = `Completed. Success: ${successCount}, Failed: ${failCount}`;
                      // If at least one success and no failures, fully lock controls
                      if(successCount>0 && failCount===0){
                        btnSubmit.disabled = true;
                        btnSubmit.classList.remove('btn-warning');
                        btnSubmit.classList.add('btn-success');
                        btnSubmit.querySelector('.submit-text').textContent = 'Submitted';
                        btnSubmit.title = 'Files submitted';
                        // Disable all inputs
                        controlHost.querySelectorAll('.reupload-input').forEach(i=>{ i.disabled = true; });
                        // Persist lock state
                        window._reuploadState[submissionCode] = { locked:true, done: state.done, sig: state.sig };
                        persistState();
                        // Replace card body with compact success banner
                        const parentCard = controlHost.querySelector('.card.border-warning');
                        if(parentCard){
                          parentCard.classList.remove('border-warning');
                          parentCard.classList.add('border-success');
                          parentCard.innerHTML = `<div class="card-body p-2 d-flex align-items-center gap-2">
                            <span class="badge bg-success">✓</span>
                            <span class="small">All requested corrections were submitted. Awaiting review.</span>
                          </div>`;
                        }
                        // Auto-refresh main table row status if backend advanced it
                        try {
                          if(lastResponse && (lastResponse.updated_status || (lastResponse.remaining && lastResponse.remaining.length===0))){
                            const row = document.querySelector(`tr[data-status][data-resubmit-files][data-status], tr[data-status]`+` td:first-child`);
                          }
                        } catch(_e){}
                        // More precise: find row by submission code
                        try {
                          const row = [...document.querySelectorAll('tr')].find(r=> r.querySelector('td') && r.querySelector('td').textContent.trim()===submissionCode);
                          if(row){
                            // Update internal data-status attribute if provided
                            if(lastResponse && lastResponse.updated_status){
                              row.setAttribute('data-status', lastResponse.updated_status);
                            }
                            // Remove resubmit attribute so modal won't show form again
                            row.removeAttribute('data-resubmit-files');
                            // Update the visible Remarks cell (4th cell in pending table)
                            const cells = row.querySelectorAll('td');
                            if(cells.length >= 4){
                              let newLabel = 'Pending Review';
                              if(lastResponse && lastResponse.updated_status){
                                const us = lastResponse.updated_status.toLowerCase();
                                if(us === 'pending_review') newLabel = 'Pending Review';
                                else if(us === 'under_review') newLabel = 'Under Review';
                                else if(us === 'approved') newLabel = 'Approved';
                                else if(us === 'completed') newLabel = 'Complete';
                              }
                              cells[3].textContent = newLabel;
                            }
                            // Row highlight flash
                            row.classList.add('table-success');
                            setTimeout(()=>{ row.classList.remove('table-success'); }, 1800);
                          }
                        } catch(err){ console.warn('Row update failed', err); }

                        // Also clear the resubmit hint on the triggering button so reopening shows only the success banner
                        try {
                          if (btn && btn.hasAttribute('data-resubmit-files')) {
                            btn.removeAttribute('data-resubmit-files');
                          }
                        } catch(_) { /* ignore */ }

                        // Close modal and reload the page to reflect latest status everywhere
                        setTimeout(() => {
                          try {
                            if (window.bootstrap && window.bootstrap.Modal && detailsModalEl) {
                              const m = window.bootstrap.Modal.getInstance(detailsModalEl) || window.bootstrap.Modal.getOrCreateInstance(detailsModalEl);
                              m.hide();
                            }
                          } catch(_) { /* ignore */ }
                          window.location.reload();
                        }, 1200);
                      } else {
                        // Re-enable submit if there were failures allowing retry of failed ones
                        btnSubmit.disabled = false;
                        if(failCount>0){
                          btnSubmit.querySelector('.submit-text').textContent = 'Retry Failed';
                        }
                      }
                      return;
                    }
                    const input = inputs[idx];
                    const docType = input.getAttribute('data-doc-type');
                    const file = input.files[0];
                    const row = input.closest('.reupload-row');
                    const badge = row.querySelector('.doc-status');
                    badge.textContent='…';
                    badge.className='badge bg-secondary doc-status';
                    badge.title='Uploading';
                    const fd = new FormData();
                    fd.append('submission_code', submissionCode);
                    fd.append('doc_type', docType);
                    fd.append('file', file);
                    fetch('reupload_document.php', {method:'POST', body:fd})
                      .then(r=>r.json())
                      .then(data=>{
                        lastResponse = data; // capture last response for final status update
                        if(data.success){
                          badge.textContent='✓';
                          badge.className='badge bg-success doc-status';
                          badge.title='Uploaded';
                          const match = detailsContent.querySelector(`[data-doc-type="${docType}"]`);
                          if(match){ match.classList.add('reupload-highlight'); }
                          successCount++;
                          // track completed doc
                          state.done.add(docType);
                          persistState();
                        } else {
                          badge.textContent='✗';
                          badge.className='badge bg-danger doc-status';
                          badge.title=data.error||'Failed';
                          failCount++;
                        }
                      })
                      .catch(err=>{
                        console.error('Upload error', err);
                        badge.textContent='✗';
                        badge.className='badge bg-danger doc-status';
                        badge.title='Network error';
                        failCount++;
                      })
                      .finally(()=>{
                        done++; progress.textContent = `Uploading ${done}/${inputs.length}...`; uploadNext(idx+1);
                      });
                  };
                  // ensure cache object exists for this submission
                  if(!window._reuploadState[submissionCode]){
                    window._reuploadState[submissionCode] = state;
                  }
                  uploadNext(0);
                });
              } else if (controlHost) {
                // In Approved/Completed, do not show reupload UI
                controlHost.innerHTML = '';
              }
            } else if (controlHost && cachedState && cachedState.locked) {
              // No active resubmission request; show success-only banner only in Pending tab
              if(inPendingTab){
                controlHost.innerHTML = `<div class="card border-success"><div class="card-body p-2 d-flex align-items-center gap-2">
                  <span class="badge bg-success">✓</span>
                  <span class="small">All requested corrections were submitted. Awaiting review.</span>
                </div></div>`;
              } else {
                controlHost.innerHTML = '';
              }
            }
          })
          .catch(() => {
            detailsContent.innerHTML =
              '<div class="alert alert-danger">Failed to load details.</div>';
          })
        ;
      });
    });
  }

  // Subtle Notes collapse/expand (delegated)
  document.addEventListener('click', function(e){
    const t = e.target.closest('.notes-toggle');
    if(!t) return;
    e.preventDefault();
    const sect = document.getElementById('notesSection');
    if(!sect) return;
    const hidden = sect.classList.contains('d-none');
    sect.classList.toggle('d-none', !hidden);
    // Toggle icon and aria
    const icon = t.querySelector('.toggle-icon');
    if(icon){ icon.textContent = hidden ? '−' : '+'; }
    t.setAttribute('aria-expanded', hidden ? 'true' : 'false');
  });

  // --- Author details (delegated) ---
  const authorModalEl = document.getElementById('authorDetailsModal');
  const authorContent = document.getElementById('authorDetailsContent');

  if (authorModalEl && authorContent) {
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.author-details-btn'); // check if clicked element is a button
      if (!btn) return; // not our button

      let author = null;
      try {
        author = JSON.parse(btn.getAttribute('data-author'));
      } catch (err) {
        console.error('Invalid author data:', err);
        return;
      }

      let html = `
        <ul class="list-unstyled">
          <li><strong>Name:</strong> ${author.first_name || ''} ${author.middle_name || ''} ${author.last_name || ''}</li>
          ${author.student_id ? `<li><strong>Student Number:</strong> ${author.student_id}</li>` : ''}
          ${author.webmail ? `<li><strong>Email:</strong> ${author.webmail}</li>` : ''}
          ${author.mobile ? `<li><strong>Mobile:</strong> ${author.mobile}</li>` : ''}
          ${author.home_address ? `<li><strong>Address:</strong> ${author.home_address}</li>` : ''}
          <li><strong>Role:</strong> ${author.is_adviser ? 'Adviser' : 'Author'}</li>
        </ul>
      `;

      authorContent.innerHTML = html;

      // Close submission modal before showing author modal
      if (detailsModalEl) {
        const detailsModal = bootstrap.Modal.getOrCreateInstance(detailsModalEl);
        detailsModalEl.addEventListener('hidden.bs.modal', function handler() {
          detailsModalEl.removeEventListener('hidden.bs.modal', handler);
          const authorModal = bootstrap.Modal.getOrCreateInstance(authorModalEl);
          authorModal.show();
        });
        detailsModal.hide();
      }
    });
  }

  // --- Toggle author details ---
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-author-details');
    if (btn) {
      const idx = btn.getAttribute('data-idx');
      const detailsDiv = document.getElementById('author-details-' + idx);
      if (detailsDiv) {
        const isShown = detailsDiv.style.display === 'block';
        detailsDiv.style.display = isShown ? 'none' : 'block';
        btn.textContent = isShown ? 'Show Details' : 'Hide Details';
      }
    }
  });

  // --- Notes: submit handler (delegated) ---
  document.addEventListener('submit', function(e){
    const form = e.target.closest('#noteForm');
    if(!form) return;
    e.preventDefault();
    const btn = form.querySelector('#noteSubmitBtn');
    const textarea = form.querySelector('#noteText');
    const hint = form.querySelector('#noteHint');
    const list = document.getElementById('notesList');
    const empty = document.getElementById('notesEmpty');
    const data = new FormData(form);
    const note = (textarea.value || '').trim();
    if(note.length === 0){
      hint.textContent = 'Note cannot be empty.';
      return;
    }
    if(note.length > 1000){
      hint.textContent = 'Note exceeds 1000 characters.';
      return;
    }
    btn.disabled = true; hint.textContent = 'Sending…';
    fetch('add_note.php', { method:'POST', body:data })
      .then(r=> r.json())
      .then(res => {
        if(!res || !res.success){
          const msg = (res && res.error) ? res.error : 'Failed to send note.';
          hint.textContent = msg;
          return;
        }
        // Prepend note to the list
        if(empty){ empty.classList.add('d-none'); }
        if(list){
          list.classList.remove('d-none');
          const li = document.createElement('li');
          li.className = 'list-group-item';
          const created = (res.note && res.note.created_at) ? res.note.created_at : new Date().toISOString().slice(0,19).replace('T',' ');
          const text = (res.note && res.note.note) ? res.note.note : note;
          li.innerHTML = `<div class="small text-muted">${created}</div><div class="mt-1" style="white-space:pre-wrap; word-wrap:break-word;"></div>`;
          li.querySelector('div.mt-1').textContent = text;
          list.prepend(li);
        }
        textarea.value = '';
        const remaining = (res.limits && (res.limits.per_submission_daily_remaining ?? null))
          ? ` Remaining today for this submission: ${res.limits.per_submission_daily_remaining}.` : '';
        hint.textContent = 'Note sent.' + remaining;
      })
      .catch(err => {
        console.error('Note submit error', err);
        hint.textContent = 'Network error. Please try again.';
      })
      .finally(()=>{ btn.disabled = false; setTimeout(()=>{ hint.textContent=''; }, 4000); });
  });

  // --- Request ID modal logic ---
  document.addEventListener('click', async function(e){
    const btn = e.target.closest('.btn-request-id');
    if(!btn) return;
    e.preventDefault();
    const rid = btn.getAttribute('data-request-id') || '';
    const rname = btn.getAttribute('data-student-name') || '';
    const rdate = btn.getAttribute('data-request-date') || '';
    const ridModal = document.getElementById('requestIdModal');
    if(!ridModal){ console.warn('requestIdModal not found'); return; }
    const valEl = document.getElementById('rid_value');
    const nameEl = document.getElementById('rid_name');
    const dateEl = document.getElementById('rid_date');
    if(valEl) valEl.textContent = rid;
    if(nameEl) nameEl.textContent = rname || '—';
    if(dateEl) dateEl.textContent = rdate || '—';

    // Fetch authoritative name from server if missing, then update only the Name field
    try {
      if (!rname && nameEl) {
        const res = await fetch(`get_request_name.php?code=${encodeURIComponent(rid)}`);
        const data = await res.json();
        if (data && data.success && typeof data.name === 'string' && data.name.trim() !== '') {
          nameEl.textContent = data.name.trim();
        }
      }
    } catch(_) { /* Silent: leave fallback */ }
    try {
      const m = bootstrap.Modal.getOrCreateInstance(ridModal);
      m.show();
    } catch(err){
      ridModal.style.display='block';
    }
  });

  // Download as PDF handler (prefer direct PDF generation; fallback to print if unavailable)
  document.addEventListener('click', function(e){
    const dlBtn = e.target.closest('#downloadRequestIdBtn');
    if(!dlBtn) return;
    e.preventDefault();
    const card = document.getElementById('requestIdCard');
    if(!card) return;
    // Build validation link using current Request ID (prefer signed URL from server)
    const ridText = (document.getElementById('rid_value')?.textContent || '').trim();
    const buildFallbackValidateUrl = () => {
      try {
        const origin = window.location.origin;
        return origin + '/Capstone/validate_ticket.php?code=' + encodeURIComponent(ridText || '');
      } catch(_) { return ''; }
    };
    const validateUrlPromise = (async () => {
      if(!ridText) return '';
      try {
        const r = await fetch(`ticket_token.php?code=${encodeURIComponent(ridText)}`);
        const d = await r.json();
        if(d && d.success && d.verifyUrl){ return d.verifyUrl; }
      } catch(_) { /* ignore and fallback */ }
      return buildFallbackValidateUrl();
    })();

    // Helper to load html2pdf once
    const loadScriptOnce = (src) => new Promise((resolve, reject) => {
      if (window.html2pdf) return resolve();
      const s = document.createElement('script');
      s.src = src; s.async = true; s.onload = () => resolve(); s.onerror = () => reject(new Error('load failed'));
      document.head.appendChild(s);
    });

    const buildWrapper = (validateUrl) => {
      const wrap = document.createElement('div');
      wrap.style.fontFamily = 'Arial,Helvetica,sans-serif';
      wrap.style.fontSize = '14px';
      wrap.style.width = '520px';
      wrap.style.margin = '0 auto';
      wrap.innerHTML = '<div style="border:1px solid #000;padding:16px;">' + card.innerHTML + (validateUrl ? `<p style="margin-top:8px;"><strong>Validation link:</strong> <span style="color:#555;">${validateUrl}</span></p>` : '') + '</div>';
      return wrap;
    };

    // Resolve validate URL first, then try direct PDF generation (no print dialog)
    validateUrlPromise.then((validateUrl) =>
      loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js')
        .then(() => {
          const wrapper = buildWrapper(validateUrl);
          document.body.appendChild(wrapper);
          const filename = 'RequestID-' + (ridText || 'ticket') + '.pdf';
          const opt = { margin: 10, filename, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2, useCORS: true }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } };
          return window.html2pdf().set(opt).from(wrapper).save().finally(() => { try { document.body.removeChild(wrapper); } catch(_) {} });
        })
        .catch(() => {
          // Fallback: print via hidden iframe (user can choose Save as PDF)
          const html = `<!DOCTYPE html><html><head><title>Request ID</title><style>body{font-family:Arial,Helvetica,sans-serif;margin:40px;} .card{border:1px solid #000;padding:16px;max-width:520px;} h1{font-size:18px;text-align:center;margin:0 0 12px;} p{margin:4px 0;font-size:14px;} .muted{color:#555;} .mt{margin-top:8px;} </style></head><body><div class=\"card\">`
            + card.innerHTML
            + (validateUrl ? `<p class=\"mt\"><strong>Validation link:</strong> <span class=\"muted\">${validateUrl}</span></p>` : '')
            + `</div></body></html>`;
          const iframe = document.createElement('iframe');
          iframe.style.position = 'fixed'; iframe.style.right = '0'; iframe.style.bottom = '0'; iframe.style.width = '0'; iframe.style.height = '0'; iframe.style.border = '0';
          document.body.appendChild(iframe);
          const doc = iframe.contentWindow || iframe.contentDocument;
          const docEl = doc.document || doc;
          docEl.open(); docEl.write(html); docEl.close();
          setTimeout(() => {
            try { (iframe.contentWindow || iframe).focus(); (iframe.contentWindow || iframe).print(); } catch(_) {}
            setTimeout(() => { try { document.body.removeChild(iframe); } catch(_) {} }, 1000);
          }, 300);
        })
    );
  });

});
