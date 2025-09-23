// Author modal and dynamic authors handling
document.addEventListener('DOMContentLoaded', function() {
    const mainForm = document.getElementById('submissionForm');
    const authorForm = document.getElementById('authorForm');
    const authorsList = document.getElementById('authorsList');
    const adviserCheckbox = document.getElementById('adviser_coauthor');
    let coauthors = [];

    // Handle adviser as co-author
    adviserCheckbox.addEventListener('change', function() {
        if (this.checked) {
            const adviser = document.querySelector('[name="adviser"]').value;
            updateAdviserCoauthor(adviser);
        }
    });

    function isValidPupWebmail(email) {
        // Pattern for student webmail
        const studentPattern = /^\d{4}-\d{5}-[A-Z]{2}-\d{1}@iskolar\.pup\.edu\.ph$/;
        
        // Pattern for faculty webmail
        const facultyPattern = /^[a-z]+\.[a-z]+@pup\.edu\.ph$/i;
        
        return studentPattern.test(email) || facultyPattern.test(email);
    }

    document.getElementById('saveAuthorBtn').addEventListener('click', function() {
        const form = document.getElementById('authorForm');
        
        // Validate student number
        const studentId = form.querySelector('[name="student_id"]').value;
        if (!/^\d{4}-\d{5}-[A-Z]{2}-\d{1}$/.test(studentId)) {
            alert('Invalid student number format. Use YYYY-XXXXX-XX-X');
            return;
        }

        // Validate mobile number
        const mobile = form.querySelector('[name="mobile"]').value;
        if (!/^09\d{9}$/.test(mobile)) {
            alert('Invalid mobile number format. Must start with 09 and have 11 digits');
            return;
        }

        // Validate webmail
        const webmail = form.querySelector('[name="webmail"]').value.toLowerCase();
        if (!isValidPupWebmail(webmail)) {
            alert('Please enter a valid PUP webmail:\n' +
                  '- Students: 2020-00000-XX-0@iskolarngbayan.pup.edu.ph\n' +
                  '- Faculty: firstname.lastname@pup.edu.ph');
            return;
        }

        // If validation passes, create author object
        const coauthor = {
            first_name: authorForm.querySelector('[name="first_name"]').value,
            last_name: authorForm.querySelector('[name="last_name"]').value,
            student_id: studentId,
            mobile: mobile,
            webmail: webmail,
            home_address: form.querySelector('[name="home_address"]').value,
            is_adviser: false
        };

        coauthors.push(coauthor);
        updateAuthorsList();
        updateHiddenFields();

        // Close modal and reset form
        bootstrap.Modal.getInstance(document.getElementById('authorModal')).hide();
        authorForm.reset();
    });

    function updateAdviserCoauthor(adviserName) {
        const [firstName, lastName] = adviserName.split(' ');
        const adviserCoauthor = {
            first_name: firstName || adviserName,
            last_name: lastName || '',
            student_id: '',
            mobile: '',
            webmail: '',
            home_address: '',
            is_adviser: true
        };

        // Remove any existing adviser from coauthors
        coauthors = coauthors.filter(author => !author.is_adviser);
        
        // Add new adviser
        coauthors.unshift(adviserCoauthor);
        
        updateAuthorsList();
        updateHiddenFields();
    }

    function updateAuthorsList() {
        authorsList.innerHTML = coauthors.map((author, index) => `
            <div class="author-item mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span>${author.first_name} ${author.last_name} 
                          ${author.is_adviser ? '<span class="badge bg-info">Adviser</span>' : ''}</span>
                    <button type="button" class="btn btn-link text-danger" 
                            onclick="removeAuthor(${index})"
                            ${author.is_adviser ? 'disabled' : ''}>
                        Remove
                    </button>
                </div>
            </div>
        `).join('');
    }

    function updateHiddenFields() {
        // Remove existing coauthor fields
        mainForm.querySelectorAll('input[name^="coauthors["]').forEach(el => el.remove());

        // Add new hidden fields
        coauthors.forEach((author, index) => {
            Object.entries(author).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `coauthors[${index}][${key}]`;
                input.value = value;
                mainForm.appendChild(input);
            });
        });
    }

    // Global remove function
    window.removeAuthor = function(index) {
        if (!coauthors[index].is_adviser) {
            coauthors.splice(index, 1);
            updateAuthorsList();
            updateHiddenFields();
        }
    };
});