import './bootstrap';

// ═══════════════════════════════════════════════════════════
// Header scroll behavior
// ═══════════════════════════════════════════════════════════
const header = document.getElementById('site-header');
const headerBg = document.getElementById('header-bg');

if (header && headerBg) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            headerBg.classList.remove('opacity-0');
            headerBg.classList.add('opacity-100');
        } else {
            headerBg.classList.remove('opacity-100');
            headerBg.classList.add('opacity-0');
        }
    }, { passive: true });
}

// ═══════════════════════════════════════════════════════════
// Mobile menu toggle
// ═══════════════════════════════════════════════════════════
const mobileToggle = document.getElementById('mobile-menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');
const mobileBackdrop = document.getElementById('mobile-menu-backdrop');
const mobileContent = document.getElementById('mobile-menu-content');
const bar1 = document.getElementById('bar-1');
const bar2 = document.getElementById('bar-2');
const bar3 = document.getElementById('bar-3');

let menuOpen = false;

function toggleMenu() {
    menuOpen = !menuOpen;
    if (menuOpen) {
        mobileMenu.classList.remove('pointer-events-none');
        mobileMenu.setAttribute('aria-hidden', 'false');
        mobileBackdrop.classList.remove('opacity-0');
        mobileBackdrop.classList.add('opacity-100');
        mobileContent.classList.remove('opacity-0', 'translate-y-8');
        mobileContent.classList.add('opacity-100', 'translate-y-0');
        document.body.style.overflow = 'hidden';
        // Animate hamburger to X
        bar1.style.transform = 'rotate(45deg) translate(4px, 4px)';
        bar2.style.opacity = '0';
        bar3.style.transform = 'rotate(-45deg) translate(3px, -3px)';
        bar3.style.width = '100%';
    } else {
        mobileMenu.classList.add('pointer-events-none');
        mobileMenu.setAttribute('aria-hidden', 'true');
        mobileBackdrop.classList.remove('opacity-100');
        mobileBackdrop.classList.add('opacity-0');
        mobileContent.classList.remove('opacity-100', 'translate-y-0');
        mobileContent.classList.add('opacity-0', 'translate-y-8');
        document.body.style.overflow = '';
        // Reset hamburger
        bar1.style.transform = '';
        bar2.style.opacity = '';
        bar3.style.transform = '';
        bar3.style.width = '';
    }
}

if (mobileToggle) {
    mobileToggle.addEventListener('click', toggleMenu);
}

// Close mobile menu on nav link click
document.querySelectorAll('.mobile-nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (menuOpen) toggleMenu();
    });
});

// ═══════════════════════════════════════════════════════════
// Scroll reveal (Intersection Observer)
// ═══════════════════════════════════════════════════════════
const revealElements = document.querySelectorAll('.reveal');

if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
}

// ═══════════════════════════════════════════════════════════
// Active nav link highlight on scroll
// ═══════════════════════════════════════════════════════════
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link');

if (sections.length > 0 && navLinks.length > 0) {
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('text-ivory', 'text-ivory/60');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('text-ivory');
                    } else {
                        link.classList.add('text-ivory/60');
                    }
                });
            }
        });
    }, {
        threshold: 0.3,
        rootMargin: '-100px 0px -50% 0px'
    });

    sections.forEach(section => sectionObserver.observe(section));
}

// ═══════════════════════════════════════════════════════════
// Smooth scroll for anchor links
// ═══════════════════════════════════════════════════════════
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const offset = 96;
            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});
