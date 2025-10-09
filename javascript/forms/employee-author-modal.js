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
    adviserInput.addEventListener('blur', () => { adviserInput.value = titleCase(adviserInput.value); });
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
    updateAuthorsList();
    updateHiddenFields();
    modal.hide();
    authorForm.reset();
    authorForm.classList.remove('was-validated');
  });

  if (adviserCheckbox && adviserInput) {
    adviserCheckbox.addEventListener('change', function () {
      if (this.checked) {
        const adviserName = adviserInput.value.trim();
        if (!adviserName) {
          alert('Please enter the adviser name first.');
          this.checked = false;
          return;
        }
        const nameParts = adviserName.split(' ');
        const firstName = titleCase(nameParts[0] || adviserName);
        const lastName = titleCase(nameParts.length > 1 ? nameParts.slice(-1)[0] : '');
        const middleName = nameParts.length > 2 ? nameParts.slice(1, -1).map((n) => n[0]).join('') : '';

        adviser_Coauthor = {
          first_name: firstName,
          middle_name: middleName,
          last_name: lastName,
          student_id: '',
          mobile: '',
          webmail: '',
          home_address: '',
          is_adviser: true,
        };
      } else {
        adviser_Coauthor = null;
      }
      updateAuthorsList();
      updateHiddenFields();
    });
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
          authors.splice(index - (adviser_Coauthor ? 1 : 0), 1);
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
