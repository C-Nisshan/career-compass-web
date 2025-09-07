@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/comment-moderation.css') }}">
@endpush

@extends('layouts.app')

@section('content')

<div class="comment-moderation-container">
    <div class="comment-moderation-content">
        <h1 class="comment-moderation-title">Comment Moderation Dashboard</h1>
        
        <div class="comment-moderation-card">
            <div class="comment-moderation-header">
                <h2 class="comment-moderation-subtitle">Forum Comments</h2>
                <div class="comment-moderation-filter">
                    <label for="comment-moderation-status-filter">Filter by Status:</label>
                    <select id="comment-moderation-status-filter">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="hidden">Hidden</option>
                        <option value="reported">Reported</option>
                    </select>
                </div>
            </div>
            <div id="comment-moderation-loading" class="hidden">Loading...</div>
            <div id="comment-moderation-error" class="hidden"></div>
            
            <div class="comment-moderation-grid" id="comment-moderation-grid">
                <!-- Populated dynamically via JavaScript -->
            </div>
        </div>

        <!-- Comment Detail Modal -->
        <div id="comment-detail-modal" class="comment-modal hidden">
            <div class="comment-modal-content">
                <span id="comment-modal-close" class="comment-modal-close">&times;</span>
                <div id="comment-modal-body">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for comment moderation');

    const loading = document.getElementById('comment-moderation-loading');
    const error = document.getElementById('comment-moderation-error');
    const grid = document.getElementById('comment-moderation-grid');
    const statusFilter = document.getElementById('comment-moderation-status-filter');
    const modal = document.getElementById('comment-detail-modal');

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

    let commentData = [];

    async function fetchComments(status = '') {
        console.log('Fetching comments with status:', status);
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        grid.innerHTML = '';

        try {
            const url = status ? `/api/admin/forum-comments?status=${encodeURIComponent(status)}` : '/api/admin/forum-comments';
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
                commentData = result.data;
                if (commentData.length === 0) {
                    grid.innerHTML = '<div class="comment-moderation-no-data">No comments found.</div>';
                } else {
                    commentData.forEach(comment => {
                        const reportCount = comment.reports ? comment.reports.length : 0;
                        let actions = `
                            <button onclick="handleAction('${comment.uuid}', 'hide')" class="comment-moderation-action-btn comment-moderation-hide-btn" title="Hide"></button>
                        `;
                        if (reportCount > 0) {
                            actions += `<button class="comment-moderation-action-btn comment-moderation-report-btn" data-uuid="${comment.uuid}" title="Review Reports (${reportCount})"></button>`;
                        }

                        const card = document.createElement('div');
                        card.className = `comment-moderation-grid-item`;
                        card.innerHTML = `
                            <p><strong>Comment:</strong> ${comment.comment.substring(0, 100)}${comment.comment.length > 100 ? '...' : ''}</p>
                            <p><strong>Author:</strong> ${comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'N/A'}</p>
                            <p><strong>Post:</strong> ${comment.post ? comment.post.title : 'N/A'}</p>
                            <p><strong>Status:</strong> <span class="comment-moderation-status-${comment.status}">${comment.status.charAt(0).toUpperCase() + comment.status.slice(1)}</span></p>
                            <p><strong>Reports:</strong> ${reportCount}</p>
                            <div class="comment-moderation-actions-wrapper">
                                ${actions}
                                <button class="comment-moderation-action-btn comment-moderation-view-btn" data-uuid="${comment.uuid}" title="View Details"></button>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                }
            } else {
                error.textContent = result.message || 'Failed to load comments. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch comments. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    window.handleAction = async function(uuid, action) {
        if (!confirm(`Are you sure you want to ${action} this comment?`)) return;

        try {
            const response = await fetch(`/api/admin/forum-comments/${action}/${uuid}`, {
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
                fetchComments(statusFilter.value);
                alert(result.message);
            } else {
                error.textContent = result.message || `Failed to ${action} comment. Please try again.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Action error:', err);
            error.textContent = 'Unable to process action. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // View Details modal open
    function openCommentModal(comment) {
        const modalBody = document.getElementById('comment-modal-body');
        const reports = comment.reports || [];
        let reportsHtml = reports.length > 0
            ? reports.map(report => `
                <div class="comment-report-item">
                    <p><strong>Reported By:</strong> ${report.reported_by ? report.reported_by.first_name + ' ' + report.reported_by.last_name : 'N/A'}</p>
                    <p><strong>Reason:</strong> ${report.reason}</p>
                    <p><strong>Status:</strong> ${report.status.charAt(0).toUpperCase() + report.status.slice(1)}</p>
                    <button onclick="handleReportAction('${report.uuid}', 'resolve')" class="comment-moderation-action-btn comment-moderation-resolve-report-btn" title="Resolve Report"></button>
                    <button onclick="handleReportAction('${report.uuid}', 'dismiss')" class="comment-moderation-action-btn comment-moderation-dismiss-report-btn" title="Dismiss Report"></button>
                </div>
            `).join('')
            : '<p>No reports for this comment.</p>';

        modalBody.innerHTML = `
            <h2>Comment Details</h2>
            <p><strong>Comment:</strong> ${comment.comment}</p>
            <p><strong>Author:</strong> ${comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'N/A'}</p>
            <p><strong>Email:</strong> ${comment.user ? comment.user.email : 'N/A'}</p>
            <p><strong>Post:</strong> ${comment.post ? comment.post.title : 'N/A'}</p>
            <p><strong>Status:</strong> ${comment.status.charAt(0).toUpperCase() + comment.status.slice(1)}</p>
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
                fetchComments(statusFilter.value);
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
    document.getElementById('comment-modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });

    // Delegate click event for "View Details" and "Review Reports" buttons
    grid.addEventListener('click', function (e) {
        const viewTarget = e.target.closest('.comment-moderation-view-btn');
        const reportTarget = e.target.closest('.comment-moderation-report-btn');
        if (viewTarget || reportTarget) {
            const uuid = viewTarget ? viewTarget.getAttribute('data-uuid') : reportTarget.getAttribute('data-uuid');
            const comment = commentData.find(c => c.uuid === uuid);
            if (comment) openCommentModal(comment);
        }
    });

    // Status filter change
    statusFilter.addEventListener('change', () => {
        console.log('Dropdown changed! Selected value:', statusFilter.value);
        fetchComments(statusFilter.value);
    });

    fetchComments(); // Initial load
});
</script>
@endsection