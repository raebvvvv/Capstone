// Ensures that when user navigates back to an authenticated page after logout, a reload occurs.
(function(){
  // Only run if page was served to an authenticated session (server sets a marker element or body attr if desired)
  // Fallback: always check session cookie presence heuristically (not accessible directly), so rely on pageshow event.
  window.addEventListener('pageshow', function(evt){
    if (evt.persisted) {
      // Force reload to ensure server re-validation of session
      window.location.reload();
    }
  });
})();
