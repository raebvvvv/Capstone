(function(){
  'use strict';
  // Requires a global variable window.currentSubmissionCode OR data attribute on a container.
  document.addEventListener('DOMContentLoaded', function(){
    const code = window.currentSubmissionCode || document.querySelector('[data-submission-code]')?.getAttribute('data-submission-code') || '';
    if(!code) return; // nothing to do
    fetch('../submissions_unlock_info.php?code=' + encodeURIComponent(code), {credentials:'same-origin'})
      .then(r=> r.ok ? r.json() : Promise.reject())
      .then(data=>{
        if(!data || !Array.isArray(data.unlock_files)) return;
        const unlocked = new Set(data.unlock_files);
        const map = {
          recordOfCopyrightApplication: '#recordOfCopyrightApplicationInput',
          journalPublicationFormat: '#journalPublicationFormatInput',
          notarizedCopyrightApplicationForm: '#notarizedCopyrightApplicationFormInput',
          receiptOfPayment: '#receiptOfPaymentInput',
          fullManuscript: '#fullManuscriptInput',
          approvalSheet: '#approvalSheetInput',
          notarizedCoAuthorship: '#notarizedCoAuthorshipInput'
        };
        Object.entries(map).forEach(([key, sel])=>{
          const el = document.querySelector(sel);
            if(!el) return;
            if(unlocked.has(key)){
              el.removeAttribute('disabled');
              el.closest('.form-group, .mb-3, div')?.classList.add('file-unlocked');
            } else {
              el.setAttribute('disabled','disabled');
              el.closest('.form-group, .mb-3, div')?.classList.add('file-locked');
            }
        });
      })
      .catch(()=>{});
  });
})();