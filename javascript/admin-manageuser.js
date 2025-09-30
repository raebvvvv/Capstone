document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="search"]');
  const form = searchInput && searchInput.form;
  if (searchInput && form) {
    searchInput.addEventListener('input', function () {
      if (searchInput.value === '') {
        form.submit();
      }
    });
  }

  // Bulk selection & actions
  document.querySelectorAll('form.bulk-form').forEach(function(bulkForm){
    const selectAll = bulkForm.querySelector('.select-all');
    const checkboxes = bulkForm.querySelectorAll('.row-check');
    const submitBtn = bulkForm.querySelector('.bulk-submit');
    const actionSel = bulkForm.querySelector('.bulk-action-select');

    function updateSubmitState(){
      const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
      submitBtn.disabled = !(anyChecked && actionSel.value);
    }

    if (selectAll){
      selectAll.addEventListener('change', function(){
        checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
        updateSubmitState();
      });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateSubmitState));
    actionSel && actionSel.addEventListener('change', updateSubmitState);

    bulkForm.addEventListener('submit', function(e){
      const action = actionSel ? actionSel.value : '';
      if (action === 'delete'){
        if (!confirm('Delete selected users? This cannot be undone.')){
          e.preventDefault();
        }
      }
    });
  });
});
