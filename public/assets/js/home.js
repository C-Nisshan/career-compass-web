// Start counters
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(document.getElementById('users-count'), 10);
            animateCounter(document.getElementById('accuracy-rate'), 88);
            animateCounter(document.getElementById('careers-matched'), 600);
            animateCounter(document.getElementById('success-stories'), 1500);
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });
statsObserver.observe(document.querySelector('.stats'));

// Typing effect
const phrases = ['Discover Your Passion', 'Plan Your Career', 'Achieve Your Dreams', 'Realize Your Potential'];
let phraseIndex = 0;
let charIndex = 0;
const typingElement = document.getElementById('typing');
function type() {
    if (charIndex < phrases[phraseIndex].length) {
        typingElement.textContent += phrases[phraseIndex].charAt(charIndex);
        charIndex++;
        setTimeout(type, 80);
    } else {
        setTimeout(() => {
            charIndex = 0;
            typingElement.textContent = '';
            phraseIndex = (phraseIndex + 1) % phrases.length;
            type();
        }, 2500);
    }
}
type();

// Particles
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 12 + 's';
        particle.style.animationDuration = Math.random() * 8 + 8 + 's';
        particlesContainer.appendChild(particle);
    }
}
createParticles();
