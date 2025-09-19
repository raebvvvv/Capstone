// Terms & Conditions gating logic
document.addEventListener('DOMContentLoaded', function() {
  const gate = document.getElementById('termsGate');
  const formSection = document.getElementById('formSection');
  const chk = document.getElementById('termsAgree');
  const nextBtn = document.getElementById('termsNext');
  const acceptedInput = document.getElementById('accepted_terms');
  
  if(!gate || !formSection || !chk || !nextBtn || !acceptedInput) return;
  
  function syncButton() { 
    nextBtn.disabled = !chk.checked; 
  }
  
  chk.addEventListener('change', syncButton); 
  syncButton();
  
  nextBtn.addEventListener('click', function(e) {
    e.preventDefault();
    if(!chk.checked) return;
    acceptedInput.value = '1';
    gate.classList.add('d-none');
    formSection.classList.remove('d-none');
    if(formSection.scrollIntoView) { 
      formSection.scrollIntoView({behavior:'smooth', block:'start'}); 
    }
  });
});