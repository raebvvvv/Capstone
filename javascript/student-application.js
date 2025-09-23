// student-application.js
document.addEventListener('DOMContentLoaded', function () {
  // --- View submission details ---
  const viewButtons = document.querySelectorAll('.view-details-btn');
  const detailsModalEl = document.getElementById('submissionDetailsModal');
  const detailsContent = document.getElementById('submissionDetailsContent');

  if (viewButtons.length > 0 && detailsModalEl && detailsContent) {
    viewButtons.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();

        const submissionId = btn.getAttribute('data-id');
        if (!submissionId) return;

        const modal = bootstrap.Modal.getOrCreateInstance(detailsModalEl);

        // Show spinner while loading
        detailsContent.innerHTML =
          '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        modal.show();

        fetch('view-submission.php?id=' + encodeURIComponent(submissionId) + '&modal=1')
          .then((response) => response.text())
          .then((html) => {
            detailsContent.innerHTML = html;
          })
          .catch(() => {
            detailsContent.innerHTML =
              '<div class="alert alert-danger">Failed to load details.</div>';
          });
      });
    });
  }

  // --- Author details (delegated) ---
  const authorModalEl = document.getElementById('authorDetailsModal');
  const authorContent = document.getElementById('authorDetailsContent');

  if (authorModalEl && authorContent) {
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.author-details-btn'); // check if clicked element is a button
      if (!btn) return; // not our button

      let author = null;
      try {
        author = JSON.parse(btn.getAttribute('data-author'));
      } catch (err) {
        console.error('Invalid author data:', err);
        return;
      }

      let html = `
        <ul class="list-unstyled">
          <li><strong>Name:</strong> ${author.first_name || ''} ${author.middle_name || ''} ${author.last_name || ''}</li>
          ${author.student_id ? `<li><strong>Student Number:</strong> ${author.student_id}</li>` : ''}
          ${author.webmail ? `<li><strong>Email:</strong> ${author.webmail}</li>` : ''}
          ${author.mobile ? `<li><strong>Mobile:</strong> ${author.mobile}</li>` : ''}
          ${author.home_address ? `<li><strong>Address:</strong> ${author.home_address}</li>` : ''}
          <li><strong>Role:</strong> ${author.is_adviser ? 'Adviser' : 'Author'}</li>
        </ul>
      `;

      authorContent.innerHTML = html;

      // Close submission modal before showing author modal
      if (detailsModalEl) {
        const detailsModal = bootstrap.Modal.getOrCreateInstance(detailsModalEl);
        detailsModalEl.addEventListener('hidden.bs.modal', function handler() {
          detailsModalEl.removeEventListener('hidden.bs.modal', handler);
          const authorModal = bootstrap.Modal.getOrCreateInstance(authorModalEl);
          authorModal.show();
        });
        detailsModal.hide();
      }
    });
  }

  // --- Toggle author details ---
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-author-details');
    if (btn) {
      const idx = btn.getAttribute('data-idx');
      const detailsDiv = document.getElementById('author-details-' + idx);
      if (detailsDiv) {
        const isShown = detailsDiv.style.display === 'block';
        detailsDiv.style.display = isShown ? 'none' : 'block';
        btn.textContent = isShown ? 'Show Details' : 'Hide Details';
      }
    }
  });
});
