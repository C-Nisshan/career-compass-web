@extends('layouts.app')

@section('content')

<div class="student-create-container">
    <div class="student-create-content">
        <h1 class="student-create-title">Create Forum Post</h1>
        
        <div class="student-create-card">
            <div class="student-create-header">
                <h2 class="student-create-subtitle">New Post</h2>
            </div>
            <div id="student-create-loading" class="hidden">Loading...</div>
            <div id="student-create-error" class="hidden"></div>
            <div id="student-create-success" class="hidden"></div>
            
            <form id="student-create-post-form" class="student-create-form">
                @csrf
                <div class="student-create-form-group">
                    <label for="student-create-title">Title</label>
                    <input type="text" id="student-create-title" name="title" placeholder="Enter post title" required>
                </div>
                <div class="student-create-form-group">
                    <label for="student-create-body">Body</label>
                    <textarea id="student-create-body" name="body" placeholder="Write your post content..." required></textarea>
                </div>
                <div class="student-create-form-group">
                    <label for="student-create-tags">Tags</label>
                    <select id="student-create-tags" name="tags" multiple>
                        <!-- Populated dynamically via JavaScript -->
                    </select>
                </div>
                <div class="student-create-form-actions">
                    <button type="submit" class="student-create-submit-btn">Create Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for student forum create post');

    const form = document.getElementById('student-create-post-form');
    const loading = document.getElementById('student-create-loading');
    const error = document.getElementById('student-create-error');
    const success = document.getElementById('student-create-success');
    const tagsSelect = document.getElementById('student-create-tags');

    if (!form || !tagsSelect) {
        console.error('Form or tags select element not found!');
        error.textContent = 'Form or tags select element not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    // Fetch available tags
    async function fetchTags() {
        console.log('Fetching available tags');
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/student/forum-tags', {
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
            error.textContent = 'Unable to fetch tags. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    // Handle form submission
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('Form submitted');

        loading.classList.remove('hidden');
        error.classList.add('hidden');
        success.classList.add('hidden');

        const formData = new FormData(form);
        const data = {
            title: formData.get('title'),
            body: formData.get('body'),
            tags: Array.from(tagsSelect.selectedOptions).map(option => option.value)
        };

        try {
            const response = await fetch('/api/student/forum-posts', {
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
            console.log('Create Post API Response:', result);

            if (response.ok && result.success) {
                success.textContent = 'Post created successfully!';
                success.classList.remove('hidden');
                form.reset();
                tagsSelect.innerHTML = ''; // Reset tags
                await fetchTags(); // Reload tags
            } else {
                error.textContent = result.message || 'Failed to create post. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Create post error:', err);
            error.textContent = 'Unable to create post. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    });

    fetchTags(); // Initial load of tags
});
</script>
@endsection