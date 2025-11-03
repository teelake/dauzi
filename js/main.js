// Initialize AOS animations
document.addEventListener('DOMContentLoaded', function () {
  if (window.AOS) {
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });
  }
  // Fallback: if AOS fails to load, reveal elements
  setTimeout(function () {
    if (!window.AOS) {
      var aosNodes = document.querySelectorAll('[data-aos]');
      aosNodes.forEach(function (el) { el.removeAttribute('data-aos'); });
    }
  }, 1200);

  // Replace Lucide icons
  if (window.lucide && window.lucide.createIcons) {
    window.lucide.createIcons();
  }

  // Mobile menu toggle
  var menuButton = document.getElementById('mobile-menu-button');
  var mobileMenu = document.getElementById('mobile-menu');
  if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', function () {
      var isOpen = !mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden');
      menuButton.setAttribute('aria-expanded', String(!isOpen));
      var icon = menuButton.querySelector('i[data-lucide]');
      if (icon && window.lucide) {
        icon.setAttribute('data-lucide', isOpen ? 'menu' : 'x');
        window.lucide.createIcons();
      }
    });
  }

  // Highlight active nav link based on pathname
  try {
    var path = window.location.pathname.split('/').pop() || 'index.html';
    var links = document.querySelectorAll('a.nav-link');
    links.forEach(function (link) {
      var href = link.getAttribute('href');
      if (!href) return;
      var page = href.split('/').pop();
      if (page === path) {
        link.classList.add('text-[var(--brand-2)]');
        link.classList.add('font-semibold');
      }
    });
  } catch (e) {
    // No-op
  }

  // Close mobile menu when clicking a link
  if (mobileMenu) {
    mobileMenu.addEventListener('click', function (e) {
      var target = e.target;
      if (target && target.matches('a.nav-link')) {
        mobileMenu.classList.add('hidden');
        if (menuButton) menuButton.setAttribute('aria-expanded', 'false');
        var icon = menuButton && menuButton.querySelector('i[data-lucide]');
        if (icon && window.lucide) {
          icon.setAttribute('data-lucide', 'menu');
          window.lucide.createIcons();
        }
      }
    });
  }

  // Contact form submission tracking
  var contactForm = document.getElementById('contact-form');
  if (contactForm && window.gtag) {
    contactForm.addEventListener('submit', function (e) {
      gtag('event', 'form_submit', {
        event_category: 'Contact',
        event_label: 'Contact Form Submission',
        value: 1
      });
      // Note: Form will still submit normally (no preventDefault)
    });
  }

  // Floating WhatsApp chat bubble
  try {
    var wa = document.createElement('a');
    wa.href = 'https://wa.me/2348033660991';
    wa.target = '_blank';
    wa.rel = 'noopener';
    wa.setAttribute('aria-label', 'WhatsApp chat');
    wa.className = 'fixed bottom-5 right-5 z-[60] inline-flex items-center justify-center h-12 w-12 rounded-full bg-emerald-500 text-white shadow-lg hover:bg-emerald-600 transition';
    wa.innerHTML = '<i data-lucide="message-circle" class="w-5 h-5"></i>';
    wa.addEventListener('click', function(){ if (window.gtag) { gtag('event','click',{event_category:'Contact',event_label:'WhatsApp Floating'}); } });
    document.body.appendChild(wa);
    if (window.lucide && window.lucide.createIcons) { window.lucide.createIcons(); }
  } catch (e) {}

  // Simple hero slider (auto-rotate)
  (function initHeroSlider(){
    var slider = document.getElementById('hero-slider');
    if (!slider) return;
    var track = slider.querySelector('.slider-track');
    if (!track) return;
    var slides = Array.prototype.slice.call(track.querySelectorAll('.slide'));
    if (slides.length === 0) return;
    var current = 0;
    slides.forEach(function(s, i){ s.style.display = i === 0 ? 'grid' : 'none'; });
    var indicators = slider.querySelector('.indicators');
    if (indicators){
      slides.forEach(function(_, i){
        var dot = document.createElement('button');
        dot.className = 'h-2 w-2 rounded-full ' + (i===0?'bg-[var(--brand)]':'bg-slate-300');
        dot.setAttribute('aria-label','Go to slide ' + (i+1));
        dot.addEventListener('click', function(){ goTo(i); });
        indicators.appendChild(dot);
      });
    }
    function render(){
      slides.forEach(function(s, i){ s.style.display = i === current ? 'grid' : 'none'; });
      if (indicators){
        var dots = indicators.querySelectorAll('button');
        dots.forEach(function(d, i){ d.className = 'h-2 w-2 rounded-full ' + (i===current?'bg-[var(--brand)]':'bg-slate-300'); });
      }
      if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
    }
    function next(){ current = (current + 1) % slides.length; render(); }
    function prev(){ current = (current - 1 + slides.length) % slides.length; render(); }
    function goTo(i){ current = i % slides.length; render(); restart(); }
    var nextBtn = slider.querySelector('.next');
    var prevBtn = slider.querySelector('.prev');
    nextBtn && nextBtn.addEventListener('click', function(){ next(); restart(); });
    prevBtn && prevBtn.addEventListener('click', function(){ prev(); restart(); });
    var timer = setInterval(next, 6000);
    function restart(){ clearInterval(timer); timer = setInterval(next, 6000); }
  })();
});


