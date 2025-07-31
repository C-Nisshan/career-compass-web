@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="hero">
    <div class="hero-content">
        <h1>Your Future Starts Here</h1>
        <p class="typing-effect" id="typing"></p>
        <p>Discover your dream career with AI-powered insights tailored to your unique journey. Join 10K+ young explorers!</p>
        <div class="hero-buttons">
            <a href="{{ route('home') }}" class="btn-primary">Begin Your Journey</a>
            <a href="#how-it-works" class="btn-secondary">Explore How</a>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item fade-in">
                <h3 id="users-count">10K+</h3>
                <p>Users</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="accuracy-rate">88%</h3>
                <p>Accuracy</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="careers-matched">600+</h3>
                <p>Careers</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="success-stories">1500+</h3>
                <p>Stories</p>
            </div>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <h2 class="section-title">Why Choose CareerCompass?</h2>
        <div class="features-grid">
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-robot"></i></div>
                    <h3>Smart AI Matching</h3>
                    <p>Our advanced AI analyzes your skills and passions to find careers that fit you perfectly.</p>
                </div>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-map"></i></div>
                    <h3>Custom Roadmaps</h3>
                    <p>Get a personalized, step-by-step plan to land your dream job.</p>
                </div>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Personality Quiz</h3>
                    <p>Take our engaging RIASEC quiz to uncover your true calling.</p>
                </div>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Skill Boosters</h3>
                    <p>Master key skills with interactive challenges and quizzes.</p>
                </div>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Connect & Grow</h3>
                    <p>Join our vibrant community to network with mentors and peers.</p>
                </div>
            </div>
            <div class="feature-card fade-in">
                <div class="feature-content">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Progress Tracker</h3>
                    <p>Track your growth with intuitive analytics and reports.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <div class="container">
        <h2 class="section-title">Your Path to Success</h2>
        <div class="steps">
            <div class="step fade-in">
                <div class="step-number">1</div>
                <h3>Take Quiz</h3>
                <p>Answer our quick RIASEC quiz to share your interests and skills.</p>
            </div>
            <div class="step fade-in">
                <div class="step-number">2</div>
                <h3>Get Matches</h3>
                <p>Our AI finds careers that suit you with 88% accuracy.</p>
            </div>
            <div class="step fade-in">
                <div class="step-number">3</div>
                <h3>Explore Options</h3>
                <p>Dive into career details and personalized plans.</p>
            </div>
            <div class="step fade-in">
                <div class="step-number">4</div>
                <h3>Grow & Connect</h3>
                <p>Build skills and network to achieve your goals.</p>
            </div>
        </div>
    </div>
</section>

<section class="community" id="community">
    <div class="community-content">
        <h2>Join 10K+ Dreamers</h2>
        <p>Connect with peers, mentors, and professionals in our vibrant community. Share your journey and grow together.</p>
        <a href="{{ route('community.forum') }}" class="cta-btn">Join Now</a>
    </div>
</section>

@endsection