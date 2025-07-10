// Hamburger menu
const hamburger = document.querySelector('.navbar-toggler.hamburger');
const navLinks = document.querySelector('.navbar-collapse');
hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('show');
    hamburger.classList.toggle('active');
});

// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector(anchor.getAttribute('href')).scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            offsetTop: -80 // Adjust for navbar height
        });
    });
});

// Parallax effect for hero and navbar scroll effect
window.addEventListener('scroll', () => {
    const hero = document.querySelector('.hero');
    const scrollY = window.scrollY;
    hero.style.setProperty('--scroll-y', `${scrollY * 0.5}px`);
    if (scrollY > 50) {
        document.querySelector('.enhanced-navbar').classList.add('scrolled');
    } else {
        document.querySelector('.enhanced-navbar').classList.remove('scrolled');
    }
});

// Logo hover animation
const compassLogo = document.querySelector('.compass-logo');
compassLogo.addEventListener('mouseenter', () => {
    compassLogo.classList.add('hovered');
});
compassLogo.addEventListener('mouseleave', () => {
    compassLogo.classList.remove('hovered');
});

// Accessibility: Handle keyboard navigation for hamburger
hamburger.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        hamburger.click();
    }
});
