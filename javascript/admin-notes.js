// Admin Notes: delegated click handler for viewing user notes
(function(){
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.view-notes');
    if (!btn) return;
    e.preventDefault();
    const id = btn.getAttribute('data-sub-id');
    const modalEl = document.getElementById('notesModal');
    const container = document.getElementById('notesContainer');
    if (!modalEl || !container) { return; }
    container.textContent = 'Loading…';
    fetch('fetch_notes.php?submission_id=' + encodeURIComponent(id), { credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
      .then(data => {
        const notes = (data && data.notes) || [];
        if (!notes.length) { container.innerHTML = '<div class="text-muted">No notes.</div>'; return; }
        // Build notes HTML safely
        const html = notes.map(n => {
          const created = (n.created_at || '').toString();
          const email = (n.email || '').toString();
          const noteText = (n.note || '').toString()
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/\n/g,'<br>');
          return (
            '<div class="mb-3 p-2 border rounded">' +
              '<div class="small text-muted">' + created + (email ? ' — ' + email : '') + '</div>' +
              '<div>' + noteText + '</div>' +
            '</div>'
          );
        }).join('');
        container.innerHTML = html;
      })
      .catch(() => { container.textContent = 'Failed to load notes.'; })
      .finally(() => {
        try {
          if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
          } else {
            // Fallback if Bootstrap modal not found
            modalEl.style.display = 'block';
          }
        } catch(_) {}
      });
  });
})();
