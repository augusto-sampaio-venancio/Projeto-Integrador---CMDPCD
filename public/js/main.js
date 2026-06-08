document.addEventListener('DOMContentLoaded', () => {
  // Inicializar os ícones do Lucide
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // Configurar ano atual no rodapé
  const yearElement = document.getElementById('current-year');
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }

  // Lógica do Menu Mobile
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
      mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
      
      if (isExpanded) {
        mobileMenu.classList.add('hidden');
        mobileMenuBtn.innerHTML = '<i data-lucide="menu" class="w-6 h-6"></i>';
      } else {
        mobileMenu.classList.remove('hidden');
        mobileMenuBtn.innerHTML = '<i data-lucide="x" class="w-6 h-6"></i>';
      }
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  }

  // Animações simples de Scroll (Fade In)
  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .zoom-in');
  animatedElements.forEach(el => observer.observe(el));
});
