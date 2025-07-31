@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="forum-create">
    <div class="container">
        <h1 class="section-title">Create a New Forum Post</h1>
        <form action="{{ route('forum.store') }}" method="POST" class="forum-form">
            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Post Title" required>
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="body">Content</label>
                <textarea id="body" name="body" placeholder="Your Post Content" required></textarea>
                @error('body')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-primary">Submit Post</button>
        </form>
    </div>
</section>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="Chatbot" aria-label="Open CareerCompass Chatbot">
</div>
@endsection