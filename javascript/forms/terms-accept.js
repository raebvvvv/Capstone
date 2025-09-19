// Centralized Terms & Conditions acceptance logic
// Applies to any form with: #termsAgree, #accepted_terms, #termsNext, and #submissionForm
(function(){
  function init(){
    var termsCheckbox = document.getElementById('termsAgree');
    var acceptedField = document.getElementById('accepted_terms');
    var nextBtn = document.getElementById('termsNext');
    var submissionForm = document.getElementById('submissionForm');
    if(!termsCheckbox || !acceptedField){ return; }

    // Enable/disable Next button live
    if(nextBtn){
      nextBtn.disabled = !termsCheckbox.checked;
      termsCheckbox.addEventListener('change', function(){
        nextBtn.disabled = !termsCheckbox.checked;
      });
      nextBtn.addEventListener('click', function(){
        if(termsCheckbox.checked){
          acceptedField.value = 'yes';
          // Reveal form section if gating container present
          var gate = document.getElementById('termsGate');
          var section = document.getElementById('formSection');
          if(section && gate){
            gate.classList.add('d-none');
            section.classList.remove('d-none');
          }
        }
      });
    }

    if(submissionForm){
      submissionForm.addEventListener('submit', function(e){
        if(termsCheckbox.checked){
          acceptedField.value = 'yes';
        } else {
          e.preventDefault();
          alert('You must accept the Terms & Conditions before submitting.');
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
    }
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
