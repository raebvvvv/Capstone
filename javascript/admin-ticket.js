// Extracted from admin/ticket.php inline script for CSP compliance
// All logic wrapped to avoid global leakage except needed functions attached intentionally.

(function(){
  'use strict';

  // Utility escape
  function escapeHTML(str){ return String(str).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];}); }
  window.escapeHTML = escapeHTML; // if needed elsewhere

  let currentDetails = {}; let currentAuthorIndex = null;

  function renderDetails(details, editMode){
    editMode = !!editMode;
    const v = (x,d='') => (x===undefined||x===null?d:x);
    const files = details.files || {};
    const items = [
      ['Record of Copyright Application', files.recordOfCopyrightApplication || ''],
      ['Journal Publication Format', files.journalPublicationFormat || ''],
      ['Notarized Copyright Application Form', files.notarizedCopyrightApplicationForm || ''],
      ['Receipt of Payment', files.receiptOfPayment || ''],
      ['Full Manuscript', files.fullManuscript || ''],
      ['Approval Sheet', files.approvalSheet || ''],
      ['Notarized Co-Authorship', files.notarizedCoAuthorship || ''],
    ];
    const attachRow = (label,url)=>{ const hasUrl=!!url; const safeUrl=hasUrl?url:'#'; const disabledAttrs=hasUrl?'class=""':'class="disabled" aria-disabled="true" tabindex="-1"'; return `<li class="d-flex justify-content-between align-items-center mb-2"><span>${label}</span><div class="d-flex gap-2"><a class="btn btn-download btn-sm" href="${safeUrl}" ${hasUrl? 'download': disabledAttrs}>Download File</a><a class="btn btn-view-file btn-sm" href="${safeUrl}" target="_blank" rel="noopener" ${hasUrl? '' : disabledAttrs}>View File</a></div></li>`; };
    const attachmentsHTML = `<ul class="list-unstyled mb-0">${items.map(i=>attachRow(i[0],i[1])).join('')}</ul>`;

    if(!Array.isArray(details.additionalAuthors)){
      const raw=(details.authorName||'').trim();
      const names = raw ? raw.split(/\s*,\s*|\s*;\s*|\s*\n\s*/).filter(Boolean):[];
      const primary = details.studentName || names.shift() || '';
      details.studentName = primary;
      details.additionalAuthors = names.filter(n=>n && n!==primary).map(n=>({ name:n, studentNumber:'', email:'', address:'', phone:'', campus:'', department:'', college:'', program:'' }));
    }
    if(!details.additionalAuthors) details.additionalAuthors=[];
    if(!details._exampleSeeded && details.additionalAuthors.length===0){
      details.additionalAuthors.push({ name:'Jane Doe', studentNumber:'2022-08860-MN-0', email:'jane@iskolarngbayan.pup.edu.ph', address:'', phone:'', campus:'PUP Sta. Mesa, Manila', department:'DIT', college:'CCIS', program:'Bachelor of Science In Information Technology' });
      details._exampleSeeded = true;
    }

    const authorsListHTML = (details.additionalAuthors||[]).map((a,idx)=>`<div class="author-entry"><span class="author-name">${a.name||'—'}</span><button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2 author-view-btn" data-author-index="${idx}">View Details</button></div>`).join('');
    const modalBody = document.getElementById('detailsModalBody');
    if(!modalBody) return;

    modalBody.innerHTML = `<div><h5>Student Information</h5>`+
      `<p><strong>Name:</strong> ${editMode? `<input type='text' id='editStudentName' value='${escapeHTML(v(details.studentName,''))}' />` : escapeHTML(v(details.studentName,'—'))}</p>`+
      `<p><strong>Student Number:</strong> ${editMode? `<input type='text' id='editStudentNumber' value='${escapeHTML(v(details.studentNumber,''))}' />` : escapeHTML(v(details.studentNumber,'—'))}</p>`+
      `<p><strong>Email Address:</strong> ${editMode? `<input type='email' id='editEmail' value='${escapeHTML(v(details.email,''))}' />` : escapeHTML(v(details.email,'—'))}</p>`+
      `<p><strong>Home Address:</strong> ${editMode? `<input type='text' id='editHomeAddress' value='${escapeHTML(v(details.homeAddress,''))}' />` : escapeHTML(v(details.homeAddress,'—'))}</p>`+
      `<p><strong>Campus:</strong> ${editMode? `<input type='text' id='editCampus' value='${escapeHTML(v(details.campus,''))}' />` : escapeHTML(v(details.campus,'—'))}</p>`+
      `<p><strong>Department:</strong> ${editMode? `<input type='text' id='editDepartment' value='${escapeHTML(v(details.department,''))}' />` : escapeHTML(v(details.department,'—'))}</p>`+
      `<p><strong>College:</strong> ${editMode? `<input type='text' id='editCollege' value='${escapeHTML(v(details.college,''))}' />` : escapeHTML(v(details.college,'—'))}</p>`+
      `<p><strong>Program:</strong> ${editMode? `<input type='text' id='editProgram' value='${escapeHTML(v(details.program,''))}' />` : escapeHTML(v(details.program,'—'))}</p></div>`+
      `<div><h5>Document Information</h5>`+
      `<p><strong>Title:</strong> ${editMode? `<input type='text' id='editDocumentTitle' value='${escapeHTML(v(details.documentTitle,''))}' />` : escapeHTML(v(details.documentTitle,'—'))}</p>`+
      `<p><strong>Author/s Full name/s:</strong> ${escapeHTML(v(details.studentName,'—'))}</p>`+
      `${authorsListHTML ? `<div class="mt-2"><div class="fw-semibold mb-1">Additional Author(s)</div>${authorsListHTML}</div>`: ''}`+
      `<p class="mt-2"><strong>Date Accomplished:</strong> ${editMode? `<input type='date' id='editAccomplishmentDate' value='${escapeHTML(v(details.accomplishmentDate,''))}' />` : escapeHTML(v(details.accomplishmentDate,'—'))}</p></div>`+
      `<div class="mt-3"><h5>Uploaded Files</h5>${attachmentsHTML}</div>`;

    const editBtn = document.getElementById('editDetailsBtn');
    const saveBtn = document.getElementById('saveDetailsBtn');
    if(editBtn && saveBtn){
      editBtn.style.display = editMode ? 'none':'inline-block';
      saveBtn.style.display = editMode ? 'inline-block':'none';
    }
    document.querySelectorAll('.author-view-btn').forEach(btn=>{
      btn.addEventListener('click',()=>{ const idx=parseInt(btn.getAttribute('data-author-index'),10); openAuthorModal(idx); });
    });
  }
  function showDetailsModal(row){
    const tr = row && row.tagName==='TR' ? row : (row.closest && row.closest('tr'));
    if(!tr) return;
    const tds = tr.querySelectorAll('td');
    const cell = i => (tds[i]? tds[i].textContent.trim(): '');
    const details = { studentName: cell(2), studentNumber: cell(1), email:'', homeAddress:'', campus:'', department:'', college:'', program: cell(4), documentTitle:'', authorName:'', accomplishmentDate: cell(5) };
    currentDetails = details;
    renderDetails(details,false);
    const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    detailsModal.show();
  }
  function renderAuthorModal(author, edit){
    const body = document.getElementById('authorInfoBody');
    if(!body) return;
    const v=(x)=> (x??'');
    if(!edit){
      body.innerHTML = `<div class="mb-3"><strong>Name:</strong> ${author.name || '—'}</div>`+
        `<div class="mb-2"><strong>Student Number:</strong> ${author.studentNumber || ''}</div>`+
        `<div class="mb-2"><strong>Email Address:</strong> ${author.email || ''}</div>`+
        `<div class="mb-2"><strong>Home Address:</strong> ${author.address || ''}</div>`+
        `<div class="mb-2"><strong>Phone Number:</strong> ${author.phone || ''}</div>`+
        `<div class="mb-2"><strong>Campus:</strong> ${author.campus || ''}</div>`+
        `<div class="mb-2"><strong>Department:</strong> ${author.department || ''}</div>`+
        `<div class="mb-2"><strong>College:</strong> ${author.college || ''}</div>`+
        `<div class="mb-2"><strong>Program:</strong> ${author.program || ''}</div>`;
    } else {
      body.innerHTML = `<div class="mb-2"><label class="form-label">Name</label><input class="form-control" id="editAuthorNameInput" value="${v(author.name)}"></div>`+
        `<div class="mb-2"><label class="form-label">Student Number</label><input class="form-control" id="editAuthorStudNoInput" value="${v(author.studentNumber)}"></div>`+
        `<div class="mb-2"><label class="form-label">Email Address</label><input type="email" class="form-control" id="editAuthorEmailInput" value="${v(author.email)}"></div>`+
        `<div class="mb-2"><label class="form-label">Home Address</label><input class="form-control" id="editAuthorAddressInput" value="${v(author.address)}"></div>`+
        `<div class="mb-2"><label class="form-label">Phone Number</label><input class="form-control" id="editAuthorPhoneInput" value="${v(author.phone)}"></div>`+
        `<div class="mb-2"><label class="form-label">Campus</label><input class="form-control" id="editAuthorCampusInput" value="${v(author.campus)}"></div>`+
        `<div class="mb-2"><label class="form-label">Department</label><input class="form-control" id="editAuthorDeptInput" value="${v(author.department)}"></div>`+
        `<div class="mb-2"><label class="form-label">College</label><input class="form-control" id="editAuthorCollegeInput" value="${v(author.college)}"></div>`+
        `<div class="mb-2"><label class="form-label">Program</label><input class="form-control" id="editAuthorProgramInput" value="${v(author.program)}"></div>`;
    }
    const editBtn = document.getElementById('authorEditBtn');
    const saveBtn = document.getElementById('authorSaveBtn');
    if(editBtn && saveBtn){
      editBtn.style.display = edit ? 'none':'inline-block';
      saveBtn.style.display = edit ? 'inline-block':'none';
    }
  }
  function openAuthorModal(index){
    currentAuthorIndex = index;
    const author = (currentDetails.additionalAuthors || [])[index] || {};
    renderAuthorModal(author,false);
    new bootstrap.Modal(document.getElementById('authorInfoModal')).show();
  }
  window.showDetailsModal = showDetailsModal; // if needed by HTML (currently triggered via listeners)

  function selectRemark(remark){ const dd=document.getElementById('remarksDropdown'); if(dd) dd.textContent=remark; }
  function selectRemarkActive(remark){ const dd=document.getElementById('remarksDropdownActive'); if(dd) dd.textContent=remark; }

  document.addEventListener('DOMContentLoaded', function(){
    // View buttons
    document.querySelectorAll('.btn-view').forEach(btn=>{ btn.addEventListener('click', e=>{ e.preventDefault(); showDetailsModal(btn.closest('tr')); }); });

    const editDetailsBtn = document.getElementById('editDetailsBtn');
    if(editDetailsBtn){ editDetailsBtn.addEventListener('click', ()=> renderDetails(currentDetails,true)); }
    const saveDetailsBtn = document.getElementById('saveDetailsBtn');
    if(saveDetailsBtn){ saveDetailsBtn.addEventListener('click', ()=>{ const updatedDetails = { request_id: currentDetails.request_id, student_name: document.getElementById('edit_name')?.value || '', student_id: document.getElementById('edit_number')?.value || '', email: document.getElementById('edit_email')?.value || '', program: document.getElementById('edit_program')?.value || '', }; fetch('../edit_ticket.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(updatedDetails) }).then(res=>res.json()).then(data=>{ if(data.success){ renderDetails(updatedDetails,false); alert('Details updated successfully!'); } else { alert('Update failed: '+(data.error || 'Unknown error')); } }); }); }

    // Incomplete (pending)
    document.querySelectorAll('#pending .btn-incomplete').forEach(btn=>{ btn.addEventListener('click', e=>{ e.preventDefault(); new bootstrap.Modal(document.getElementById('incompleteModal')).show(); }); });
    const confirmIncompleteBtn = document.getElementById('confirmIncompleteBtn');
    if(confirmIncompleteBtn){ confirmIncompleteBtn.addEventListener('click', ()=>{ bootstrap.Modal.getInstance(document.getElementById('incompleteModal'))?.hide(); }); }

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
      if(completeModalEl) new bootstrap.Modal(completeModalEl).show();
    });

    // Approve flow
  const approveCommentModalEl = document.getElementById('approveCommentModal');
    const approveCommentModal = approveCommentModalEl ? new bootstrap.Modal(approveCommentModalEl): null;
    const successModalEl = document.getElementById('successModal');
    const successModal = successModalEl ? new bootstrap.Modal(successModalEl): null;

    document.querySelectorAll('.btn-approve').forEach(btn=>{
      btn.addEventListener('click', e=>{
        e.preventDefault();
        const hiddenInput = btn.closest('form')?.querySelector('input[name="approve_ticket_id"]');
        const requestId = hiddenInput ? hiddenInput.value : '';
        const reqInput = document.getElementById('approveRequestId');
        if(reqInput) reqInput.value = requestId;
        const commentText = document.getElementById('approveCommentText');
        if(commentText) commentText.value = '';
        approveCommentModal && approveCommentModal.show();
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
        if(data.success){
          approveCommentModal && approveCommentModal.hide();
          const row = [...document.querySelectorAll('tr')].find(r=> r.firstElementChild && r.firstElementChild.textContent.trim() === requestId);
          if(row){ const statusCell = row.querySelector('td:nth-child(7)'); if(statusCell) statusCell.textContent = 'Approved'; }
          successModal && successModal.show();
        } else { alert(data.error || 'Approval failed'); }
      } catch(err){ alert('Network error'); }
    }); }

    // Certificate view
    document.addEventListener('click', e=>{ const trigger = e.target.closest('.btn-view-certificate'); if(!trigger) return; e.preventDefault(); const url = trigger.getAttribute('data-cert-url') || ''; const dl = document.getElementById('downloadCertificateBtn'); if(dl){ if(url){ dl.href = url; dl.setAttribute('download','certificate.pdf'); } else { dl.href='#'; dl.removeAttribute('download'); } } new bootstrap.Modal(document.getElementById('certificateModal')).show(); });

    // Incomplete Active
    document.addEventListener('click', e=>{ const trigger = e.target.closest('.btn-incomplete-active'); if(!trigger) return; e.preventDefault(); new bootstrap.Modal(document.getElementById('incompleteActiveModal')).show(); });
    const confirmIncompleteActiveBtn = document.getElementById('confirmIncompleteActiveBtn');
    if(confirmIncompleteActiveBtn){ confirmIncompleteActiveBtn.addEventListener('click', ()=>{ bootstrap.Modal.getInstance(document.getElementById('incompleteActiveModal'))?.hide(); }); }

    // Comments
    document.addEventListener('click', e=>{ const trigger = e.target.closest('.btn-comments'); if(!trigger) return; e.preventDefault(); let remarkText=''; const tr = trigger.closest('tr'); if(tr){ const tds = Array.from(tr.querySelectorAll('td')); if(tr.closest('#pending')){ remarkText = (tds[6]?.textContent || '').trim(); } else if(tr.closest('#approved')){ remarkText='No remarks available.'; } else if(tr.closest('#completed')){ remarkText = (tds[7]?.textContent || '').trim(); } } if(!remarkText) remarkText='No remarks available.'; const ta=document.getElementById('comment_text'); if(ta) ta.value = remarkText; new bootstrap.Modal(document.getElementById('commentModal')).show(); });
  });
})();
