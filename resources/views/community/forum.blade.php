@extends('layouts.app')

@section('content')
<div class="community-forum-animated-bg"></div>
<div class="community-forum-particles" id="community-forum-particles"></div>

<section class="community-forum-hero">
    <div class="community-forum-hero-content">
        <h1>Community Forum</h1>
        <p class="community-forum-typing-effect" id="community-forum-typing"></p>
        <p>Discuss careers, share experiences, and connect with like-minded dreamers. Join the conversation!</p>
        <div class="community-forum-hero-buttons">
            <a href="{{ route('register') }}" class="community-forum-btn-primary">Create New Post</a>
            <a href="#community-forum-posts" class="community-forum-btn-secondary">View Discussions</a>
        </div>
    </div>
</section>

<section class="community-forum-posts" id="community-forum-posts">
    <div class="community-forum-container">
        <h2 class="community-forum-section-title">Active Discussions</h2>
        <div id="community-forum-posts-list" class="community-forum-posts-feed">
            <!-- Posts will be dynamically loaded here -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchForumPosts();
});

function fetchForumPosts() {
    fetch('{{ route('api.community.forum-posts') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayForumPosts(data.data);
            } else {
                console.error('Failed to load posts');
            }
        })
        .catch(error => console.error('Error fetching posts:', error));
}

function displayForumPosts(posts) {
    const list = document.getElementById('community-forum-posts-list');
    list.innerHTML = ''; // Clear existing content

    posts.forEach(post => {
        const postElement = document.createElement('div');
        postElement.classList.add('community-forum-post-card');
        if (post.pinned) {
            postElement.classList.add('community-forum-pinned');
        }

        // Use backend-computed fields
        const username = post.user && post.user.display_name ? post.user.display_name : 'Anonymous';
        const roleLabel = post.user && post.user.role_label ? post.user.role_label : 'User';

        const tagsHtml = post.tags 
            ? post.tags.map(tag => `<span class="community-forum-tag">${tag.name}</span>`).join(' ') 
            : '';

        const createdAt = new Date(post.created_at).toLocaleString();

        postElement.innerHTML = `
            <div class="community-forum-post-header">
                <span class="community-forum-user">[${roleLabel}] ${username}</span>
                <span class="community-forum-date">${createdAt}</span>
            </div>
            <div class="community-forum-post-title">${post.title}</div>
            <div class="community-forum-post-body">${post.body}</div>
            <div class="community-forum-post-tags">${tagsHtml}</div>
        `;

        list.appendChild(postElement);
    });
}
</script>
@endpush
