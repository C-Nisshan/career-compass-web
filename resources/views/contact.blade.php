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
                <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
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
@endsection