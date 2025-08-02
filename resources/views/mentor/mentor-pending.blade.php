@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/mentor-pending.css') }}">
@endsection

@section('content')
<div class="mentor-animated-bg"></div>
<div class="mentor-particles" id="mentor-particles"></div>

<section class="mentor-hero">
    <div class="mentor-hero-content">
        <h1>Awaiting Approval</h1>
        <p class="mentor-typing-effect" id="mentor-typing">Please wait for admin approval...</p>
        <p>Your mentor account is currently under review. You'll be notified once your account is approved by the admin. Thank you for your patience!</p>
        <div class="mentor-hero-buttons">
            <a href="{{ route('api.logout') }}" class="mentor-btn-primary">Logout</a>
            <a href="{{ route('home') }}" class="mentor-btn-secondary">Back to Home</a>
        </div>
    </div>
</section>

<section class="mentor-info">
    <div class="mentor-container">
        <h2 class="mentor-section-title">What Happens Next?</h2>
        <div class="mentor-steps">
            <div class="mentor-step mentor-fade-in">
                <div class="mentor-step-number">1</div>
                <h3>Admin Review</h3>
                <p>Our admin team is reviewing your mentor profile to ensure it meets our standards.</p>
            </div>
            <div class="mentor-step mentor-fade-in">
                <div class="mentor-step-number">2</div>
                <h3>Approval Notification</h3>
                <p>You'll receive an email notification once your account is approved or if further information is needed.</p>
            </div>
            <div class="mentor-step mentor-fade-in">
                <div class="mentor-step-number">3</div>
                <h3>Access Dashboard</h3>
                <p>Once approved, you'll gain full access to the mentor dashboard to start mentoring.</p>
            </div>
        </div>
    </div>
</section>

<section class="mentor-community">
    <div class="mentor-community-content">
        <h2>Stay Connected</h2>
        <p>While you wait, explore our community and connect with other mentors and learners.</p>
        <a href="{{ route('community.forum') }}" class="mentor-cta-btn">Join the Community</a>
    </div>
</section>

@endsection