@extends('layouts.app')

@section('content')
<div class="animated-bg"></div>
<div class="particles" id="particles"></div>

<section class="forum-post">
    <div class="container">
        <h1 class="section-title" id="post-title"></h1>
        <p><small>Posted by <span id="post-author"></span> on <span id="post-date"></span></small></p>
        <div id="post-tags"></div>
        <div id="post-body"></div>
        <h2>Comments</h2>
        <div id="post-comments"></div>
        @auth
            <form id="comment-form" class="comment-form">
                @csrf
                <div class="form-group">
                    <label for="comment">Add a Comment</label>
                    <textarea id="comment" name="comment" placeholder="Your comment" required></textarea>
                    <span class="error" id="comment-error"></span>
                </div>
                <button type="submit" class="btn-primary">Submit Comment</button>
            </form>
        @endauth
        <div id="success-message" class="success" style="display: none;"></div>
        <div id="error-message" class="error" style="display: none;"></div>
        @auth
            @if(auth()->user()->role->value === 'mentor' || auth()->user()->role->value === 'admin')
                <button id="pin-button" class="btn-primary" style="display: none;">Pin Post</button>
                <button id="unpin-button" class="btn-primary" style="display: none;">Unpin Post</button>
            @endif
            @if(auth()->user()->role->value === 'admin')
                <form id="moderate-form" class="moderate-form">
                    @csrf
                    <div class="form-group">
                        <label for="status">Moderate Post</label>
                        <select id="status" name="status">
                            <option value="active">Active</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Moderate</button>
                </form>
            @endif
        @endauth
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const postUuid = window.location.pathname.split('/').pop();
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const commentError = document.getElementById('comment-error');

    async function fetchPost() {
        try {
            const response = await fetch(`/api/forum/${postUuid}`, {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user() ? auth()->user()->token : '' }}',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error((await response.json()).error || 'Failed to fetch post');
            }

            const data = await response.json();
            const post = data.post;

            document.getElementById('post-title').textContent = post.title;
            document.getElementById('post-author').textContent = post.user?.name || 'Anonymous';
            document.getElementById('post-date').textContent = new Date(post.created_at).toLocaleDateString();
            document.getElementById('post-body').textContent = post.body;
            document.getElementById('post-tags').innerHTML = post.tags.map(tag => `<span class="tag">${tag.name}</span>`).join(' ');

            const commentsContainer = document.getElementById('post-comments');
            commentsContainer.innerHTML = post.comments.length === 0 ? '<p>No comments yet.</p>' : '';
            post.comments.forEach(comment => {
                const commentElement = document.createElement('div');
                commentElement.className = 'comment';
                commentElement.innerHTML = `
                    <p>${comment.comment}</p>
                    <p><small>Posted by ${comment.user?.name || 'Anonymous'} on ${new Date(comment.created_at).toLocaleDateString()}</small></p>
                `;
                commentsContainer.appendChild(commentElement);
            });

            @auth
                if ({{ auth()->user()->role->value === 'mentor' || auth()->user()->role->value === 'admin' }}) {
                    document.getElementById('pin-button').style.display = post.pinned ? 'none' : 'inline-block';
                    document.getElementById('unpin-button').style.display = post.pinned ? 'inline-block' : 'none';
                }
            @endauth
        } catch (error) {
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            console.error('Error fetching post:', error);
        }
    }

    @auth
        document.getElementById('comment-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const comment = document.getElementById('comment').value;
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
            commentError.textContent = '';

            try {
                const response = await fetch(`/api/forum/${postUuid}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer {{ auth()->user()->token }}',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ comment }),
                });

                const data = await response.json();

                if (!response.ok) {
                    commentError.textContent = data.errors?.comment?.[0] || data.error || 'Failed to create comment';
                    errorMessage.style.display = 'block';
                    return;
                }

                successMessage.textContent = data.message;
                successMessage.style.display = 'block';
                document.getElementById('comment').value = '';
                fetchPost();
            } catch (error) {
                errorMessage.textContent = 'An error occurred while creating the comment';
                errorMessage.style.display = 'block';
                console.error('Error creating comment:', error);
            }
        });

        @if(auth()->user()->role->value === 'mentor' || auth()->user()->role->value === 'admin')
            document.getElementById('pin-button').addEventListener('click', async () => {
                try {
                    const response = await fetch(`/api/forum/${postUuid}/pin`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer {{ auth()->user()->token }}',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to pin post');
                    }

                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    fetchPost();
                } catch (error) {
                    errorMessage.textContent = error.message;
                    errorMessage.style.display = 'block';
                    console.error('Error pinning post:', error);
                }
            });

            document.getElementById('unpin-button').addEventListener('click', async () => {
                try {
                    const response = await fetch(`/api/forum/${postUuid}/unpin`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer {{ auth()->user()->token }}',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to unpin post');
                    }

                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    fetchPost();
                } catch (error) {
                    errorMessage.textContent = error.message;
                    errorMessage.style.display = 'block';
                    console.error('Error unpinning post:', error);
                }
            });
        @endif

        @if(auth()->user()->role->value === 'admin')
            document.getElementById('moderate-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const status = document.getElementById('status').value;

                try {
                    const response = await fetch(`/api/forum/${postUuid}/moderate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{ auth()->user()->token }}',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to moderate post');
                    }

                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    fetchPost();
                } catch (error) {
                    errorMessage.textContent = error.message;
                    errorMessage.style.display = 'block';
                    console.error('Error moderating post:', error);
                }
            });
        @endif
    @endauth

    fetchPost();
});
</script>

<style>
.tag {
    display: inline-block;
    background-color: #e0e0e0;
    padding: 4px 8px;
    margin-right: 5px;
    border-radius: 4px;
}
.comment {
    border-bottom: 1px solid #e0e0e0;
    padding: 10px 0;
}
.comment-form, .moderate-form {
    margin-top: 20px;
}
.success {
    color: green;
    margin-top: 10px;
}
.error {
    color: red;
    margin-top: 10px;
}
</style>
@endsection