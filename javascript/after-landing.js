// JS for navigation highlight and scroll (optional, for smooth UX)
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = link.getAttribute('href');
      if(href.startsWith('#')) {
        e.preventDefault();
        document.querySelector(href).scrollIntoView({behavior: 'smooth'});
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
      }
    });
  });