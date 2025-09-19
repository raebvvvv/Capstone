// Author modal and dynamic authors handling
document.addEventListener('DOMContentLoaded', function() {
  let authorIndex = 0;
  const authorForm = document.getElementById('authorForm');
  const authorsList = document.getElementById('authorsList');
  const authorsHidden = document.getElementById('authorsHidden');
  const saveBtn = document.getElementById('saveAuthorBtn');
  const modalEl = document.getElementById('authorModal');
  
  // Only initialize bootstrap modal if bootstrap is loaded and element exists
  let modal = null;
  if (window.bootstrap && modalEl) {
    modal = new bootstrap.Modal(modalEl);
  }
  
  if(!authorForm || !authorsList || !authorsHidden || !saveBtn) return;

  function renderPlaceholder() {
    if(!authorsHidden.children.length) {
      authorsList.innerHTML = 'No authors added yet.';
    }
  }

  function makePill(data, idx) {
    const div = document.createElement('div');
    div.className = 'author-pill';
    div.dataset.idx = idx;
    div.innerHTML = `<span class="fw-semibold">${data.first_name} ${data.middle_initial ? data.middle_initial+'. ' : ''}${data.last_name}</span><small class="text-muted ms-2">${data.program || ''}</small>`;
    authorsList.appendChild(div);
  }

  function addHiddenInputs(data, idx) {
    const wrapper = document.createElement('div');
    wrapper.id = 'author-hidden-' + idx;
    for(const key in data) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = `authors[${idx}][${key}]`;
      input.value = data[key];
      wrapper.appendChild(input);
    }
    authorsHidden.appendChild(wrapper);
  }

  saveBtn.addEventListener('click', function() {
    const formData = new FormData(authorForm);
    const first = (formData.get('first_name')||'').trim();
    const last = (formData.get('last_name')||'').trim();
    if(!first || !last) {
      authorForm.querySelector('[name="first_name"]').focus();
      return;
    }
    const data = {};
    formData.forEach((v,k) => { 
      data[k] = (v||'').toString().trim(); 
    });
    addHiddenInputs(data, authorIndex);
    if(authorsList.innerHTML.includes('No authors')) authorsList.innerHTML = '';
    makePill(data, authorIndex);
    authorIndex++;
    authorForm.reset();
    if(modal) modal.hide();
    renderPlaceholder();
  });

  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', () => {
      const first = authorForm.querySelector('[name="first_name"]');
      if(first) first.focus();
    });
  }

  renderPlaceholder();
});