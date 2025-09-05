document.addEventListener('DOMContentLoaded', () => {
    const statsElements = {
        totalUsers: document.querySelector('#total-users'),
        totalMentors: document.querySelector('#total-mentors'),
        activeForumPosts: document.querySelector('#active-forum-posts'),
        totalPredictions: document.querySelector('#total-predictions'),
        totalSuccessStories: document.querySelector('#total-success-stories'),
        totalQuizResults: document.querySelector('#total-quiz-results'),
        recentPredictions: document.querySelector('#recent-predictions'),
        recentPosts: document.querySelector('#recent-posts'),
    };

    // Error message container
    const errorMessage = document.querySelector('#error-message');

    // Set loading state
    Object.values(statsElements).forEach(element => {
        if (element) {
            element.innerHTML = 'Loading...';
            element.classList.add('admin-dashboard-loading');
        }
    });

    // Get JWT token from cookie
    const token = document.cookie.split('; ').find(row => row.startsWith('token='))?.split('=')[1];

    fetch('/api/admin/stats', {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Authorization': token ? `Bearer ${token}` : '',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Remove loading state
        Object.values(statsElements).forEach(element => {
            if (element) element.classList.remove('admin-dashboard-loading');
        });

        // Update stats
        statsElements.totalUsers.textContent = data.total_users || '0';
        statsElements.totalMentors.textContent = data.total_mentors || '0';
        statsElements.activeForumPosts.textContent = data.active_forum_posts || '0';
        statsElements.totalPredictions.textContent = data.total_predictions || '0';
        statsElements.totalSuccessStories.textContent = data.total_success_stories || '0';
        statsElements.totalQuizResults.textContent = data.total_quiz_results || '0';

        // Update recent predictions
        statsElements.recentPredictions.innerHTML = data.recent_predictions?.length
            ? data.recent_predictions.map(p => `
                <div class="flex items-center gap-4 admin-dashboard-border-b admin-dashboard-pb-2">
                    <div class="flex-1">
                        <p class="admin-dashboard-text-sm admin-dashboard-font-medium admin-dashboard-text-gray-700">${p.user || 'Unknown'}</p>
                        <p class="admin-dashboard-text-sm admin-dashboard-text-gray-500">Predicted at: ${p.predicted_at || 'N/A'}</p>
                    </div>
                </div>
            `).join('')
            : '<p class="admin-dashboard-text-gray-500">No recent predictions.</p>';

        // Update recent posts
        statsElements.recentPosts.innerHTML = data.recent_posts?.length
            ? data.recent_posts.map(p => `
                <div class="flex items-center gap-4 admin-dashboard-border-b admin-dashboard-pb-2">
                    <div class="flex-1">
                        <p class="admin-dashboard-text-sm admin-dashboard-font-medium admin-dashboard-text-gray-700">${p.title || 'Untitled'}</p>
                        <p class="admin-dashboard-text-sm admin-dashboard-text-gray-500">By: ${p.user || 'Unknown'}</p>
                    </div>
                </div>
            `).join('')
            : '<p class="admin-dashboard-text-gray-500">No recent posts.</p>';
    })
    .catch(error => {
        console.error('Error fetching stats:', error);
        Object.values(statsElements).forEach(element => {
            if (element) {
                element.innerHTML = 'Error loading data';
                element.classList.remove('admin-dashboard-loading');
            }
        });
        if (errorMessage) {
            errorMessage.textContent = 'Failed to load dashboard data. Please try again.';
            errorMessage.classList.remove('text-danger');
            errorMessage.classList.add('admin-dashboard-text-red-500');
        }
    });
});