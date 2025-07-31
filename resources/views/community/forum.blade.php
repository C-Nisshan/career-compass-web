@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="forum">
    <div class="container">
        <h1 class="section-title">Community Forum</h1>
        <p>Connect with peers, mentors, and professionals. Share your journey and ask questions!</p>
        @if($posts->isEmpty())
            <p>No posts yet. Start the conversation!</p>
        @else
            <div class="forum-posts">
                @foreach($posts as $post)
                    <div class="post-card fade-in">
                        <h3>{{ $post->title }}</h3>
                        <p>{{ Str::limit($post->content, 200) }}</p>
                        <p><small>Posted by {{ $post->user->name ?? 'Anonymous' }} on {{ $post->created_at->format('M d, Y') }}</small></p>
                    </div>
                @endforeach
            </div>
        @endif
        @auth
            <a href="{{ route('forum.create') }}" class="btn-primary">Create New Post</a>
        @endauth
    </div>
</section>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="Chatbot" aria-label="Open CareerCompass Chatbot">
</div>
@endsection