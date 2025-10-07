// Employee authors modal + hidden fields handling (migrated from inline to satisfy CSP)
(function(){
  var authorIndex = 0;
  var authorForm = document.getElementById('authorForm');
  var authorsList = document.getElementById('authorsList');
  var authorsHidden = document.getElementById('authorsHidden');
  var saveBtn = document.getElementById('saveAuthorBtn');
  var modalEl = document.getElementById('authorModal');
  if(!authorForm || !authorsList || !authorsHidden || !saveBtn || !modalEl){ return; }

  var modal;
  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    modal = new bootstrap.Modal(modalEl);
  }

  function renderPlaceholder(){
    if(!authorsHidden.children.length){
      authorsList.innerHTML = 'No authors added yet.';
    }
  }

  function makePill(data, idx){
    var div = document.createElement('div');
    div.className = 'author-pill';
    div.dataset.idx = idx;
    var midRaw = (data.middle_name || data.middle_initial || '').toString().trim();
    var middle = '';
    if (midRaw) {
      // if user provided a full middle name, show as-is; if initial, append a dot
      middle = (midRaw.length === 1 ? (midRaw.toUpperCase() + '. ') : (midRaw + ' '));
    }
    div.innerHTML = '<span class="fw-semibold">' + (data.first_name||'') + ' ' + middle + (data.last_name||'') + '</span>' +
                    '<small class="text-muted ms-2">' + (data.program||'') + '</small>';
    authorsList.appendChild(div);
  }

  function addHiddenInputs(data, idx){
    var wrapper = document.createElement('div');
    wrapper.id = 'author-hidden-' + idx;
    for(var key in data){
      if(Object.prototype.hasOwnProperty.call(data, key)){
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'authors[' + idx + '][' + key + ']';
        input.value = data[key];
        wrapper.appendChild(input);
      }
    }
    authorsHidden.appendChild(wrapper);
  }

  saveBtn.addEventListener('click', function(){
    var formData = new FormData(authorForm);
    var first = (formData.get('first_name')||'').toString().trim();
    var last = (formData.get('last_name')||'').toString().trim();
    if(!first || !last){
      saveBtn.disabled = false;
      var firstInput = authorForm.querySelector('[name="first_name"]');
      if(firstInput) firstInput.focus();
      return;
    }
    var data = {};
    formData.forEach(function(v,k){ data[k] = (v||'').toString().trim(); });
    addHiddenInputs(data, authorIndex);
    if(authorsList.innerHTML.indexOf('No authors') !== -1){ authorsList.innerHTML = ''; }
    makePill(data, authorIndex);
    authorIndex++;
    authorForm.reset();
    if(modal){ modal.hide(); }
    renderPlaceholder();
  });

  modalEl.addEventListener('shown.bs.modal', function(){
    var firstInput = authorForm.querySelector('[name="first_name"]');
    if(firstInput) firstInput.focus();
  });

  // Adviser co-author sync
  var adviserCheckbox = document.getElementById('adviserCoauthor');
  var adviserInput = document.querySelector('input[name="adviser"]');
  var adviserIdx = 'adviser';
  var adviserMirrorHidden = document.getElementById('adviser_Coauthor_hidden');

  function addAdviserAuthor(name) {
    if (!document.getElementById('adviser-pill')) {
      var div = document.createElement('div');
      div.className = 'author-pill';
      div.id = 'adviser-pill';
      div.innerHTML = '<span class="fw-semibold">' + name + '</span> <small class="text-muted ms-2">Adviser</small>';
      if (authorsList.innerHTML.indexOf('No authors') !== -1) authorsList.innerHTML = '';
      authorsList.appendChild(div);
    }
    if (!document.getElementById('adviser-hidden')) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'authors[' + adviserIdx + '][name]';
      input.value = name;
      input.id = 'adviser-hidden';
      authorsHidden.appendChild(input);
    }
  }

  function removeAdviserAuthor() {
    var pill = document.getElementById('adviser-pill');
    var hidden = document.getElementById('adviser-hidden');
    if (pill) pill.remove();
    if (hidden) hidden.remove();
    if (!authorsHidden.children.length) {
      authorsList.innerHTML = 'No authors added yet.';
    }
  }

  if(adviserCheckbox && adviserInput){
    adviserCheckbox.addEventListener('change', function() {
      if (adviserMirrorHidden) adviserMirrorHidden.value = adviserCheckbox.checked ? '1' : '';
      if (adviserCheckbox.checked && adviserInput.value.trim()) {
        addAdviserAuthor(adviserInput.value.trim());
      } else {
        removeAdviserAuthor();
      }
    });

    adviserInput.addEventListener('input', function() {
      if (adviserCheckbox.checked) {
        removeAdviserAuthor();
        if (adviserInput.value.trim()) {
          addAdviserAuthor(adviserInput.value.trim());
        }
      }
    });
  }

  renderPlaceholder();
})();
