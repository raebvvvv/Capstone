/*
  Upload Size Guard
  - Enforces per-file 50MB (configurable via data-file-max-mb)
  - Gives instant feedback on file selection and blocks submit if limits exceeded
*/
(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') return fn();
    document.addEventListener('DOMContentLoaded', fn);
  }

  function mbToBytes(mb) {
    var n = Number(mb);
    return isFinite(n) && n > 0 ? n * 1024 * 1024 : 0;
  }

  function createAlert(message, type) {
    type = type || 'danger';
    var div = document.createElement('div');
    div.className = 'alert alert-' + type + ' alert-dismissible fade show';
    div.role = 'alert';
    div.innerHTML =
      '<div>' + message + '</div>' +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    return div;
  }

  function showFieldError(input, message) {
    // Mark invalid
    input.classList.add('is-invalid');
    // Ensure there is an invalid-feedback sibling
    var fb = input.parentElement && input.parentElement.querySelector('.invalid-feedback');
    if (!fb) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      input.parentElement && input.parentElement.appendChild(fb);
    }
    fb.textContent = message;
  }

  function clearFieldError(input) {
    input.classList.remove('is-invalid');
    var fb = input.parentElement && input.parentElement.querySelector('.invalid-feedback');
    if (fb) fb.textContent = '';
  }

  ready(function () {
    var form = document.getElementById('submissionForm');
    if (!form) return;

    // Read limits from data attributes or fallback
  var perFileMaxMB = Number(form.getAttribute('data-file-max-mb')) || 50; // default 50MB per file
    var perFileMaxBytes = mbToBytes(perFileMaxMB);

    // Helper to validate a single input
    function validateInput(input) {
      clearFieldError(input);
      if (!input.files || input.files.length === 0) return { ok: true, size: 0 };
      var f = input.files[0];
      if (perFileMaxBytes && f.size > perFileMaxBytes) {
        // Align with exact required message
        showFieldError(input, 'File exceed 50mb');
        // Clear the selected file to prevent accidental submit
        try { input.value = ''; } catch (e) {}
        return { ok: false, size: 0 };
      }
      return { ok: true, size: f.size };
    }

    // Attach change listeners to file inputs
    var fileInputs = Array.prototype.slice.call(form.querySelectorAll('input[type="file"]'));
    fileInputs.forEach(function (inp) {
      inp.addEventListener('change', function () {
        validateInput(inp);
      });
    });

    // On submit, enforce per-file limit
    form.addEventListener('submit', function (ev) {
      var firstInvalid = null;
      for (var i = 0; i < fileInputs.length; i++) {
        var res = validateInput(fileInputs[i]);
        if (!res.ok && !firstInvalid) {
          firstInvalid = fileInputs[i];
        }
      }

      if (firstInvalid) {
        ev.preventDefault();
        firstInvalid.focus();
        return;
      }
    });
  });
})();
