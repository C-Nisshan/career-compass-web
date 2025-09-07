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

// Manage active state for nav links and dropdown items
document.querySelectorAll('.nav-links .nav-link, .dropdown-menu .dropdown-item').forEach(link => {
    link.addEventListener('click', (e) => {
        // Remove active class from all links and dropdown items
        document.querySelectorAll('.nav-links .nav-link, .dropdown-menu .dropdown-item').forEach(nav => {
            nav.classList.remove('active');
        });
        // Add active class to the clicked link
        e.currentTarget.classList.add('active');
        // If a dropdown item is clicked, also activate the parent "Tools" link
        if (e.currentTarget.classList.contains('dropdown-item')) {
            e.currentTarget.closest('.nav-item.dropdown').querySelector('.nav-link').classList.add('active');
        }
    });
});

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

            // Remove active class from all links and dropdown items
            document.querySelectorAll('.nav-links .nav-link, .dropdown-menu .dropdown-item').forEach(nav => {
                nav.classList.remove('active');
            });
            // Add active class to the clicked anchor
            anchor.classList.add('active');
        }
    });
});

// Set active link based on current URL
function setActiveLink() {
    const currentPath = window.location.pathname;
    // Normalize current path by removing language prefix (e.g., '/en' -> '/')
    const normalizedCurrentPath = currentPath.replace(/^\/[a-z]{2}(\/|$)/, '/');

    let matched = false;
    document.querySelectorAll('.nav-links .nav-link, .dropdown-menu .dropdown-item').forEach(link => {
        let linkPath;
        try {
            linkPath = new URL(link.href, window.location.origin).pathname;
        } catch (error) {
            linkPath = link.getAttribute('href'); // Fallback for relative or invalid URLs
        }
        // Normalize link path by removing language prefix
        const normalizedLinkPath = linkPath.replace(/^\/[a-z]{2}(\/|$)/, '/');

        // Skip anchor links (e.g., #features, #how-it-works)
        if (linkPath.startsWith('#')) {
            link.classList.remove('active');
            return;
        }

        if (normalizedLinkPath === normalizedCurrentPath && !matched) {
            link.classList.add('active');
            // If a dropdown item is active, also activate the parent "Tools" link
            if (link.classList.contains('dropdown-item')) {
                link.closest('.nav-item.dropdown').querySelector('.nav-link').classList.add('active');
            }
            matched = true; // Ensure only the first match is marked active
        } else {
            link.classList.remove('active');
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', setActiveLink);

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