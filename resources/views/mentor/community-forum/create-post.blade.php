@extends('layouts.app')

@section('content')
<div class="create-forum-container">
    <div class="create-forum-content">
        <h1 class="create-forum-title">Create Guidance Post</h1>
        
        <div class="create-forum-card">
            <div class="create-forum-header">
                <h2 class="create-forum-subtitle">New Post</h2>
            </div>
            <div id="create-forum-loading" class="hidden">Loading...</div>
            <div id="create-forum-error" class="hidden"></div>
            
            <form id="create-post-form" class="create-post-form">
                @csrf
                <div class="create-post-form-group">
                    <label for="create-post-title">Title</label>
                    <input type="text" id="create-post-title" name="title" placeholder="Enter post title..." required>
                </div>
                <div class="create-post-form-group">
                    <label for="create-post-body">Body</label>
                    <textarea id="create-post-body" name="body" placeholder="Write your post content..." required></textarea>
                </div>
                <div class="create-post-form-group">
                    <label for="create-post-tags">Tags</label>
                    <select id="create-post-tags" name="tags" multiple>
                        <!-- Populated dynamically via JavaScript -->
                    </select>
                </div>
                <div class="create-post-form-group">
                    <label for="create-post-guidance">
                        <input type="checkbox" id="create-post-guidance" name="is_guidance" checked>
                        Mark as Mentor Guidance Post
                    </label>
                </div>
                <div class="create-post-form-actions">
                    <button type="submit" class="create-post-submit-btn">Create Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for mentor forum create post');

    const loading = document.getElementById('create-forum-loading');
    const error = document.getElementById('create-forum-error');
    const form = document.getElementById('create-post-form');
    const tagsSelect = document.getElementById('create-post-tags');

    if (!form || !tagsSelect) {
        console.error('Required elements not found!');
        error.textContent = 'Page elements not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    async function fetchTags() {
        console.log('Fetching tags');
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/mentor/forum-tags', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Tags API Response:', result);

            if (response.ok && result.success) {
                tagsSelect.innerHTML = '';
                result.data.forEach(tag => {
                    const option = document.createElement('option');
                    option.value = tag.uuid;
                    option.textContent = tag.name;
                    tagsSelect.appendChild(option);
                });
            } else {
                error.textContent = result.message || 'Failed to load tags. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch tags error:', err);
            error.textContent = 'Unable to fetch tags. Please check your connection.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('Post form submitted');
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        const formData = new FormData(form);
        const data = {
            title: formData.get('title'),
            body: formData.get('body'),
            tags: Array.from(formData.getAll('tags')),
            is_guidance: formData.get('is_guidance') === 'on'
        };

        try {
            const response = await fetch('/api/mentor/forum-posts', {
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
            console.log('Post API Response:', result);

            if (response.ok && result.success) {
                form.reset();
                error.textContent = 'Post created successfully!';
                error.classList.remove('hidden');
                error.style.background = '#3b82f6';
                error.style.color = '#ffffff';
                setTimeout(() => {
                    window.location.href = '{{ route("mentor.forum.browse-posts") }}';
                }, 2000);
            } else {
                error.textContent = result.message || 'Failed to create post. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Post error:', err);
            error.textContent = 'Unable to create post. Please check your connection.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    });

    fetchTags();
});
</script>
@endsection