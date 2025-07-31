// ========================
// Header.js
// ========================

// Hamburger menu
const hamburger = document.querySelector('.navbar-toggler.hamburger');
const navLinks = document.querySelector('.navbar-collapse');

if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('show');
        hamburger.classList.toggle('active');
    });

    // Accessibility: Handle keyboard navigation for hamburger
    hamburger.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            hamburger.click();
        }
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.querySelector(anchor.getAttribute('href'));
        if (target) {
            const navbarHeight = 80; // Adjust as needed
            const offsetTop = target.getBoundingClientRect().top + window.scrollY - navbarHeight;

            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// Parallax effect for hero and navbar scroll effect
const hero = document.querySelector('.hero');
const navbar = document.querySelector('.enhanced-navbar');

if (hero || navbar) {
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;

        if (hero) {
            hero.style.setProperty('--scroll-y', `${scrollY * 0.5}px`);
        }

        if (navbar) {
            if (scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });
}

// Logo hover animation
const compassLogo = document.querySelector('.compass-logo');

if (compassLogo) {
    compassLogo.addEventListener('mouseenter', () => {
        compassLogo.classList.add('hovered');
    });
    compassLogo.addEventListener('mouseleave', () => {
        compassLogo.classList.remove('hovered');
    });
}
