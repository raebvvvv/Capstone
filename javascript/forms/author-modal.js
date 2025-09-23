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
      first_name: authorForm.querySelector('[name="first_name"]').value.trim(),
      middle_name: authorForm.querySelector('[name="middle_name"]').value.trim(),
      last_name: authorForm.querySelector('[name="last_name"]').value.trim(),
      student_id: studentId,
      mobile: mobile,
      webmail: webmail,
      home_address: authorForm.querySelector('[name="home_address"]').value.trim(),
      is_adviser: false,
    };

    authors.push(coauthor);
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
      if (this.checked) {
        const adviserName = adviserInput.value.trim();
        if (!adviserName) {
          alert('Please enter the adviser name first.');
          this.checked = false;
          return;
        }
        // Split adviser name into first/middle/last (simple logic, can be improved)
        const nameParts = adviserName.split(' ');
        const firstName = nameParts[0] || adviserName;
        const lastName = nameParts.length > 1 ? nameParts.slice(-1)[0] : '';
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
          // Remove from authors array by index (skip adviser)
          authors.splice(index - (adviser_Coauthor ? 1 : 0), 1);
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
        input.value = value;
        mainForm.appendChild(input);
      });
    });
  }
});
