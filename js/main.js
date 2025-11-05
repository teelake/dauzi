// Load header and footer from includes
function loadIncludes() {
  var headerPlaceholder = document.getElementById('header-placeholder');
  var footerPlaceholder = document.getElementById('footer-placeholder');
  
  // Get base path - handle both root and subdirectory deployments
  function getBasePath() {
    var path = window.location.pathname;
    // Remove filename and get directory path
    var lastSlash = path.lastIndexOf('/');
    if (lastSlash === -1 || lastSlash === 0) {
      // At root level
      return '';
    }
    // Return the directory path (including leading slash)
    return path.substring(0, lastSlash + 1);
  }
  
  var basePath = getBasePath();
  var headerPath = basePath + 'includes/header.html';
  var footerPath = basePath + 'includes/footer.html';
  
  if (headerPlaceholder) {
    fetch(headerPath)
      .then(function(response) {
        if (!response.ok) {
          throw new Error('HTTP error! status: ' + response.status);
        }
        return response.text();
      })
      .then(function(html) {
        if (html && html.trim()) {
          headerPlaceholder.outerHTML = html;
          // Reinitialize mobile menu and icons after header loads
          setTimeout(function() {
            initMobileMenu();
            setActiveNavLink();
            if (window.lucide && window.lucide.createIcons) {
              window.lucide.createIcons();
            }
          }, 50);
        } else {
          console.error('Header file is empty');
        }
      })
      .catch(function(err) { 
        console.error('Failed to load header from', headerPath, ':', err);
      });
  }
  
  if (footerPlaceholder) {
    fetch(footerPath)
      .then(function(response) {
        if (!response.ok) {
          throw new Error('HTTP error! status: ' + response.status);
        }
        return response.text();
      })
      .then(function(html) {
        if (html && html.trim()) {
          footerPlaceholder.outerHTML = html;
          // Reinitialize icons after footer loads
          setTimeout(function() {
            if (window.lucide && window.lucide.createIcons) {
              window.lucide.createIcons();
            }
          }, 50);
        } else {
          console.error('Footer file is empty');
        }
      })
      .catch(function(err) { 
        console.error('Failed to load footer from', footerPath, ':', err);
      });
  }
}

function initMobileMenu() {
  var menuButton = document.getElementById('mobile-menu-button');
  var mobileMenu = document.getElementById('mobile-menu');
  if (menuButton && mobileMenu) {
    // Remove existing listeners by cloning
    var newButton = menuButton.cloneNode(true);
    menuButton.parentNode.replaceChild(newButton, menuButton);
    var newMenu = mobileMenu.cloneNode(true);
    mobileMenu.parentNode.replaceChild(newMenu, mobileMenu);
    
    menuButton = document.getElementById('mobile-menu-button');
    mobileMenu = document.getElementById('mobile-menu');
    
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
    
    // Close mobile menu when clicking a link
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
}

function setActiveNavLink() {
  try {
    var path = window.location.pathname.split('/').pop() || 'index.html';
    var pageMap = {
      'index.html': 'index',
      'about.html': 'about',
      'services.html': 'services',
      'projects.html': 'projects',
      'training.html': 'training',
      'contact.html': 'contact'
    };
    var currentPage = pageMap[path] || 'index';
    
    var links = document.querySelectorAll('a.nav-link, a[data-nav-link]');
    links.forEach(function (link) {
      var navLink = link.getAttribute('data-nav-link');
      var href = link.getAttribute('href');
      if (!navLink && href) {
        var page = href.split('/').pop();
        navLink = pageMap[page];
      }
      if (navLink === currentPage) {
        link.classList.add('text-[var(--brand-2)]');
        link.classList.add('font-semibold');
        link.classList.add('active');
      } else {
        link.classList.remove('text-[var(--brand-2)]');
        link.classList.remove('font-semibold');
        link.classList.remove('active');
      }
    });
  } catch (e) {
    // No-op
  }
}

// Initialize AOS animations
document.addEventListener('DOMContentLoaded', function () {
  // Load includes first
  loadIncludes();
  
  // Wait a bit for includes to load, then continue
  setTimeout(function() {
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

    // Replace Lucide icons (will be called again after includes load)
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons();
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
  }, 100); // Wait 100ms for includes to potentially load
});


