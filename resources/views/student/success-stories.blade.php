@extends('layouts.app')

@section('content')
<div class="student-success-story-container">
    <div class="student-success-story-content">
        <h1 class="student-success-story-title">Success Stories</h1>
        
        <div id="student-success-story-loading" class="student-success-story-loading hidden">Loading...</div>
        <div id="student-success-story-error" class="student-success-story-error hidden"></div>
        
        <div class="student-success-story-grid" id="student-success-story-grid">
            <!-- Populated dynamically via JavaScript -->
        </div>
    </div>
</div>

<style>
.student-success-story-container {
    min-height: 100vh;
    background: #f9fafb;
    padding: 3rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.student-success-story-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.student-success-story-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #ffffff 0%, #a855f7 100%);
    -webkit-background-clip: text;
    background-clip: text;
    letter-spacing: -0.02em;
    text-align: center;
}

.student-success-story-loading {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #a855f7;
    border-radius: 10px;
    margin: 1rem 0;
    animation: student-success-story-pulse 1.5s infinite ease-in-out;
}

.student-success-story-error {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #dc2626;
    border-radius: 10px;
    margin: 1rem 0;
    border: 1px solid #dc2626;
}

.student-success-story-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem;
}

.student-success-story-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.student-success-story-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(168, 85, 247, 0.3);
}

.student-success-story-image {
    width: 100%;
    max-height: 250px; /* Prevent overly large images */
    object-fit: contain; /* Show full image without cropping */
    display: block;
    background: #f1f1f1; /* Background for images with transparent areas */
    padding: 1rem; /* Add padding to prevent image from touching edges */
}

.student-success-story-card-content {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem; /* Consistent spacing between elements */
}

.student-success-story-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.student-success-story-career {
    font-size: 1rem;
    font-weight: 500;
    color: #6b7280;
}

.student-success-story-text {
    font-size: 0.9rem;
    color: #4b5563;
    line-height: 1.6;
    max-height: 100px; /* Limit text height to prevent overly long cards */
    overflow-y: auto; /* Allow scrolling for long stories */
}

.student-success-story-no-data {
    grid-column: 1 / -1;
    text-align: center;
    color: #6b7280;
    font-size: 1.2rem;
    font-weight: 500;
    padding: 2rem;
}

@keyframes student-success-story-pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .student-success-story-title {
        font-size: 2rem;
    }

    .student-success-story-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .student-success-story-image {
        max-height: 200px; /* Slightly smaller max height for mobile */
        padding: 0.75rem;
    }

    .student-success-story-name {
        font-size: 1.3rem;
    }

    .student-success-story-career {
        font-size: 0.9rem;
    }

    .student-success-story-text {
        font-size: 0.85rem;
        max-height: 80px;
    }
}

.hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('student-success-story-loading');
    const error = document.getElementById('student-success-story-error');
    const grid = document.getElementById('student-success-story-grid');

    async function fetchStories() {
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        grid.innerHTML = '';

        try {
            const response = await fetch('/api/student/success-stories', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            if (response.ok && result.success) {
                const stories = result.data;
                if (stories.length === 0) {
                    grid.innerHTML = '<p class="student-success-story-no-data">No success stories found.</p>';
                } else {
                    stories.forEach(story => {
                        const card = document.createElement('div');
                        card.className = 'student-success-story-card';
                        card.innerHTML = `
                            ${story.image_url ? `<img src="${story.image_url}" alt="${story.name || 'Story Image'}" class="student-success-story-image" onerror="this.style.display='none'">` : ''}
                            <div class="student-success-story-card-content">
                                <h3 class="student-success-story-name">${story.name || 'N/A'}</h3>
                                <p class="student-success-story-career">${story.career_path || 'N/A'}</p>
                                <p class="student-success-story-text">${story.story || 'N/A'}</p>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                }
            } else {
                error.textContent = result.message || 'Failed to load success stories. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch success stories. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    fetchStories();
});
</script>
@endsection
