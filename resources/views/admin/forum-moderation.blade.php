@extends('layouts.app')

@section('content')

<div class="forum-moderation-container">
    <div class="forum-moderation-content">
        <h1 class="forum-moderation-title">Forum Moderation Dashboard</h1>
        
        <div class="forum-moderation-card">
            <div class="forum-moderation-header">
                <h2 class="forum-moderation-subtitle">Forum Posts</h2>
                <div class="forum-moderation-filter">
                    <label for="forum-moderation-status-filter">Filter by Status:</label>
                    <select id="forum-moderation-status-filter">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="hidden">Hidden</option>
                        <option value="reported">Reported</option>
                    </select>
                </div>
            </div>
            <div id="forum-moderation-loading" class="hidden">Loading...</div>
            <div id="forum-moderation-error" class="hidden"></div>
            
            <div class="forum-moderation-grid" id="forum-moderation-grid">
                <!-- Populated dynamically via JavaScript -->
            </div>
        </div>

        <!-- Post Detail Modal -->
        <div id="post-detail-modal" class="forum-modal hidden">
            <div class="forum-modal-content">
                <span id="post-modal-close" class="forum-modal-close">&times;</span>
                <div id="post-modal-body">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for forum moderation');

    const loading = document.getElementById('forum-moderation-loading');
    const error = document.getElementById('forum-moderation-error');
    const grid = document.getElementById('forum-moderation-grid');
    const statusFilter = document.getElementById('forum-moderation-status-filter');
    const modal = document.getElementById('post-detail-modal');

    if (!statusFilter) {
        console.error('Status filter element not found!');
        error.textContent = 'Status filter dropdown not found in the DOM.';
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

    async function fetchPosts(status = '') {
        console.log('Fetching posts with status:', status);
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        grid.innerHTML = '';

        try {
            const url = status ? `/api/admin/forum-posts?status=${encodeURIComponent(status)}` : '/api/admin/forum-posts';
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
                    grid.innerHTML = '<div class="forum-moderation-no-data">No posts found.</div>';
                } else {
                    postData.forEach(post => {
                        const tags = Array.isArray(post.tags) ? post.tags.map(tag => tag.name).join(', ') : 'N/A';
                        const statusClass = `forum-moderation-status-${post.status}`;
                        const pinnedClass = post.pinned ? 'forum-moderation-pinned' : '';
                        const reportCount = post.reports ? post.reports.length : 0;
                        let actions = `
                            <button onclick="handleAction('${post.uuid}', 'pin')" class="forum-moderation-action-btn forum-moderation-pin-btn" title="${post.pinned ? 'Unpin' : 'Pin'}"></button>
                            <button onclick="handleAction('${post.uuid}', 'delete')" class="forum-moderation-action-btn forum-moderation-delete-btn" title="Delete"></button>
                        `;
                        if (reportCount > 0) {
                            actions += `<button class="forum-moderation-action-btn forum-moderation-report-btn" data-uuid="${post.uuid}" title="Review Reports (${reportCount})"></button>`;
                        }

                        const card = document.createElement('div');
                        card.className = `forum-moderation-grid-item ${pinnedClass}`;
                        card.innerHTML = `
                            <h3>${post.title}</h3>
                            <p><strong>Author:</strong> ${post.user ? post.user.first_name + ' ' + post.user.last_name : 'N/A'}</p>
                            <p><strong>Tags:</strong> ${tags}</p>
                            <p><strong>Status:</strong> <span class="${statusClass}">${post.status.charAt(0).toUpperCase() + post.status.slice(1)}</span></p>
                            <p><strong>Reports:</strong> ${reportCount}</p>
                            <div class="forum-moderation-actions-wrapper">
                                ${actions}
                                <button class="forum-moderation-action-btn forum-moderation-view-btn" data-uuid="${post.uuid}" title="View Details"></button>
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

    window.handleAction = async function(uuid, action) {
        if (!confirm(`Are you sure you want to ${action} this post?`)) return;

        try {
            const response = await fetch(`/api/admin/forum-posts/${action}/${uuid}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Action Response:', result);

            if (response.ok && result.success) {
                fetchPosts(statusFilter.value);
                alert(result.message);
            } else {
                error.textContent = result.message || `Failed to ${action} post. Please try again.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Action error:', err);
            error.textContent = 'Unable to process action. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // View Details modal open
    function openPostModal(post) {
        const modalBody = document.getElementById('post-modal-body');
        const tags = Array.isArray(post.tags) ? post.tags.map(tag => tag.name).join(', ') : 'N/A';
        const reports = post.reports || [];
        let reportsHtml = reports.length > 0
            ? reports.map(report => `
                <div class="forum-report-item">
                    <p><strong>Reported By:</strong> ${report.reported_by ? report.reported_by.first_name + ' ' + report.reported_by.last_name : 'N/A'}</p>
                    <p><strong>Reason:</strong> ${report.reason}</p>
                    <p><strong>Status:</strong> ${report.status.charAt(0).toUpperCase() + report.status.slice(1)}</p>
                    <button onclick="handleReportAction('${report.uuid}', 'resolve')" class="forum-moderation-action-btn forum-moderation-resolve-report-btn" title="Resolve Report"></button>
                    <button onclick="handleReportAction('${report.uuid}', 'dismiss')" class="forum-moderation-action-btn forum-moderation-dismiss-report-btn" title="Dismiss Report"></button>
                </div>
            `).join('')
            : '<p>No reports for this post.</p>';

        modalBody.innerHTML = `
            <h2>${post.title}</h2>
            <p><strong>Author:</strong> ${post.user ? post.user.first_name + ' ' + post.user.last_name : 'N/A'}</p>
            <p><strong>Email:</strong> ${post.user ? post.user.email : 'N/A'}</p>
            <p><strong>Body:</strong> ${post.body}</p>
            <p><strong>Tags:</strong> ${tags}</p>
            <p><strong>Status:</strong> ${post.status.charAt(0).toUpperCase() + post.status.slice(1)}</p>
            <p><strong>Pinned:</strong> ${post.pinned ? 'Yes' : 'No'}</p>
            <h3>Reports</h3>
            ${reportsHtml}
        `;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Handle Report Actions
    window.handleReportAction = async function(reportUuid, action) {
        if (!confirm(`Are you sure you want to ${action} this report?`)) return;

        try {
            const response = await fetch(`/api/admin/forum-reports/${action}/${reportUuid}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Report Action Response:', result);

            if (response.ok && result.success) {
                fetchPosts(statusFilter.value);
                alert(result.message);
            } else {
                error.textContent = result.message || `Failed to ${action} report. Please try again.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Report action error:', err);
            error.textContent = 'Unable to process report action. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // Close modal
    document.getElementById('post-modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });

    // Delegate click event for "View Details" and "Review Reports" buttons
    grid.addEventListener('click', function (e) {
        const viewTarget = e.target.closest('.forum-moderation-view-btn');
        const reportTarget = e.target.closest('.forum-moderation-report-btn');
        if (viewTarget || reportTarget) {
            const uuid = viewTarget ? viewTarget.getAttribute('data-uuid') : reportTarget.getAttribute('data-uuid');
            const post = postData.find(p => p.uuid === uuid);
            if (post) openPostModal(post);
        }
    });

    // Status filter change
    statusFilter.addEventListener('change', () => {
        console.log('Dropdown changed! Selected value:', statusFilter.value);
        fetchPosts(statusFilter.value);
    });

    fetchPosts(); // Initial load
});
</script>
@endsection