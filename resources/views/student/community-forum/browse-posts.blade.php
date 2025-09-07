@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/community-forum/browse-posts.css') }}">
@endpush

@extends('layouts.app')

@section('content')
<div class="student-forum-container">
    <div class="student-forum-content">
        <h1 class="student-forum-title">Community Forum</h1>
        
        <div class="student-forum-card">
            <div class="student-forum-header">
                <h2 class="student-forum-subtitle">Browse Posts</h2>
                <div class="student-forum-search">
                    <input type="text" id="student-forum-search-input" placeholder="Search by title or tags..." aria-label="Search posts">
                    <button id="student-forum-search-btn" class="student-forum-search-btn" title="Search"></button>
                </div>
            </div>
            <div id="student-forum-loading" class="hidden">Loading...</div>
            <div id="student-forum-error" class="hidden"></div>
            
            <div class="student-forum-grid" id="student-forum-grid">
                <!-- Populated dynamically via JavaScript -->
            </div>
        </div>

        <!-- Post Detail Modal -->
        <div id="student-post-detail-modal" class="student-forum-modal hidden">
            <div class="student-forum-modal-content">
                <span id="student-post-modal-close" class="student-forum-modal-close">&times;</span>
                <div id="student-post-modal-body">
                    <!-- Populated dynamically -->
                </div>
                <div id="student-comment-loading" class="hidden student-comment-loading">Loading...</div>
                <div id="student-comment-error" class="hidden student-comment-error"></div>
                <form id="student-comment-form" class="student-comment-form">
                    @csrf
                    <div class="student-comment-form-group">
                        <label for="student-comment-input">Add a Comment</label>
                        <textarea id="student-comment-input" name="comment" placeholder="Write your comment..." required></textarea>
                    </div>
                    <div class="student-comment-form-actions">
                        <button type="submit" class="student-comment-submit-btn">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for student forum browse posts');

    const loading = document.getElementById('student-forum-loading');
    const error = document.getElementById('student-forum-error');
    const grid = document.getElementById('student-forum-grid');
    const searchInput = document.getElementById('student-forum-search-input');
    const searchBtn = document.getElementById('student-forum-search-btn');
    const modal = document.getElementById('student-post-detail-modal');
    const modalBody = document.getElementById('student-post-modal-body');
    const commentLoading = document.getElementById('student-comment-loading');
    const commentError = document.getElementById('student-comment-error');
    const commentForm = document.getElementById('student-comment-form');

    if (!searchInput || !searchBtn || !grid || !modal || !modalBody || !commentForm) {
        console.error('Required elements not found!');
        error.textContent = 'Page elements not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    let postData = [];

    async function fetchPosts(search = '') {
        console.log('Fetching posts with search:', search);
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        grid.innerHTML = '';

        try {
            const url = search ? `/api/student/forum-posts?search=${encodeURIComponent(search)}` : '/api/student/forum-posts';
            console.log('API URL:', url);
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('API Response:', result);

            if (response.ok && result.success) {
                postData = result.data;
                if (postData.length === 0) {
                    grid.innerHTML = '<div class="student-forum-no-data">No posts found.</div>';
                } else {
                    postData.forEach(post => {
                        const tags = Array.isArray(post.tags) ? post.tags.map(tag => tag.name).join(', ') : 'N/A';
                        const pinnedClass = post.pinned ? 'student-forum-pinned' : '';
                        const card = document.createElement('div');
                        card.className = `student-forum-grid-item ${pinnedClass}`;
                        card.innerHTML = `
                            <h3>${post.title}</h3>
                            <p><strong>Author:</strong> ${post.user ? post.user.first_name + ' ' + post.user.last_name : 'N/A'}</p>
                            <p><strong>Tags:</strong> ${tags}</p>
                            <p><strong>Comments:</strong> ${post.comments_count || 0}</p>
                            <p><strong>Upvotes:</strong> ${post.votes_count || 0}</p>
                            <div class="student-forum-actions-wrapper">
                                <button class="student-forum-action-btn student-forum-view-btn" data-uuid="${post.uuid}" title="View Details"></button>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                }
            } else {
                error.textContent = result.message || 'Failed to load posts. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch posts. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    async function fetchPost(uuid) {
        console.log('Fetching post:', uuid);
        commentLoading.classList.remove('hidden');
        commentError.classList.add('hidden');

        try {
            const response = await fetch(`/api/student/forum-posts/${uuid}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Post API Response:', result);

            if (response.ok && result.success) {
                return result.data;
            } else {
                commentError.textContent = result.message || 'Failed to load post details. Please try again.';
                commentError.classList.remove('hidden');
                return null;
            }
        } catch (err) {
            console.error('Fetch post error:', err);
            commentError.textContent = 'Unable to fetch post details. Please check your connection.';
            commentError.classList.remove('hidden');
            return null;
        } finally {
            commentLoading.classList.add('hidden');
        }
    }

    function openPostModal(post, hasVoted) {
        const tags = Array.isArray(post.tags) ? post.tags.map(tag => `<span class="student-comment-tag">${tag.name}</span>`).join('') : 'N/A';
        const comments = post.comments || [];
        const commentsHtml = comments.length > 0
            ? comments.map(comment => `
                <div class="student-comment-item">
                    <p><strong>${comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'N/A'}:</strong> ${comment.comment}</p>
                    <p><small>Posted: ${new Date(comment.created_at).toLocaleString()}</small></p>
                </div>
            `).join('')
            : '<p class="student-comment-no-data">No comments yet.</p>';

        modalBody.innerHTML = `
            <h2>${post.title}</h2>
            <p><strong>Author:</strong> ${post.user ? post.user.first_name + ' ' + post.user.last_name : 'N/A'}</p>
            <p><strong>Email:</strong> ${post.user ? post.user.email : 'N/A'}</p>
            <p><strong>Body:</strong> ${post.body}</p>
            <p><strong>Tags:</strong> ${tags}</p>
            <p><strong>Pinned:</strong> ${post.pinned ? 'Yes' : 'No'}</p>
            <p><strong>Upvotes:</strong> <span id="student-comment-vote-count">${post.votes_count || 0}</span></p>
            <button id="student-comment-vote-btn" class="student-comment-vote-btn" data-uuid="${post.uuid}" data-voted="${hasVoted}">${hasVoted ? 'Remove Upvote' : 'Upvote'}</button>
            <h3>Comments</h3>
            <div id="student-comment-list">${commentsHtml}</div>
        `;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    grid.addEventListener('click', async function (e) {
        const viewTarget = e.target.closest('.student-forum-view-btn');
        if (viewTarget) {
            const uuid = viewTarget.getAttribute('data-uuid');
            const data = await fetchPost(uuid);
            if (data) {
                openPostModal(data.post, data.has_voted);
            }
        }
    });

    modalBody.addEventListener('click', async function (e) {
        if (e.target.id === 'student-comment-vote-btn') {
            const uuid = e.target.getAttribute('data-uuid');
            console.log('Vote button clicked for post:', uuid);
            commentLoading.classList.remove('hidden');
            commentError.classList.add('hidden');

            try {
                const response = await fetch(`/api/student/forum-posts/${uuid}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'include'
                });

                const result = await response.json();
                console.log('Vote API Response:', result);

                if (response.ok && result.success) {
                    const voteCountSpan = document.getElementById('student-comment-vote-count');
                    voteCountSpan.textContent = result.data.vote_count;
                    e.target.textContent = result.data.has_voted ? 'Remove Upvote' : 'Upvote';
                    e.target.dataset.voted = result.data.has_voted;
                } else {
                    commentError.textContent = result.message || 'Failed to toggle vote. Please try again.';
                    commentError.classList.remove('hidden');
                }
            } catch (err) {
                console.error('Vote error:', err);
                commentError.textContent = 'Unable to toggle vote. Please check your connection.';
                commentError.classList.remove('hidden');
            } finally {
                commentLoading.classList.add('hidden');
            }
        }
    });

    commentForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('Comment form submitted');
        commentLoading.classList.remove('hidden');
        commentError.classList.add('hidden');

        const formData = new FormData(commentForm);
        const uuid = document.getElementById('student-comment-vote-btn')?.getAttribute('data-uuid');
        if (!uuid) {
            commentError.textContent = 'No post selected for commenting.';
            commentError.classList.remove('hidden');
            commentLoading.classList.add('hidden');
            return;
        }

        const data = { comment: formData.get('comment') };

        try {
            const response = await fetch(`/api/student/forum-posts/${uuid}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Comment API Response:', result);

            if (response.ok && result.success) {
                const comment = result.data;
                const commentList = document.getElementById('student-comment-list');
                commentList.insertAdjacentHTML('beforeend', `
                    <div class="student-comment-item">
                        <p><strong>${comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'N/A'}:</strong> ${comment.comment}</p>
                        <p><small>Posted: ${new Date(comment.created_at).toLocaleString()}</small></p>
                    </div>
                `);
                commentForm.reset();
                const noComments = commentList.querySelector('.student-comment-no-data');
                if (noComments) noComments.remove();
            } else {
                commentError.textContent = result.message || 'Failed to add comment. Please try again.';
                commentError.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Comment error:', err);
            commentError.textContent = 'Unable to add comment. Please check your connection.';
            commentError.classList.remove('hidden');
        } finally {
            commentLoading.classList.add('hidden');
        }
    });

    document.getElementById('student-post-modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });

    searchBtn.addEventListener('click', () => {
        console.log('Search button clicked! Search term:', searchInput.value);
        fetchPosts(searchInput.value.trim());
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            console.log('Enter key pressed! Search term:', searchInput.value);
            fetchPosts(searchInput.value.trim());
        }
    });

    fetchPosts();
});
</script>
@endsection