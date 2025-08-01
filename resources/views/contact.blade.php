@extends('layouts.app')

@section('content')
<div class="contact-main-wrapper">
    <div class="contact-content-container">
        <div class="contact-animated-bg"></div>
        <div class="contact-particles" id="contact-particles">
            <div class="contact-particle"></div>
            <div class="contact-particle"></div>
            <div class="contact-particle"></div>
            <div class="contact-particle"></div>
        </div>

        <section class="contact">
            <div class="contact-container">
                <h1 class="section-title">Get in Touch</h1>
                <p>Have questions or need support? Reach out to us, and we’ll help you navigate your career journey!</p>
                <form id="contact-form" class="contact-form">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                    <p id="form-message" style="display: none;"></p>
                </form>
                <div class="contact-details">
                    <p><strong>Email:</strong> support@careercompass.com</p>
                    <p><strong>Follow Us:</strong>
                        <a href="#" aria-label="CareerCompass Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="CareerCompass LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </p>
                </div>
            </div>
        </section>

        <section class="contact-newsletter">
            <div class="contact-newsletter-container">
                <h2 class="section-title">Stay in the Loop</h2>
                <p>Get career tips, updates, and exclusive offers!</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn-primary">Subscribe</button>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        message: formData.get('message'),
        _token: formData.get('_token')
    };

    const messageElement = document.getElementById('form-message');
    messageElement.style.display = 'none';
    messageElement.style.color = '';

    try {
        const response = await fetch('/api/contact/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data),
            credentials: 'include' // Include cookies for potential JWT token
        });

        const result = await response.json();

        if (response.ok) {
            messageElement.textContent = 'Your message has been sent successfully!';
            messageElement.style.color = 'green';
            form.reset();
        } else {
            messageElement.textContent = result.message || 'An error occurred. Please try again.';
            messageElement.style.color = 'red';
        }
    } catch (error) {
        messageElement.textContent = 'Network error. Please try again later.';
        messageElement.style.color = 'red';
    }

    messageElement.style.display = 'block';
});
</script>
@endsection