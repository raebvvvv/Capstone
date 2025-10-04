(function(){
  'use strict';

  function escapeHTML(str){ return String(str==null?'':str).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c];}); }

  function formatSize(bytes){ if(!bytes && bytes!==0) return ''; const units=['B','KB','MB','GB']; let i=0; let v=bytes; while(v>=1024 && i<units.length-1){ v/=1024; i++; } return v.toFixed(v>=10||i===0?0:1)+' '+units[i]; }

  function formatDateText(dstr){
    try{
      if(!dstr) return '';
      // Normalize common YYYY-MM-DD values
      const iso = String(dstr).trim();
      const d = new Date(iso);
      if(!isNaN(d.getTime())){
        return d.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'2-digit' });
      }
      return iso; // fallback to original if parse fails
    }catch(_){ return String(dstr); }
  }

  function buildFilesHTML(details, options){
    const detailedList = Array.isArray(details.files_list) ? details.files_list : [];
    const legacyMap = details.files || {};
    const flaggedSet = new Set((options && Array.isArray(options.flaggedTypes)) ? options.flaggedTypes.map(s=>String(s).toLowerCase()) : []);
    const rows = (detailedList.length ? detailedList : Object.keys(legacyMap).map(k=>({ label: k.replace(/[-_]/g,' ').replace(/\b\w/g,c=>c.toUpperCase()), url: legacyMap[k], type:k })))
      .map(file=>{
        const hasUrl = !!file.url;
        const safeUrl = hasUrl ? file.url : '#';
        const sizePart = file.size ? `<span class="text-muted ms-2 small">${formatSize(file.size)}</span>` : '';
        const verifiedBadge = (file.verified===1 || file.verified===true) ? `<span class="badge bg-success ms-2">Verified</span>` : '';
        const label = escapeHTML(file.label || file.type || 'File');
        const dtype = String(file.type||'').toLowerCase();
        const needsReup = flaggedSet.has(dtype) ? `<span class="badge bg-warning text-dark ms-2">Needs reupload</span>` : '';
        return `<li class="d-flex justify-content-between align-items-center mb-2 flex-wrap" data-doc-type="${escapeHTML(file.type||'')}">
          <div><span>${label}</span>${sizePart}${verifiedBadge}${needsReup}</div>
          <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a class="btn btn-download btn-sm" href="${safeUrl}" ${hasUrl? 'download': 'class="disabled" aria-disabled="true" tabindex="-1"'}>Download File</a>
            <a class="btn btn-view-file btn-sm" href="${safeUrl}" target="_blank" rel="noopener" ${hasUrl? '': 'class="disabled" aria-disabled="true" tabindex="-1"'}>View File</a>
          </div>
        </li>`;
      }).join('');
    return `<ul class="list-unstyled mb-0">${rows || '<li class="text-muted fst-italic">No files uploaded.</li>'}</ul>`;
  }

  function buildAuthorsHTML(details, options){
    const add = Array.isArray(details.additionalAuthors)? details.additionalAuthors: [];
    if(!add.length && !details.adviser) return '';
  const isAdv = (a)=> !!(a && a.is_adviser);
    const rows = add.map((a,idx)=>{
      const badge = isAdv(a) ? '<span class="badge bg-warning text-dark ms-2">Adviser</span>' : '';
      const name = escapeHTML(a.name || '—');
      const btn = options && options.onAuthorDetails ? `<button type="button" class="btn btn-success btn-sm rounded-pill px-3 ms-2 author-view-btn" data-author-index="${idx}">View Details</button>` : '';
      return `<div class="author-entry">${name}${badge}${btn}</div>`;
    }).join('');
  const hasAdviserInAuthors = add.some(isAdv);
  const adviserRaw = (details.adviser || '').toString().trim();
  const adviserName = (!hasAdviserInAuthors && adviserRaw) ? `<p><strong>Adviser:</strong> ${escapeHTML(adviserRaw)}</p>` : '';
    const authorsBlock = rows ? `<div class="mt-2"><div class="fw-semibold mb-1">Additional Author(s)</div>${rows}</div>` : '';
    return adviserName + authorsBlock;
  }

  function render(containerEl, details, opts){
    const o = Object.assign({ role: 'admin', showNotes: false, onAuthorDetails: null }, opts||{});
    if(!containerEl) return;
    containerEl.classList.remove('text-center');
    containerEl.classList.add('text-start');
    const v = (x,d='—') => (x==null||x==='')?d:x;

    const studentName = v(details.studentName);
    const num = v(details.studentNumber);
    const email = v(details.email);
    const addr = v(details.homeAddress);
    const campus = v(details.campus);
    const college = v(details.college);
    const program = v(details.program);
    const level = v(details.academicLevel);

    const title = v(details.documentTitle);
    const wc = v(details.workClassification);
  const accDate = v(details.accomplishmentDate);

    // For user role, highlight/label files flagged for resubmission
    let flaggedTypes = [];
    try {
      const hint = (o.flaggedTypes || details.flaggedTypes || details.resubmitTypes || details.resubmitRaw || '').toString();
      if (hint) { flaggedTypes = hint.split('|').map(s=>s.trim()).filter(Boolean); }
    } catch(_){}
    const filesHTML = buildFilesHTML(details, { flaggedTypes });
    const authorsHTML = buildAuthorsHTML(details, { onAuthorDetails: !!o.onAuthorDetails });

  const bannerText = (o.role === 'user' && details.statusBanner && String(details.statusBanner).trim() !== '') ? String(details.statusBanner) : '';
  const statusHTML = bannerText ? `<div class="alert alert-success mb-3">${escapeHTML(bannerText)}</div>` : '';
    const notesHTML = o.showNotes ? `<div class="mt-3" id="sharedNotesHost"></div>` : '';

    // Determine labels based on role/page: Employees should see Employee ID/Number and Employee Information
    const isEmployeeContext = (String(o.role||'').toLowerCase()==='employee') || (document.body && document.body.getAttribute('data-user-kind')==='employee');
    const infoHeader = isEmployeeContext ? 'Employee Information' : 'Student Information';
    const idLabel = isEmployeeContext ? 'Employee ID/Number' : 'Student Number';

    containerEl.innerHTML = `
      ${statusHTML}
      <div>
        <h5>${escapeHTML(infoHeader)}</h5>
        <p><strong>Name:</strong> ${escapeHTML(studentName)}</p>
        <p><strong>${escapeHTML(idLabel)}:</strong> ${escapeHTML(num)}</p>
        <p><strong>Email Address:</strong> ${escapeHTML(email)}</p>
        <p><strong>Home Address:</strong> ${escapeHTML(addr)}</p>
        <p><strong>Campus:</strong> ${escapeHTML(campus)}</p>
        <p><strong>College:</strong> ${escapeHTML(college)}</p>
        <p><strong>Program:</strong> ${escapeHTML(program)}</p>
        <p><strong>Academic Level:</strong> ${escapeHTML(level)}</p>
      </div>
      <div class="mt-3">
        <h5>Document Information</h5>
        <p><strong>Title:</strong> ${escapeHTML(title)}</p>
        <p><strong>Type (Work Classification):</strong> ${escapeHTML(wc)}</p>
        <p><strong>Author/s Full name/s:</strong> ${escapeHTML(studentName)}</p>
  ${accDate ? `<p><strong>Date Accomplished:</strong> ${escapeHTML(formatDateText(accDate))}</p>` : ''}
        ${authorsHTML}
      </div>
      <div class="mt-3">
        <h5>Uploaded Files</h5>
        ${filesHTML}
      </div>
      ${notesHTML}
    `;

    if (o.onAuthorDetails) {
      containerEl.querySelectorAll('.author-view-btn').forEach(btn=>{
        btn.addEventListener('click',()=>{
          const idx = parseInt(btn.getAttribute('data-author-index'),10);
          o.onAuthorDetails(idx);
        });
      });
    }
  }

  window.renderSubmissionDetails = render;
})();
