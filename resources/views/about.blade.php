@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="about">
    <div class="about-container">
        <h1 class="about-section-title">About CareerCompass</h1>
        <p>CareerCompass is a holistic platform designed to help young people discover their dream careers through personalized, AI-powered recommendations. Our mission is to bridge the gap between your passions, skills, and the evolving job market, empowering you with clear, actionable steps to succeed.</p>
        <p>Using the RIASEC model and advanced machine learning, we analyze your academic performance, personality, interests, and experiences to suggest careers that truly fit you. From custom roadmaps to interactive quizzes and a vibrant community, we’re here to guide and inspire you every step of the way.</p>
    </div>
</section>

<section class="testimonials" id="testimonials">
    <div class="container">
        <h2 class="section-title">What Our Users Say</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card fade-in">
                <p>CareerCompass transformed my career path! I discovered my passion for UX design, and the roadmap was a lifesaver!</p>
                <h4>Sara, 22</h4>
            </div>
            <div class="testimonial-card fade-in">
                <p>The AI recommendations were incredibly accurate. I’m now thriving in data science, thanks to CareerCompass!</p>
                <h4>Arjun, 19</h4>
            </div>
            <div class="testimonial-card fade-in">
                <p>The community is fantastic! I connected with a mentor who helped me land my first job!</p>
                <h4>Emma, 25</h4>
            </div>
        </div>
    </div>
</section>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="Chatbot" aria-label="Open CareerCompass Chatbot">
</div>
@endsection