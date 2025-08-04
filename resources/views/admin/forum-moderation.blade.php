@extends('layouts.app')

@section('content')
<link href="{{ asset('css/forum-moderation.css') }}" rel="stylesheet">

<div class="forum-moderation-container">
    <div class="forum-moderation-content">
        <h1 class="forum-moderation-title">Admin Forum Moderation</h1>
        <p class="forum-moderation-description">Manage posts and comments in the community forum.</p>

        <div class="forum-moderation-card">
            <div class="forum-moderation-filter">
                <label for="forum-moderation-status-filter">Filter by Status:</label>
                <select id="forum-moderation-status-filter">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="hidden">Hidden</option>
                </select>
            </div>

            <div id="forum-moderation-loading" class="hidden">Loading...</div>
            <div id="forum-moderation-error" class="hidden"></div>
            <div id="forum-moderation-success" class="hidden"></div>

            <div class="forum-moderation-section">
                <h2 class="forum-moderation-subtitle">Posts</h2>
                <div id="forum-moderation-posts-list"></div>
            </div>

            <div class="forum-moderation-section">
                <h2 class="forum-moderation-subtitle">Comments</h2>
                <div id="forum-moderation-comments-list"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded fired for Forum Moderation'); // Debug: Confirm event

    const statusFilter = document.getElementById('forum-moderation-status-filter');
    const postsList = document.getElementById('forum-moderation-posts-list');
    const commentsList = document.getElementById('forum-moderation-comments-list');
    const errorMessage = document.getElementById('forum-moderation-error');
    const successMessage = document.getElementById('forum-moderation-success');
    const loading = document.getElementById('forum-moderation-loading');

    // Debug: Check if elements exist
    if (!statusFilter || !postsList || !commentsList || !errorMessage || !successMessage || !loading) {
        console.error('One or more DOM elements not found!');
        errorMessage.textContent = 'Required elements not found in the DOM.';
        errorMessage.classList.remove('hidden');
        return;
    }

    async function fetchPostsAndComments(status = '') {
        console.log('Fetching posts/comments with status:', status); // Debug: Log status
        loading.classList.remove('hidden');
        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');
        postsList.innerHTML = '';
        commentsList.innerHTML = '';

        try {
            // Fetch posts
            const postsUrl = status ? `/api/forum?status=${encodeURIComponent(status)}` : '/api/forum';
            console.log('Posts API URL:', postsUrl); // Debug: Log URL
            const postsResponse = await fetch(postsUrl, {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user()->token }}',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const postsResult = await postsResponse.json();
            console.log('Posts API Response:', postsResult); // Debug: Log response

            if (!postsResponse.ok) {
                throw new Error(postsResult.error || 'Failed to fetch posts');
            }

            if (postsResult.posts.data.length === 0) {
                postsList.innerHTML = '<div class="forum-moderation-no-data">No posts found.</div>';
            } else {
                postsResult.posts.data.forEach(post => {
                    const postElement = document.createElement('div');
                    postElement.className = 'forum-moderation-post-card';
                    const statusClass = `forum-moderation-status-${post.status}`;
                    const buttonText = post.status === 'active' ? 'Hide' : 'Activate';
                    const buttonClass = post.status === 'active' ? 'forum-moderation-hide-btn' : 'forum-moderation-activate-btn';
                    postElement.innerHTML = `
                        <h3 class="forum-moderation-post-title">${post.title}</h3>
                        <p class="forum-moderation-post-body">${post.body.substring(0, 100)}${post.body.length > 100 ? '...' : ''}</p>
                        <p class="forum-moderation-meta">Posted by ${post.user?.name || 'Anonymous'} on ${new Date(post.created_at).toLocaleDateString()}</p>
                        <p class="${statusClass}">Status: ${post.status.charAt(0).toUpperCase() + post.status.slice(1)}</p>
                        <div class="forum-moderation-actions-wrapper">
                            <button class="forum-moderation-action-btn ${buttonClass}" data-uuid="${post.uuid}" data-status="${post.status === 'active' ? 'hidden' : 'active'}">${buttonText} Post</button>
                            <a href="/community/forum/${post.uuid}" class="forum-moderation-action-btn forum-moderation-view-btn">View Post</a>
                        </div>
                    `;
                    postsList.appendChild(postElement);
                });
            }

            // Fetch comments
            const commentsUrl = status ? `/api/forum/comments?status=${encodeURIComponent(status)}` : '/api/forum/comments';
            console.log('Comments API URL:', commentsUrl); // Debug: Log URL
            const commentsResponse = await fetch(commentsUrl, {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user()->token }}',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const commentsResult = await commentsResponse.json();
            console.log('Comments API Response:', commentsResult); // Debug: Log response

            if (!commentsResponse.ok) {
                throw new Error(commentsResult.error || 'Failed to fetch comments');
            }

            if (commentsResult.comments.length === 0) {
                commentsList.innerHTML = '<div class="forum-moderation-no-data">No comments found.</div>';
            } else {
                commentsResult.comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.className = 'forum-moderation-comment-card';
                    const statusClass = `forum-moderation-status-${comment.status}`;
                    const buttonText = comment.status === 'active' ? 'Hide' : 'Activate';
                    const buttonClass = comment.status === 'active' ? 'forum-moderation-hide-btn' : 'forum-moderation-activate-btn';
                    commentElement.innerHTML = `
                        <p class="forum-moderation-comment-body">${comment.comment.substring(0, 100)}${comment.comment.length > 100 ? '...' : ''}</p>
                        <p class="forum-moderation-meta">Posted by ${comment.user?.name || 'Anonymous'} on ${new Date(comment.created_at).toLocaleDateString()}</p>
                        <p class="${statusClass}">Status: ${comment.status.charAt(0).toUpperCase() + comment.status.slice(1)}</p>
                        <div class="forum-moderation-actions-wrapper">
                            <button class="forum-moderation-action-btn ${buttonClass}" data-uuid="${comment.uuid}" data-status="${comment.status === 'active' ? 'hidden' : 'active'}">${buttonText} Comment</button>
                            <a href="/community/forum/${comment.forum_post_id}" class="forum-moderation-action-btn forum-moderation-view-btn">View Post</a>
                        </div>
                    `;
                    commentsList.appendChild(commentElement);
                });
            }

            // Add event listeners for moderation buttons
            document.querySelectorAll('.forum-moderation-action-btn:not(.forum-moderation-view-btn)').forEach(button => {
                button.addEventListener('click', async () => {
                    const uuid = button.dataset.uuid;
                    const status = button.dataset.status;
                    await moderateItem(button.classList.contains('forum-moderation-comment-card') ? 'comment' : 'post', uuid, status);
                });
            });

        } catch (error) {
            console.error('Error fetching posts/comments:', error);
            errorMessage.textContent = error.message || 'Failed to load data. Please try again.';
            errorMessage.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    async function moderateItem(type, uuid, status) {
        if (!confirm(`Are you sure you want to ${status === 'active' ? 'activate' : 'hide'} this ${type}?`)) return;

        try {
            const response = await fetch(`/api/forum/${type === 'post' ? uuid : `comments/${uuid}`}/moderate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer {{ auth()->user()->token }}',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status }),
                credentials: 'include'
            });

            const data = await response.json();
            console.log('Moderation Response:', data); // Debug: Log response

            if (!response.ok) {
                throw new Error(data.error || `Failed to moderate ${type}`);
            }

            successMessage.textContent = data.message || `${type.charAt(0).toUpperCase() + type.slice(1)} ${status === 'active' ? 'activated' : 'hidden'} successfully.`;
            successMessage.classList.remove('hidden');
            setTimeout(() => successMessage.classList.add('hidden'), 3000);
            fetchPostsAndComments(statusFilter.value);
        } catch (error) {
            console.error(`Error moderating ${type}:`, error);
            errorMessage.textContent = error.message || `Failed to moderate ${type}. Please try again.`;
            errorMessage.classList.remove('hidden');
        }
    }

    statusFilter.addEventListener('change', () => {
        console.log('Status filter changed:', statusFilter.value); // Debug: Log filter change
        fetchPostsAndComments(statusFilter.value);
    });

    statusFilter.addEventListener('click', () => {
        console.log('Status filter clicked:', statusFilter.value); // Debug: Log click
    });

    fetchPostsAndComments(); // Initial load
});
</script>
@endsection