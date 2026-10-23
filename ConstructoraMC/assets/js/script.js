// assets/js/script.js
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Efecto del Header al hacer scroll
    const header = document.getElementById('header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // 2. Animaciones de aparición (Reveal) con Intersection Observer
    const reveals = document.querySelectorAll('.reveal');
    
    const revealOnScroll = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Dejar de observar una vez animado
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(reveal => {
        revealOnScroll.observe(reveal);
    });

    // 3. Contador animado para las estadísticas
    const stats = document.querySelectorAll('.stat-number');
    const animateStat = element => {
        const finalText = element.textContent.trim();
        const match = finalText.match(/^([^\d]*)(\d+)(.*)$/);
        if (!match) return;

        const [, prefix, digits, suffix] = match;
        const target = Number(digits);
        const duration = 1800;
        const start = performance.now();

        const update = now => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = progress === 1
                ? finalText
                : `${prefix}${Math.floor(target * eased)}${suffix}`;

            if (progress < 1) requestAnimationFrame(update);
        };

        element.textContent = `${prefix}0${suffix}`;
        requestAnimationFrame(update);
    };

    // Iniciar el conteo cuando la sección de estadísticas aparece en pantalla.
    const statsSection = document.querySelector('.stats-section');
    if (statsSection && 'IntersectionObserver' in window) {
        const statsObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    stats.forEach(animateStat);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.25 });

        statsObserver.observe(statsSection);
    } else {
        stats.forEach(animateStat);
    }

    // 4. Menú móvil (Toggle)
    const menuToggle = document.getElementById('menu-toggle');
    const nav = document.getElementById('nav');
    
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    }

    // 5. Cerrar menú al hacer clic en un enlace (móvil)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            nav.classList.remove('active');
        });
    });
});
