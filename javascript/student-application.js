// student-application.js
// Global delegated handler (CSP-safe) for comments & remarks in case buttons
// are added after DOMContentLoaded or existing listener misses them.
document.addEventListener('click', function(e){
  const remarksBtn = e.target.closest('[data-remarks-btn]');
  const commentBtn = remarksBtn ? null : e.target.closest('.btn-comments');
  if(!remarksBtn && !commentBtn) return;
  e.preventDefault();
  const modalEl = document.getElementById('commentModal');
  const textarea = document.getElementById('comment_text');
  if(!modalEl || !textarea){
    console.warn('[student-application] Comment modal elements not found');
    return;
  }
  let text = '';
  if(remarksBtn){
    text = remarksBtn.getAttribute('data-remarks') || '';
  } else if(commentBtn){
    const tr = commentBtn.closest('tr');
    text = commentBtn.getAttribute('data-admin-comment') || (tr ? tr.getAttribute('data-admin-comment') : '') || '';
  }
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
        fetch('view-submission.php?code=' + encodeURIComponent(submissionCode) + '&modal=1')
          .then((response) => response.text())
          .then((html) => {
            detailsContent.innerHTML = html;
            const controlHost = detailsContent.querySelector('#reuploadControls');
            const cachedState = window._reuploadState[submissionCode];
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

              // Build reupload controls if container present
              if(controlHost && parts && parts.length){
                // Determine existing state and show banner if already locked for this signature
                let state = window._reuploadState[submissionCode];
                if(state && state.locked && state.sig === signature){
                  controlHost.innerHTML = `<div class="card border-success"><div class="card-body p-2 d-flex align-items-center gap-2">
                    <span class="badge bg-success">✓</span>
                    <span class="small">All requested corrections were submitted. Awaiting review.</span>
                  </div></div>`;
                  return; // no form needed
                }
                // Otherwise start a fresh cycle (reset state)
                state = { locked:false, done:new Set(), sig: signature };
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
              }
            }
            // Even if no resubmitRaw (attribute removed after backend update), show locked notice if previously completed
            if(controlHost && (!resubmitRaw) && cachedState && cachedState.locked){
              controlHost.innerHTML = `<div class="card border-success"><div class="card-body p-2 d-flex align-items-center gap-2">
                <span class="badge bg-success">✓</span>
                <span class="small">All requested corrections were submitted. Awaiting review.</span>
              </div></div>`;
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

});
