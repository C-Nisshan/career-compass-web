@extends('layouts.app')

@section('content')
<div class="browse-forum-container">
    <div class="browse-forum-content">
        <h1 class="browse-forum-title">Mentor Community Forum</h1>
        
        <div class="browse-forum-card">
            <div class="browse-forum-header">
                <h2 class="browse-forum-subtitle">Browse Posts</h2>
                <div class="browse-forum-actions">
                    <a href="{{ route('mentor.forum.create-post') }}" class="browse-forum-create-btn">Create Post</a>
                    <div class="browse-forum-search">
                        <input type="text" id="browse-forum-search-input" placeholder="Search by title or tags..." aria-label="Search posts">
                        <button id="browse-forum-search-btn" class="browse-forum-search-btn" title="Search"></button>
                    </div>
                </div>
            </div>
            <div id="browse-forum-loading" class="hidden">Loading...</div>
            <div id="browse-forum-error" class="hidden"></div>
            
            <div class="browse-forum-grid" id="browse-forum-grid">
                <!-- Populated dynamically via JavaScript -->
            </div>
        </div>

        <!-- Post Detail Modal -->
        <div id="browse-post-detail-modal" class="browse-forum-modal hidden">
            <div class="browse-forum-modal-content">
                <span id="browse-post-modal-close" class="browse-forum-modal-close">&times;</span>
                <div id="browse-post-modal-body">
                    <!-- Populated dynamically -->
                </div>
                <div id="browse-comment-loading" class="hidden browse-comment-loading">Loading...</div>
                <div id="browse-comment-error" class="hidden browse-comment-error"></div>
                <form id="browse-comment-form" class="browse-comment-form">
                    @csrf
                    <div class="browse-comment-form-group">
                        <label for="browse-comment-input">Add a Comment</label>
                        <textarea id="browse-comment-input" name="comment" placeholder="Write your comment..." required></textarea>
                    </div>
                    <div class="browse-comment-form-actions">
                        <button type="submit" class="browse-comment-submit-btn">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for mentor forum browse posts');

    const loading = document.getElementById('browse-forum-loading');
    const error = document.getElementById('browse-forum-error');
    const grid = document.getElementById('browse-forum-grid');
    const searchInput = document.getElementById('browse-forum-search-input');
    const searchBtn = document.getElementById('browse-forum-search-btn');
    const modal = document.getElementById('browse-post-detail-modal');
    const modalBody = document.getElementById('browse-post-modal-body');
    const commentLoading = document.getElementById('browse-comment-loading');
    const commentError = document.getElementById('browse-comment-error');
    const commentForm = document.getElementById('browse-comment-form');

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
            const url = search ? `/api/mentor/forum-posts?search=${encodeURIComponent(search)}` : '/api/mentor/forum-posts';
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
                    grid.innerHTML = '<div class="browse-forum-no-data">No posts found.</div>';
                } else {
                    postData.forEach(post => {
                        const tags = Array.isArray(post.tags) ? post.tags.map(tag => tag.name).join(', ') : 'N/A';
                        const pinnedClass = post.pinned ? 'browse-forum-pinned' : '';
                        const guidanceBadge = post.mentor_guidance ? '<span class="browse-forum-guidance-badge">Mentor Guidance</span>' : '';
                        const authorName = post.user ? `${post.user.first_name || ''} ${post.user.last_name || ''}`.trim() || 'Unknown User' : 'Unknown User';
                        const card = document.createElement('div');
                        card.className = `browse-forum-grid-item ${pinnedClass}`;
                        card.innerHTML = `
                            <h3>${post.title} ${guidanceBadge}</h3>
                            <p><strong>Author:</strong> ${authorName}</p>
                            <p><strong>Tags:</strong> ${tags}</p>
                            <p><strong>Comments:</strong> ${post.comments_count || 0}</p>
                            <p><strong>Upvotes:</strong> ${post.votes_count || 0}</p>
                            <div class="browse-forum-actions-wrapper">
                                <button class="browse-forum-action-btn browse-forum-view-btn" data-uuid="${post.uuid}" title="View Details"></button>
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
            const response = await fetch(`/api/mentor/forum-posts/${uuid}`, {
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
                console.error('Failed to fetch post:', result.message || 'Unknown error');
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

    function openPostModal(post, canPin) {
        const tags = Array.isArray(post.tags) ? post.tags.map(tag => `<span class="browse-comment-tag">${tag.name}</span>`).join('') : 'N/A';
        const comments = post.comments || [];
        const authorName = post.user ? `${post.user.first_name || ''} ${post.user.last_name || ''}`.trim() || 'Unknown User' : 'Unknown User';
        const commentsHtml = comments.length > 0
            ? comments.map(comment => `
                <div class="browse-comment-item">
                    <p><strong>${comment.user ? `${comment.user.first_name || ''} ${comment.user.last_name || ''}`.trim() || 'Unknown User' : 'Unknown User'}:</strong> ${comment.comment}</p>
                    <p><small>Posted: ${new Date(comment.created_at).toLocaleString()}</small></p>
                </div>
            `).join('')
            : '<p class="browse-comment-no-data">No comments yet.</p>';
        const guidanceBadge = post.mentor_guidance ? '<span class="browse-comment-guidance-badge">Mentor Guidance</span>' : '';

        modalBody.innerHTML = `
            <h2>${post.title} ${guidanceBadge}</h2>
            <p><strong>Author:</strong> ${authorName}</p>
            <p><strong>Email:</strong> ${post.user ? post.user.email || 'N/A' : 'N/A'}</p>
            <p><strong>Body:</strong> ${post.body}</p>
            <p><strong>Tags:</strong> ${tags}</p>
            <p><strong>Pinned:</strong> ${post.pinned ? 'Yes' : 'No'}</p>
            <p><strong>Upvotes:</strong> ${post.votes_count || 0}</p>
            ${canPin ? `<button id="browse-comment-pin-btn" class="browse-comment-pin-btn" data-uuid="${post.uuid}" data-pinned="${post.pinned}">${post.pinned ? 'Unpin Post' : 'Pin Post'}</button>` : ''}
            <h3>Comments</h3>
            <div id="browse-comment-list">${commentsHtml}</div>
        `;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    grid.addEventListener('click', async function (e) {
        const viewTarget = e.target.closest('.browse-forum-view-btn');
        if (viewTarget) {
            const uuid = viewTarget.getAttribute('data-uuid');
            console.log('View button clicked for post:', uuid);
            const data = await fetchPost(uuid);
            if (data) {
                openPostModal(data.post, data.can_pin);
            } else {
                console.error('No data returned for post:', uuid);
            }
        }
    });

    commentForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('Comment form submitted');
        commentLoading.classList.remove('hidden');
        commentError.classList.add('hidden');

        const formData = new FormData(commentForm);
        const uuid = document.getElementById('browse-comment-pin-btn')?.getAttribute('data-uuid') || document.querySelector('.browse-comment-pin-btn')?.getAttribute('data-uuid');
        if (!uuid) {
            console.error('No post UUID found for commenting');
            commentError.textContent = 'No post selected for commenting.';
            commentError.classList.remove('hidden');
            commentLoading.classList.add('hidden');
            return;
        }

        const data = { comment: formData.get('comment') };

        try {
            const response = await fetch(`/api/mentor/forum-posts/${uuid}/comment`, {
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
                const commentList = document.getElementById('browse-comment-list');
                const authorName = comment.user ? `${comment.user.first_name || ''} ${comment.user.last_name || ''}`.trim() || 'Unknown User' : 'Unknown User';
                commentList.insertAdjacentHTML('beforeend', `
                    <div class="browse-comment-item">
                        <p><strong>${authorName}:</strong> ${comment.comment}</p>
                        <p><small>Posted: ${new Date(comment.created_at).toLocaleString()}</small></p>
                    </div>
                `);
                commentForm.reset();
                const noComments = commentList.querySelector('.browse-comment-no-data');
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

    document.getElementById('browse-post-modal-close').addEventListener('click', () => {
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
