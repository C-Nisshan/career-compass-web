@extends('layouts.app')

@section('styles')
    
@endsection

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="hero">
    <div class="hero-content">
        <h1>{{ __('home.hero_title') }}</h1>
        <p class="typing-effect" id="typing"></p>
        <p>{{ __('home.hero_subtitle') }}</p>
        <div class="hero-buttons">
            <a href="#" class="btn-primary">{{ __('home.begin_journey') }}</a>
            <a href="#" class="btn-secondary">{{ __('home.explore_how') }}</a>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item fade-in">
                <h3 id="users-count">{{ __('home.users_count') }}</h3>
                <p>{{ __('home.users_label') }}</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="accuracy-rate">{{ __('home.accuracy_rate') }}</h3>
                <p>{{ __('home.accuracy_label') }}</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="careers-matched">{{ __('home.careers_matched') }}</h3>
                <p>{{ __('home.careers_label') }}</p>
            </div>
            <div class="stat-item fade-in">
                <h3 id="success-stories">{{ __('home.success_stories') }}</h3>
                <p>{{ __('home.stories_label') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <h2 class="section-title">{{ __('home.features_title') }}</h2>
        <div class="features-grid">
            @foreach(__('home.features') as $feature)
                <div class="feature-card fade-in">
                    <div class="feature-content">
                        <div class="feature-icon"><i class="{{ $feature['icon'] }}"></i></div>
                        <h3>{{ $feature['title'] }}</h3>
                        <p>{{ $feature['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="testimonials" id="testimonials">
    <div class="container">
        <h2 class="section-title">{{ __('home.testimonials_title') }}</h2>
        <div class="testimonial-grid">
            @foreach(__('home.testimonials') as $testimonial)
                <div class="testimonial-card fade-in">
                    <p>{{ $testimonial['quote'] }}</p>
                    <h4>{{ $testimonial['author'] }}</h4>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <div class="container">
        <h2 class="section-title">{{ __('home.how_it_works_title') }}</h2>
        <div class="steps">
            @foreach(__('home.steps') as $index => $step)
                <div class="step fade-in">
                    <div class="step-number">{{ $index + 1 }}</div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="community" id="community">
    <div class="community-content">
        <h2>{{ __('home.community_title') }}</h2>
        <p>{{ __('home.community_description') }}</p>
        <a href="#" class="cta-btn">{{ __('home.join_now') }}</a>
    </div>
</section>

<section class="newsletter">
    <div class="container">
        <h2 class="section-title">{{ __('home.newsletter_title') }}</h2>
        <p>{{ __('home.newsletter_description') }}</p>
        <form>
            <input type="email" placeholder="{{ __('home.email_placeholder') }}" required>
            <button type="submit" class="btn-primary">{{ __('home.subscribe') }}</button>
        </form>
    </div>
</section>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="{{ __('home.chatbot_alt') }}">
</div>
@endsection

