// Adviser as co-author functionality
(function(){
  const adviserCheckbox = document.getElementById('adviserCoauthor');
  const adviserInput = document.querySelector('input[name="adviser"]');
  const authorsList = document.getElementById('authorsList');
  const authorsHidden = document.getElementById('authorsHidden');
  let authorIndex = document.querySelectorAll('.author-pill').length || 0;
  
  function createAuthorData(adviserName) {
    // Create a more complete author object from the adviser name
    const nameParts = adviserName.trim().split(' ');
    let firstName = nameParts[0] || '';
    let lastName = nameParts.length > 1 ? nameParts[nameParts.length - 1] : '';
    let middleInitial = '';
    
    // If there are 3+ parts, middle part(s) might be middle name or part of first name
    if (nameParts.length > 2) {
      middleInitial = nameParts[1].charAt(0);
    }
    
    return {
      first_name: firstName,
      last_name: lastName,
      middle_initial: middleInitial,
      student_id: 'N/A', // Not applicable for advisers
      mobile: '',
      home_address: '',
      webmail: '',
      role: 'Adviser',
      is_adviser: true
    };
  }

  function addAdviserAuthor(name) {
    // Remove existing adviser-author if any
    removeAdviserAuthor();
    
    if (!name.trim()) return;
    
    const authorData = createAuthorData(name);
    const idx = authorIndex;
    
    // Create pill
    const div = document.createElement('div');
    div.className = 'author-pill';
    div.id = 'adviser-pill';
    div.dataset.idx = idx;
    div.innerHTML = `
      <span class="fw-semibold">${authorData.first_name} ${authorData.middle_initial ? authorData.middle_initial + '. ' : ''}${authorData.last_name}</span>
      <small class="text-muted ms-2">Adviser</small>
    `;
    
    // Clear "No authors" message if present
    if (authorsList.innerHTML.includes('No authors')) {
      authorsList.innerHTML = '';
    }
    
    authorsList.appendChild(div);
    
    // Create hidden inputs for all adviser data
    const wrapper = document.createElement('div');
    wrapper.id = 'author-hidden-adviser';
    
    for (const key in authorData) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = `authors[${idx}][${key}]`;
      input.value = authorData[key];
      wrapper.appendChild(input);
    }
    
    authorsHidden.appendChild(wrapper);
    authorIndex++;
  }

  function removeAdviserAuthor() {
    const pill = document.getElementById('adviser-pill');
    const hidden = document.getElementById('author-hidden-adviser');
    
    if (pill) pill.remove();
    if (hidden) hidden.remove();
    
    if (!document.querySelectorAll('.author-pill').length) {
      authorsList.innerHTML = 'No authors added yet.';
    }
  }

  // Initialize everything when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Only proceed if we have the necessary elements
    if (!adviserCheckbox || !adviserInput || !authorsList || !authorsHidden) return;
    
    // Add event listener for checkbox
    adviserCheckbox.addEventListener('change', function() {
      if (adviserCheckbox.checked && adviserInput.value.trim()) {
        addAdviserAuthor(adviserInput.value.trim());
      } else {
        removeAdviserAuthor();
      }
    });

    // Add event listener for adviser name input
    adviserInput.addEventListener('input', function() {
      if (adviserCheckbox.checked) {
        if (adviserInput.value.trim()) {
          addAdviserAuthor(adviserInput.value.trim());
        } else {
          removeAdviserAuthor();
        }
      }
    });
    
    // Initialize author index by checking existing authors
    const existingPills = document.querySelectorAll('.author-pill');
    if (existingPills.length) {
      authorIndex = Math.max(...Array.from(existingPills).map(p => parseInt(p.dataset.idx || '0'))) + 1;
    }
  });
})();