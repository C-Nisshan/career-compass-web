@extends('layouts.app')

@section('content')
<link href="{{ asset('css/forum-create.css') }}" rel="stylesheet">

<div class="forum-create-container">
    <div class="forum-create-content">
        <h1 class="forum-create-title">Create a New Forum Post</h1>
        
        <div class="forum-create-card">
            <form id="forum-create-form" class="forum-create-form">
                @csrf
                <div class="forum-create-form-group">
                    <label for="forum-create-title">Title</label>
                    <input type="text" id="forum-create-title" name="title" placeholder="Post Title" required>
                    <span class="forum-create-error" id="forum-create-title-error"></span>
                </div>
                <div class="forum-create-form-group">
                    <label for="forum-create-body">Content</label>
                    <textarea id="forum-create-body" name="body" placeholder="Your Post Content" required></textarea>
                    <span class="forum-create-error" id="forum-create-body-error"></span>
                </div>
                <div class="forum-create-form-group">
                    <label for="forum-create-tags">Tags (comma-separated)</label>
                    <input type="text" id="forum-create-tags" name="tags" placeholder="e.g., tech, career">
                    <span class="forum-create-error" id="forum-create-tags-error"></span>
                </div>
                <button type="submit" class="forum-create-submit-btn">Submit Post</button>
            </form>
            <div id="forum-create-loading" class="hidden">Creating Post...</div>
            <div id="forum-create-error" class="hidden"></div>
            <div id="forum-create-success" class="hidden"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded fired for Forum Create'); // Debug: Confirm event

    const form = document.getElementById('forum-create-form');
    const titleError = document.getElementById('forum-create-title-error');
    const bodyError = document.getElementById('forum-create-body-error');
    const tagsError = document.getElementById('forum-create-tags-error');
    const loading = document.getElementById('forum-create-loading');
    const errorMessage = document.getElementById('forum-create-error');
    const successMessage = document.getElementById('forum-create-success');
    const submitBtn = form.querySelector('.forum-create-submit-btn');

    // Debug: Check if elements exist
    if (!form || !titleError || !bodyError || !tagsError || !loading || !errorMessage || !successMessage || !submitBtn) {
        console.error('One or more DOM elements not found!');
        errorMessage.textContent = 'Required elements not found in the DOM.';
        errorMessage.classList.remove('hidden');
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log('Form submitted'); // Debug: Confirm submission

        // Clear previous errors
        titleError.textContent = '';
        bodyError.textContent = '';
        tagsError.textContent = '';
        errorMessage.textContent = '';
        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');

        // Disable submit button and show loading
        submitBtn.disabled = true;
        loading.classList.remove('hidden');

        const title = form.title.value;
        const body = form.body.value;
        const tags = form.tags.value.split(',').map(tag => tag.trim()).filter(tag => tag);

        try {
            const response = await fetch('/api/forum', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer {{ auth()->user() ? auth()->user()->token : '' }}',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title, body, tags }),
                credentials: 'include'
            });

            const result = await response.json();
            console.log('API Response:', result); // Debug: Log response

            if (!response.ok) {
                if (result.errors) {
                    titleError.textContent = result.errors.title?.[0] || '';
                    bodyError.textContent = result.errors.body?.[0] || '';
                    tagsError.textContent = result.errors.tags?.[0] || '';
                    titleError.classList.toggle('active', !!result.errors.title);
                    bodyError.classList.toggle('active', !!result.errors.body);
                    tagsError.classList.toggle('active', !!result.errors.tags);
                } else {
                    errorMessage.textContent = result.error || 'Failed to create post. Please try again.';
                    errorMessage.classList.remove('hidden');
                }
                return;
            }

            successMessage.textContent = result.message || 'Post created successfully!';
            successMessage.classList.remove('hidden');
            setTimeout(() => {
                successMessage.classList.add('hidden');
                window.location.href = '/community/forum';
            }, 2000);

        } catch (error) {
            console.error('Error creating post:', error);
            errorMessage.textContent = 'Unable to create post. Please check your connection or contact support.';
            errorMessage.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            loading.classList.add('hidden');
        }
    });
});
</script>
@endsection