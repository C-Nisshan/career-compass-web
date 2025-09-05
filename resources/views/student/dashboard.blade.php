@extends('layouts.app')

@section('content')
    <div class="container student-dashboard-container">
        <h1 class="section-title">Student Dashboard</h1>

        <div class="student-dashboard-grid">
            <!-- Recent Recommendations -->
            <div class="student-dashboard-card">
                <h2 class="student-dashboard-card-title">Recent Recommendations</h2>
                <div id="recommendations-list" class="student-dashboard-list">
                    <p class="student-dashboard-loading">Loading recommendations...</p>
                </div>
            </div>

            <!-- Quiz Scores -->
            <div class="student-dashboard-card">
                <h2 class="student-dashboard-card-title">Recent Quiz Scores</h2>
                <div id="quiz-scores-list" class="student-dashboard-list">
                    <p class="student-dashboard-loading">Loading quiz scores...</p>
                </div>
            </div>

            <!-- Forum Activity -->
            <div class="student-dashboard-card">
                <h2 class="student-dashboard-card-title">Forum Activity</h2>
                <div id="forum-activity-list" class="student-dashboard-list">
                    <p class="student-dashboard-loading">Loading forum activity...</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Student Dashboard Specific Styles */
        .student-dashboard-container {
            padding: 2rem 1rem;
            max-width: 1500px;
            margin: 0 auto;
        }

        .student-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .student-dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .student-dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px var(--glow);
        }

        .student-dashboard-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            background: var(--primary);
            -webkit-background-clip: text;
            color: transparent;
            background-clip: text;
        }

        .student-dashboard-list {
            min-height: 150px;
        }

        .student-dashboard-item {
            padding: 1rem;
            border-bottom: 1px solid var(--light);
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .student-dashboard-item:last-child {
            border-bottom: none;
        }

        .student-dashboard-item strong {
            color: #2c2c54;
            font-weight: 600;
        }

        .student-dashboard-item a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .student-dashboard-item a:hover {
            color: #a454ff;
            font-weight: 600;
        }

        .student-dashboard-loading {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
        }

        .student-dashboard-error {
            text-align: center;
            color: #ef4444;
            font-size: 1rem;
        }

        .student-dashboard-recommendation {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .student-dashboard-recommendation span {
            font-size: 0.9rem;
            color: #2c2c54;
        }

        .student-dashboard-recommendation .confidence {
            color: var(--success);
            font-weight: 500;
        }

        .student-dashboard-recommendation-explanation {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            background: var(--light);
            padding: 0.5rem;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .student-dashboard-container {
                padding: 1rem;
            }

            .student-dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .student-dashboard-card {
                padding: 1rem;
            }

            .student-dashboard-card-title {
                font-size: 1.3rem;
            }

            .student-dashboard-recommendation {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const userId = '{{ $userId }}';
            const token = '{{ csrf_token() }}';

            // Helper function to handle fetch requests
            async function fetchData(url, containerId, renderFunction) {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        document.getElementById(containerId).innerHTML = renderFunction(result.data);
                    } else {
                        document.getElementById(containerId).innerHTML = '<p class="student-dashboard-error">Failed to load data.</p>';
                    }
                } catch (error) {
                    document.getElementById(containerId).innerHTML = '<p class="student-dashboard-error">Error: ' + error.message + '</p>';
                }
            }

            // Render Recommendations
            function renderRecommendations(data) {
                if (!data.length) return '<p class="student-dashboard-error">No recommendations found.</p>';
                return data.map(item => {
                    // Parse recommendations JSON if it's a string
                    const recommendations = typeof item.recommendations === 'string' 
                        ? JSON.parse(item.recommendations) 
                        : item.recommendations;
                    
                    return `
                        <div class="student-dashboard-item">
                            <strong>Predicted on: ${new Date(item.predicted_at).toLocaleDateString()}</strong>
                            ${recommendations.map(rec => `
                                <div class="student-dashboard-recommendation">
                                    <span><strong>Career:</strong> ${rec.career}</span>
                                    <span><strong>Field:</strong> ${rec.field}</span>
                                    <span><strong>Confidence:</strong> <span class="confidence">${(rec.confidence * 100).toFixed(2)}%</span></span>
                                    <div class="student-dashboard-recommendation-explanation">${rec.explanation}</div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }).join('');
            }

            // Render Quiz Scores
            function renderQuizScores(data) {
                if (!data.length) return '<p class="student-dashboard-error">No quiz scores found.</p>';
                return data.map(item => `
                    <div class="student-dashboard-item">
                        <strong>Quiz:</strong> ${item.quiz?.title || 'Unknown Quiz'}<br>
                        <strong>Score:</strong> ${item.score}%<br>
                        <small>Taken on: ${new Date(item.created_at).toLocaleDateString()}</small>
                    </div>
                `).join('');
            }

            // Render Forum Activity
            function renderForumActivity(data) {
                let html = '<h3 class="student-dashboard-sub-title">Your Posts</h3>';
                if (!data.posts.length) {
                    html += '<p class="student-dashboard-error">No posts found.</p>';
                } else {
                    html += data.posts.map(post => `
                        <div class="student-dashboard-item">
                            <strong>Post:</strong> <a href="/forum/posts/${post.uuid}">${post.title}</a><br>
                            <small>Posted on: ${new Date(post.created_at).toLocaleDateString()}</small>
                        </div>
                    `).join('');
                }

                html += '<h3 class="student-dashboard-sub-title">Your Comments</h3>';
                if (!data.comments.length) {
                    html += '<p class="student-dashboard-error">No comments found.</p>';
                } else {
                    html += data.comments.map(comment => `
                        <div class="student-dashboard-item">
                            <strong>Comment on:</strong> <a href="/forum/posts/${comment.forum_post_id}">${comment.post?.title || 'Unknown Post'}</a><br>
                            <p>${comment.comment.substring(0, 100)}${comment.comment.length > 100 ? '...' : ''}</p>
                            <small>Commented on: ${new Date(comment.created_at).toLocaleDateString()}</small>
                        </div>
                    `).join('');
                }

                return html;
            }

            // Fetch data for each section
            fetchData('/api/student/recommendations', 'recommendations-list', renderRecommendations);
            fetchData('/api/student/quiz-scores', 'quiz-scores-list', renderQuizScores);
            fetchData('/api/student/forum-activity', 'forum-activity-list', renderForumActivity);
        });
    </script>
@endsection