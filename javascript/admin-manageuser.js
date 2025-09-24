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
});
