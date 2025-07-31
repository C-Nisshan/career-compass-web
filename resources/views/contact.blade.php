@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="contact">
    <div class="container">
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

<section class="newsletter">
    <div class="container">
        <h2 class="section-title">Stay in the Loop</h2>
        <p>Get career tips, updates, and exclusive offers!</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="btn-primary">Subscribe</button>
        </form>
    </div>
</section>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="Chatbot" aria-label="Open CareerCompass Chatbot">
</div>
@endsection