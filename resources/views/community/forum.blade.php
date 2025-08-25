@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="forum">
    <div class="container">
        <h1 class="section-title">Community Forum</h1>
        <p>Connect with peers, mentors, and professionals. Share your journey and ask questions!</p>
        <div id="forum-posts"></div>
        @auth
            <a href="{{ route('forum.create') }}" class="btn-primary">Create New Post</a>
        @endauth
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetchPosts();

    async function fetchPosts() {
        try {
            const response = await fetch('/api/forum', {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user() ? auth()->user()->token : '' }}',
                },
            });
            const data = await response.json();
            const postsContainer = document.getElementById('forum-posts');
            postsContainer.innerHTML = '';

            if (data.posts.length === 0) {
                postsContainer.innerHTML = '<p>No posts yet. Start the conversation!</p>';
                return;
            }

            data.posts.forEach(post => {
                const postElement = document.createElement('div');
                postElement.className = 'post-card fade-in';
                postElement.innerHTML = `
                    <h3>${post.title}</h3>
                    <p>${post.body.substring(0, 200)}${post.body.length > 200 ? '...' : ''}</p>
                    <p><small>Posted by ${post.user?.name || 'Anonymous'} on ${new Date(post.created_at).toLocaleDateString()}</small></p>
                    <a href="/community/forum/${post.uuid}">View Post</a>
                `;
                postsContainer.appendChild(postElement);
            });
        } catch (error) {
            console.error('Error fetching posts:', error);
        }
    }
});
</script>
@endsection