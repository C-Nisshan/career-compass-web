@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="forum">
    <div class="container">
        <h1 class="section-title">Mentor Community Forum</h1>
        <p>Engage with students and peers. Pin important posts to highlight key discussions.</p>
        <div id="forum-posts"></div>
        <div id="pagination" class="pagination"></div>
        @auth
            <a href="{{ route('forum.create') }}" class="btn-primary">Create New Post</a>
        @endauth
        <div id="error-message" class="error" style="display: none;"></div>
        <div id="success-message" class="success" style="display: none;"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;

    async function fetchPosts(page = 1) {
        try {
            const response = await fetch(`/api/forum?page=${page}`, {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user()->token }}',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error((await response.json()).error || 'Failed to fetch posts');
            }

            const data = await response.json();
            const postsContainer = document.getElementById('forum-posts');
            const errorMessage = document.getElementById('error-message');
            const successMessage = document.getElementById('success-message');
            postsContainer.innerHTML = '';
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';

            if (data.posts.data.length === 0) {
                postsContainer.innerHTML = '<p>No posts yet. Start the conversation!</p>';
                return;
            }

            data.posts.data.forEach(post => {
                const postElement = document.createElement('div');
                postElement.className = 'post-card fade-in';
                postElement.innerHTML = `
                    <h3>${post.title}</h3>
                    <p>${post.body.substring(0, 200)}${post.body.length > 200 ? '...' : ''}</p>
                    <p><small>Posted by ${post.user?.name || 'Anonymous'} on ${new Date(post.created_at).toLocaleDateString()}</small></p>
                    <p>Status: ${post.status}${post.pinned ? ' (Pinned)' : ''}</p>
                    <a href="/community/forum/${post.uuid}" class="btn-primary">View Post</a>
                    @auth
                        @if(auth()->user()->role->value === 'mentor' || auth()->user()->role->value === 'admin')
                            <button class="pin-post btn-primary ml-2" data-uuid="${post.uuid}" data-action="${post.pinned ? 'unpin' : 'pin'}">
                                ${post.pinned ? 'Unpin' : 'Pin'} Post
                            </button>
                        @endif
                    @endauth
                `;
                postsContainer.appendChild(postElement);
            });

            // Add event listeners for pin/unpin buttons
            document.querySelectorAll('.pin-post').forEach(button => {
                button.addEventListener('click', async () => {
                    const uuid = button.dataset.uuid;
                    const action = button.dataset.action;
                    try {
                        const response = await fetch(`/api/forum/${uuid}/${action}`, {
                            method: 'POST',
                            headers: {
                                'Authorization': 'Bearer {{ auth()->user()->token }}',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.error || `Failed to ${action} post`);
                        }

                        successMessage.textContent = data.message;
                        successMessage.style.display = 'block';
                        setTimeout(() => successMessage.style.display = 'none', 3000);
                        fetchPosts(currentPage);
                    } catch (error) {
                        errorMessage.textContent = error.message;
                        errorMessage.style.display = 'block';
                        console.error(`Error ${action}ning post:`, error);
                    }
                });
            });

            // Render pagination
            const paginationContainer = document.getElementById('pagination');
            paginationContainer.innerHTML = '';
            if (data.posts.last_page > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Previous';
                prevButton.disabled = data.posts.current_page === 1;
                prevButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        fetchPosts(currentPage);
                    }
                });
                paginationContainer.appendChild(prevButton);

                const nextButton = document.createElement('button');
                nextButton.textContent = 'Next';
                nextButton.disabled = data.posts.current_page === data.posts.last_page;
                nextButton.addEventListener('click', () => {
                    if (currentPage < data.posts.last_page) {
                        currentPage++;
                        fetchPosts(currentPage);
                    }
                });
                paginationContainer.appendChild(nextButton);
            }
        } catch (error) {
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            console.error('Error fetching posts:', error);
        }
    }

    fetchPosts();
});
</script>

<style>
.pagination {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}
.pagination button {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.pagination button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}
.error {
    color: red;
    margin-top: 10px;
}
.success {
    color: green;
    margin-top: 10px;
}
.post-card {
    background-color: #f9f9f9;
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 4px;
}
.btn-primary {
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-primary:hover {
    background-color: #0056b3;
}
</style>
@endsection