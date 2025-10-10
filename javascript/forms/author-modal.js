// Author modal and dynamic authors handling
document.addEventListener('DOMContentLoaded', function () {
  const mainForm = document.getElementById('submissionForm');
  const authorForm = document.getElementById('authorForm');
  const authorsList = document.getElementById('authorsList');
  const adviserCheckbox = document.getElementById('adviser_Coauthor');
  const adviserInput = document.querySelector('[name="adviser"]');
  const modalEl = document.getElementById('authorModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl); // to show/hide modal

  let authors = []; // Only user-added authors (not adviser)
  let adviser_Coauthor = null; // Adviser as co-author object or null
  let adviserExistingIdx = -1; // index in authors[] if adviser maps to existing

  // --- Name normalization & matching helpers ---
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
    // build full name and simple first+last variants
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

  // --- Helpers: Title Case for names ---
  function titleCase(str) {
    if (!str) return '';
    // Normalize whitespace and lowercase, then capitalize word starts
    const lowered = str.toLowerCase().replace(/\s+/g, ' ').trim();
    // Handle hyphenated and apostrophe names as separate word boundaries
    return lowered.replace(/\b([a-z])/g, (m, c) => c.toUpperCase())
                  .replace(/-([a-z])/g, (m, c) => '-' + c.toUpperCase())
                  .replace(/'([a-z])/g, (m, c) => "'" + c.toUpperCase());
  }

  // Auto-format name inputs in the modal
  if (authorForm) {
    ['first_name','middle_name','last_name'].forEach((n) => {
      const el = authorForm.querySelector(`[name="${n}"]`);
      if (el) {
        el.addEventListener('blur', () => { el.value = titleCase(el.value); });
      }
    });
  }
  // Auto-format adviser field too
  if (adviserInput) {
    adviserInput.addEventListener('blur', () => {
      adviserInput.value = titleCase(adviserInput.value);
      // If currently checked, re-evaluate mapping to avoid duplicates
      if (adviserCheckbox && adviserCheckbox.checked) {
        handleAdviserCheckboxChange(true);
      }
    });
  }

  // --- Email validation ---
  function isValidPupWebmail(email) {
    const studentPattern = /^[a-z]+(\.[a-z]+)?@iskolarngbayan\.pup\.edu\.ph$/i;
    const facultyPattern = /^[a-z]+(\.[a-z]+)?@pup\.edu\.ph$/i;
    return studentPattern.test(email) || facultyPattern.test(email);
  }

  // --- Save Author Button ---
  document.getElementById('saveAuthorBtn').addEventListener('click', function () {
    // Validate form
    if (!authorForm.checkValidity()) {
      authorForm.classList.add('was-validated');
      return;
    }

    // Validate student number
    const studentId = authorForm.querySelector('[name="student_id"]').value.trim();
    if (!/^\d{4}-\d{5}-[A-Z]{2}-\d{1}$/.test(studentId)) {
      alert('Invalid student number format. Use YYYY-XXXXX-XX-X');
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

    // If validation passes, create author object
    const coauthor = {
      first_name: titleCase(authorForm.querySelector('[name="first_name"]').value.trim()),
      middle_name: titleCase(authorForm.querySelector('[name="middle_name"]').value.trim()),
      last_name: titleCase(authorForm.querySelector('[name="last_name"]').value.trim()),
      student_id: studentId,
      mobile: mobile,
      webmail: webmail,
      home_address: authorForm.querySelector('[name="home_address"]').value.trim(),
      is_adviser: false,
    };

    authors.push(coauthor);

    // If adviser is checked and this co-author matches adviser name, mark it as adviser and drop separate adviser entry
    if (adviserCheckbox && adviserCheckbox.checked && adviserInput && adviserInput.value.trim()) {
      const newIdx = authors.length - 1;
      const aName = authorDisplayName(authors[newIdx]);
      if (matchesAdviserByName(aName, adviserInput.value.trim())) {
        // clear others
        authors.forEach((a,i)=>{ a.is_adviser = (i===newIdx); });
        adviserExistingIdx = newIdx;
        adviser_Coauthor = null;
      }
    }
    updateAuthorsList();
    updateHiddenFields();

    // Close modal and reset form
    modal.hide();
    authorForm.reset();
    authorForm.classList.remove('was-validated');
  });

  // --- Adviser as co-author toggle ---
  if (adviserCheckbox && adviserInput) {
    adviserCheckbox.addEventListener('change', function () {
      handleAdviserCheckboxChange(this.checked);
    });
  }

  function handleAdviserCheckboxChange(checked){
    if (checked) {
      const adviserName = adviserInput.value.trim();
      if (!adviserName) {
        alert('Please enter the adviser name first.');
        adviserCheckbox.checked = false;
        return;
      }
      // Try to map to an existing co-author
      adviserExistingIdx = -1;
      for (let i=0;i<authors.length;i++){
        const aName = authorDisplayName(authors[i]);
        if (matchesAdviserByName(aName, adviserName)) { adviserExistingIdx = i; break; }
      }
      if (adviserExistingIdx >= 0) {
        // mark exactly one as adviser
        authors.forEach((a,i)=>{ a.is_adviser = (i===adviserExistingIdx); });
        adviser_Coauthor = null;
      } else {
        // Create separate adviser entry
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
        // Ensure no authors flagged as adviser
        authors.forEach((a)=>{ a.is_adviser = false; });
      }
    } else {
      // Uncheck: remove adviser flag from existing and drop separate entry
      if (adviserExistingIdx >= 0 && authors[adviserExistingIdx]) {
        authors[adviserExistingIdx].is_adviser = false;
      }
      adviserExistingIdx = -1;
      adviser_Coauthor = null;
    }
    updateAuthorsList();
    updateHiddenFields();
  }

  // --- Authors list rendering ---
  function updateAuthorsList() {
    authorsList.innerHTML = ''; // clear

    // Combine adviser (if co-author) and authors for display
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

      // Only allow removing non-adviser authors
      if (!author.is_adviser) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-link text-danger ms-2 p-0';
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', () => {
          // Remove from authors array by index (skip adviser separate entry)
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

  // --- Hidden fields for form submission ---
  function updateHiddenFields() {
    // Remove existing coauthor fields
    mainForm.querySelectorAll('input[name^="coauthors["]').forEach((el) => el.remove());

    // Combine adviser (if co-author) and authors for hidden fields
    const allAuthors = [];
    if (adviser_Coauthor) allAuthors.push(adviser_Coauthor);
    authors.forEach((a) => allAuthors.push(a));

    allAuthors.forEach((author, index) => {
      Object.entries(author).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `coauthors[${index}][${key}]`;
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
