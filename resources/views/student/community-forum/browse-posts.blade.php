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

    if (!searchInput || !searchBtn) {
        console.error('Search elements not found!');
        error.textContent = 'Search input or button not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    if (!modal) {
        console.error('Detail modal not found!');
        error.textContent = 'Detail modal not found in the DOM.';
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

    // View Details modal open
    function openPostModal(post) {
        const modalBody = document.getElementById('student-post-modal-body');
        const tags = Array.isArray(post.tags) ? post.tags.map(tag => tag.name).join(', ') : 'N/A';
        const comments = post.comments || [];
        const commentsHtml = comments.length > 0
            ? comments.map(comment => `
                <div class="student-forum-comment-item">
                    <p><strong>${comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'N/A'}:</strong> ${comment.comment}</p>
                    <p><small>Posted: ${new Date(comment.created_at).toLocaleString()}</small></p>
                </div>
            `).join('')
            : '<p>No comments yet.</p>';

        modalBody.innerHTML = `
            <h2>${post.title}</h2>
            <p><strong>Author:</strong> ${post.user ? post.user.first_name + ' ' + post.user.last_name : 'N/A'}</p>
            <p><strong>Email:</strong> ${post.user ? post.user.email : 'N/A'}</p>
            <p><strong>Body:</strong> ${post.body}</p>
            <p><strong>Tags:</strong> ${tags}</p>
            <p><strong>Pinned:</strong> ${post.pinned ? 'Yes' : 'No'}</p>
            <h3>Comments</h3>
            ${commentsHtml}
        `;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Close modal
    document.getElementById('student-post-modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });

    // Delegate click event for "View Details" button
    grid.addEventListener('click', function (e) {
        const viewTarget = e.target.closest('.student-forum-view-btn');
        if (viewTarget) {
            const uuid = viewTarget.getAttribute('data-uuid');
            const post = postData.find(p => p.uuid === uuid);
            if (post) openPostModal(post);
        }
    });

    // Search button click
    searchBtn.addEventListener('click', () => {
        console.log('Search button clicked! Search term:', searchInput.value);
        fetchPosts(searchInput.value.trim());
    });

    // Search on Enter key
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            console.log('Enter key pressed! Search term:', searchInput.value);
            fetchPosts(searchInput.value.trim());
        }
    });

    fetchPosts(); // Initial load
});
</script>
@endsection