// Employee Author modal and dynamic authors handling (mirrors student behavior)
document.addEventListener('DOMContentLoaded', function () {
  const mainForm = document.getElementById('submissionForm');
  const authorForm = document.getElementById('authorForm');
  const authorsList = document.getElementById('authorsList');
  const adviserCheckbox = document.getElementById('adviser_Coauthor');
  const adviserInput = document.querySelector('[name="adviser"]');
  const modalEl = document.getElementById('authorModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  let authors = []; // user-added authors (not adviser)
  let adviser_Coauthor = null; // adviser co-author object
  let adviserExistingIdx = -1;

  function normalizeName(s){
    return String(s||'')
      .replace(/\./g,'')
      .replace(/\s+/g,' ')
      .trim()
      .toLowerCase();
  }
  function firstLastOnlyFromStr(s){
    const p = normalizeName(s).split(' ').filter(Boolean);
    if(p.length===0) return '';
    if(p.length===1) return p[0];
    return p[0] + ' ' + p[p.length-1];
  }
  function authorDisplayName(a){
    const mid = (a.middle_name||'').trim();
    const full = `${a.first_name||''} ${mid?mid+' ':''}${a.last_name||''}`;
    return full.replace(/\s+/g,' ').trim();
  }
  function matchesAdviserByName(aName, adviserName){
    const advNorm = normalizeName(adviserName);
    const advSimple = firstLastOnlyFromStr(adviserName);
    const cNorm = normalizeName(aName);
    const cSimple = firstLastOnlyFromStr(aName);
    if(!cNorm) return false;
    return (advNorm && cNorm===advNorm) || (advSimple && cSimple===advSimple);
  }

  function titleCase(str) {
    if (!str) return '';
    const lowered = str.toLowerCase().replace(/\s+/g, ' ').trim();
    return lowered
      .replace(/\b([a-z])/g, (m, c) => c.toUpperCase())
      .replace(/-([a-z])/g, (m, c) => '-' + c.toUpperCase())
      .replace(/'([a-z])/g, (m, c) => "'" + c.toUpperCase());
  }

  if (authorForm) {
    ['first_name','middle_name','last_name'].forEach((n) => {
      const el = authorForm.querySelector(`[name="${n}"]`);
      if (el) {
        el.addEventListener('blur', () => { el.value = titleCase(el.value); });
      }
    });
  }
  if (adviserInput) {
    adviserInput.addEventListener('blur', () => {
      adviserInput.value = titleCase(adviserInput.value);
      if (adviserCheckbox && adviserCheckbox.checked) {
        handleAdviserCheckboxChange(true);
      }
    });
  }

  function isValidPupWebmail(email) {
    const studentPattern = /^[a-z]+(\.[a-z]+)?@iskolarngbayan\.pup\.edu\.ph$/i;
    const facultyPattern = /^[a-z]+(\.[a-z]+)?@pup\.edu\.ph$/i;
    return studentPattern.test(email) || facultyPattern.test(email);
  }

  document.getElementById('saveAuthorBtn').addEventListener('click', function () {
    if (!authorForm.checkValidity()) {
      authorForm.classList.add('was-validated');
      return;
    }

    // Validate Employee ID (5-digit)
    const empId = authorForm.querySelector('[name="student_id"]').value.trim();
    if (!/^\d{5}$/.test(empId)) {
      alert('Invalid employee number. Use 5 digits.');
      return;
    }

    // Validate mobile number
    const mobile = authorForm.querySelector('[name="mobile"]').value.trim();
    if (!/^09\d{9}$/.test(mobile)) {
      alert('Invalid mobile number format. Must start with 09 and have 11 digits');
      return;
    }

    // Validate webmail
    const webmail = authorForm.querySelector('[name="webmail"]').value.trim().toLowerCase();
    if (!isValidPupWebmail(webmail)) {
      alert(
        'Please enter a valid PUP webmail:\n' +
          '- Students: firstname.lastname@iskolarngbayan.pup.edu.ph\n' +
          '- Faculty: firstname.lastname@pup.edu.ph'
      );
      return;
    }

    const coauthor = {
      first_name: titleCase(authorForm.querySelector('[name="first_name"]').value.trim()),
      middle_name: titleCase(authorForm.querySelector('[name="middle_name"]').value.trim()),
      last_name: titleCase(authorForm.querySelector('[name="last_name"]').value.trim()),
      student_id: empId,
      mobile: mobile,
      webmail: webmail,
      home_address: authorForm.querySelector('[name="home_address"]').value.trim(),
      is_adviser: false,
    };

    authors.push(coauthor);
    if (adviserCheckbox && adviserCheckbox.checked && adviserInput && adviserInput.value.trim()) {
      const newIdx = authors.length - 1;
      const aName = authorDisplayName(authors[newIdx]);
      if (matchesAdviserByName(aName, adviserInput.value.trim())) {
        authors.forEach((a,i)=>{ a.is_adviser = (i===newIdx); });
        adviserExistingIdx = newIdx;
        adviser_Coauthor = null;
      }
    }
    updateAuthorsList();
    updateHiddenFields();
    modal.hide();
    authorForm.reset();
    authorForm.classList.remove('was-validated');
  });

  if (adviserCheckbox && adviserInput) {
    adviserCheckbox.addEventListener('change', function () { handleAdviserCheckboxChange(this.checked); });
  }

  function handleAdviserCheckboxChange(checked){
    if (checked) {
      const adviserName = adviserInput.value.trim();
      if (!adviserName) {
        alert('Please enter the adviser name first.');
        adviserCheckbox.checked = false;
        return;
      }
      adviserExistingIdx = -1;
      for (let i=0;i<authors.length;i++){
        const aName = authorDisplayName(authors[i]);
        if (matchesAdviserByName(aName, adviserName)) { adviserExistingIdx = i; break; }
      }
      if (adviserExistingIdx >= 0) {
        authors.forEach((a,i)=>{ a.is_adviser = (i===adviserExistingIdx); });
        adviser_Coauthor = null;
      } else {
        const nameParts = adviserName.split(' ');
        const firstName = titleCase(nameParts[0] || adviserName);
        const lastName = titleCase(nameParts.length > 1 ? nameParts.slice(-1)[0] : '');
        const middleName = nameParts.length > 2 ? nameParts.slice(1, -1).map((n) => n[0]).join('') : '';
        adviser_Coauthor = {
          first_name: firstName,
          middle_name: middleName,
          last_name: lastName,
          student_id: '', mobile: '', webmail: '', home_address: '',
          is_adviser: true,
        };
        authors.forEach((a)=>{ a.is_adviser = false; });
      }
    } else {
      if (adviserExistingIdx >= 0 && authors[adviserExistingIdx]) {
        authors[adviserExistingIdx].is_adviser = false;
      }
      adviserExistingIdx = -1;
      adviser_Coauthor = null;
    }
    updateAuthorsList();
    updateHiddenFields();
  }

  function updateAuthorsList() {
    authorsList.innerHTML = '';
    const allAuthors = [];
    if (adviser_Coauthor) allAuthors.push(adviser_Coauthor);
    authors.forEach((a) => allAuthors.push(a));

    if (allAuthors.length === 0) {
      authorsList.innerHTML = '<span class="text-muted">No authors added yet.</span>';
      return;
    }

    allAuthors.forEach((author, index) => {
      const badge = document.createElement('span');
      badge.className =
        'badge rounded-pill bg-light text-dark me-2 mb-2 px-3 py-2 d-inline-flex align-items-center border';
      badge.innerHTML = `<strong>${author.first_name} ${author.middle_name ? author.middle_name + '.' : ''} ${author.last_name}</strong>
                         ${author.is_adviser ? '<span class="ms-2 text-primary">Adviser</span>' : ''}`;

      if (!author.is_adviser) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-link text-danger ms-2 p-0';
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', () => {
          const offset = adviser_Coauthor ? 1 : 0;
          const realIdx = index - offset;
          if (realIdx >= 0) {
            authors.splice(realIdx, 1);
            if (adviserExistingIdx === realIdx) {
              adviserExistingIdx = -1;
              if (adviserCheckbox) adviserCheckbox.checked = false;
            } else if (adviserExistingIdx > realIdx) {
              adviserExistingIdx -= 1;
            }
          }
          updateAuthorsList();
          updateHiddenFields();
        });
        badge.appendChild(removeBtn);
      }

      authorsList.appendChild(badge);
    });
  }

  function updateHiddenFields() {
    mainForm.querySelectorAll('input[name^="coauthors["]').forEach((el) => el.remove());
    const allAuthors = [];
    if (adviser_Coauthor) allAuthors.push(adviser_Coauthor);
    authors.forEach((a) => allAuthors.push(a));

    allAuthors.forEach((author, index) => {
      Object.entries(author).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `coauthors[${index}][${key}]`;
        // Convert boolean is_adviser to numeric string for PHP
        if (key === 'is_adviser') {
          input.value = value ? '1' : '0';
        } else {
          input.value = value;
        }
        mainForm.appendChild(input);
      });
    });
  }
});
