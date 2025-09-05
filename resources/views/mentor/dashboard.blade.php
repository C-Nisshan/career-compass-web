@extends('layouts.app')

@section('content')
    <div class="container mentor-dashboard-container">
        <h1 class="section-title">Mentor Dashboard</h1>

        <div class="mentor-dashboard-grid">
            <!-- Forum Contributions -->
            <div class="mentor-dashboard-card">
                <h2 class="mentor-dashboard-card-title">Forum Contributions</h2>
                <div id="forum-contributions-list" class="mentor-dashboard-list">
                    <p class="mentor-dashboard-loading">Loading contributions...</p>
                </div>
            </div>

            <!-- Feedback from Students -->
            <div class="mentor-dashboard-card">
                <h2 class="mentor-dashboard-card-title">Feedback from Students</h2>
                <div id="feedback-list" class="mentor-dashboard-list">
                    <p class="mentor-dashboard-loading">Loading feedback...</p>
                </div>
            </div>

            <!-- Student Engagement -->
            <div class="mentor-dashboard-card">
                <h2 class="mentor-dashboard-card-title">Student Engagement</h2>
                <div id="student-engagement-list" class="mentor-dashboard-list">
                    <p class="mentor-dashboard-loading">Loading engagement data...</p>
                </div>
            </div>

            <!-- Success Stories -->
            <div class="mentor-dashboard-card">
                <h2 class="mentor-dashboard-card-title">Success Stories</h2>
                <div id="success-stories-list" class="mentor-dashboard-list">
                    <p class="mentor-dashboard-loading">Loading success stories...</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mentor-dashboard-container {
            padding: 2rem 1rem;
            max-width: 1500px;
            margin: 0 auto;
        }

        .mentor-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .mentor-dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mentor-dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px var(--glow);
        }

        .mentor-dashboard-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            background: var(--primary);
            -webkit-background-clip: text;
            color: transparent;
            background-clip: text;
        }

        .mentor-dashboard-list {
            min-height: 150px;
        }

        .mentor-dashboard-item {
            padding: 1rem;
            border-bottom: 1px solid var(--light);
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .mentor-dashboard-item:last-child {
            border-bottom: none;
        }

        .mentor-dashboard-item strong {
            color: #2c2c54;
            font-weight: 600;
        }

        .mentor-dashboard-item a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .mentor-dashboard-item a:hover {
            color: #a454ff;
            font-weight: 600;
        }

        .mentor-dashboard-loading {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
        }

        .mentor-dashboard-error {
            text-align: center;
            color: #ef4444;
            font-size: 1rem;
        }

        .mentor-dashboard-contribution {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .mentor-dashboard-contribution span {
            font-size: 0.9rem;
            color: #2c2c54;
        }

        .mentor-dashboard-contribution .count {
            color: var(--success);
            font-weight: 500;
        }

        .mentor-dashboard-engagement {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .mentor-dashboard-engagement span {
            font-size: 0.9rem;
            color: #2c2c54;
        }

        .mentor-dashboard-engagement .count {
            color: var(--success);
            font-weight: 500;
        }

        .mentor-dashboard-success-story-card {
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .mentor-dashboard-success-story-image {
            width: 100%;
            max-height: 150px;
            object-fit: contain;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            background: #f1f1f1;
        }

        .mentor-dashboard-success-story-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c2c54;
        }

        .mentor-dashboard-success-story-career {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .mentor-dashboard-success-story-text {
            font-size: 0.85rem;
            color: #4b5563;
            max-height: 80px;
            overflow-y: auto;
        }

        .mentor-dashboard-feedback {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .mentor-dashboard-feedback span {
            font-size: 0.9rem;
            color: #2c2c54;
        }

        .mentor-dashboard-feedback .rating {
            color: var(--success);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .mentor-dashboard-container {
                padding: 1rem;
            }

            .mentor-dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .mentor-dashboard-card {
                padding: 1rem;
            }

            .mentor-dashboard-card-title {
                font-size: 1.3rem;
            }

            .mentor-dashboard-contribution,
            .mentor-dashboard-engagement,
            .mentor-dashboard-feedback {
                flex-direction: column;
                align-items: flex-start;
            }

            .mentor-dashboard-success-story-image {
                max-height: 120px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mentorId = '{{ $mentorId }}';
            const token = '{{ csrf_token() }}';

            // Helper function to handle fetch requests
            async function fetchData(url, containerId, renderFunction) {
                const container = document.getElementById(containerId);
                container.innerHTML = '<p class="mentor-dashboard-loading">Loading...</p>';
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        container.innerHTML = renderFunction(result.data);
                    } else {
                        container.innerHTML = `<p class="mentor-dashboard-error">${result.message || 'Failed to load data. Please try again.'}</p>`;
                    }
                } catch (error) {
                    console.error(`Fetch error for ${containerId}:`, error);
                    container.innerHTML = '<p class="mentor-dashboard-error">Error: Unable to fetch data. Please check your connection.</p>';
                }
            }

            // Render Forum Contributions
            function renderForumContributions(data) {
                let html = '<h3 class="mentor-dashboard-sub-title">Your Posts</h3>';
                if (!data.posts || data.posts.length === 0) {
                    html += '<p class="mentor-dashboard-error">No posts found.</p>';
                } else {
                    html += data.posts.map(post => `
                        <div class="mentor-dashboard-item">
                            <div class="mentor-dashboard-contribution">
                                <span><strong>Post:</strong> <a href="/forum/posts/${post.uuid}">${post.title}</a></span>
                                <span><strong>Comments:</strong> <span class="count">${post.comments_count}</span></span>
                                <span><strong>Posted on:</strong> ${new Date(post.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    `).join('');
                }

                html += '<h3 class="mentor-dashboard-sub-title">Your Comments</h3>';
                if (!data.comments || data.comments.length === 0) {
                    html += '<p class="mentor-dashboard-error">No comments found.</p>';
                } else {
                    html += data.comments.map(comment => `
                        <div class="mentor-dashboard-item">
                            <strong>Comment on:</strong> <a href="/forum/posts/${comment.forum_post_id}">${comment.post?.title || 'Unknown Post'}</a><br>
                            <p>${comment.comment.substring(0, 100)}${comment.comment.length > 100 ? '...' : ''}</p>
                            <small>Commented on: ${new Date(comment.created_at).toLocaleDateString()}</small>
                        </div>
                    `).join('');
                }

                return html;
            }

            // Render Student Engagement
            function renderStudentEngagement(data) {
                if (!data || data.length === 0) {
                    return '<p class="mentor-dashboard-error">No posts found.</p>';
                }
                return data.map(post => `
                    <div class="mentor-dashboard-item">
                        <div class="mentor-dashboard-engagement">
                            <span><strong>Post:</strong> <a href="/forum/posts/${post.uuid}">${post.title}</a></span>
                            <span><strong>Comments:</strong> <span class="count">${post.comments_count}</span></span>
                            <span><strong>Votes:</strong> <span class="count">${post.votes_count}</span></span>
                            <span><strong>Posted on:</strong> ${new Date(post.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                `).join('');
            }

            // Render Success Stories
            function renderSuccessStories(data) {
                if (!data || data.length === 0) {
                    return '<p class="mentor-dashboard-error">No success stories found.</p>';
                }
                return data.map(story => `
                    <div class="mentor-dashboard-success-story-card">
                        ${story.image_url ? `<img src="${story.image_url}" alt="${story.name || 'Story Image'}" class="mentor-dashboard-success-story-image" onerror="this.style.display='none'">` : ''}
                        <div class="mentor-dashboard-success-story-name">${story.name || 'N/A'}</div>
                        <div class="mentor-dashboard-success-story-career">${story.career_path || 'N/A'}</div>
                        <div class="mentor-dashboard-success-story-text">${story.story || 'N/A'}</div>
                    </div>
                `).join('');
            }

            // Render Feedback
            function renderFeedback(data) {
                if (!data || data.length === 0) {
                    return '<p class="mentor-dashboard-error">No feedback received yet.</p>';
                }
                return data.map(feedback => `
                    <div class="mentor-dashboard-item">
                        <div class="mentor-dashboard-feedback">
                            <span><strong>Student:</strong> ${feedback.student_name}</span>
                            ${feedback.rating ? `<span><strong>Rating:</strong> <span class="rating">${feedback.rating}/5</span></span>` : ''}
                            <span><strong>Submitted on:</strong> ${new Date(feedback.created_at).toLocaleDateString()}</span>
                        </div>
                        <p>${feedback.feedback.substring(0, 100)}${feedback.feedback.length > 100 ? '...' : ''}</p>
                    </div>
                `).join('');
            }

            // Fetch data for each section
            fetchData('/api/mentor/forum-contributions', 'forum-contributions-list', renderForumContributions);
            fetchData('/api/mentor/student-engagement', 'student-engagement-list', renderStudentEngagement);
            fetchData('/api/mentor/success-stories', 'success-stories-list', renderSuccessStories);
            fetchData('/api/mentor/feedback', 'feedback-list', renderFeedback);
        });
    </script>
@endsection