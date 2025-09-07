@extends('layouts.app')

@section('content')
<div class="admin-success-stories-container">
    <div class="admin-success-stories-content">
        <h1 class="admin-success-stories-title">Success Stories Dashboard</h1>
        
        <div class="admin-success-stories-card">
            <div class="admin-success-stories-header">
                <h2 class="admin-success-stories-subtitle">All Success Stories</h2>
                <button id="admin-success-stories-create-btn"
                        class="admin-success-stories-create-btn">
                    Create New Story
                </button>
            </div>
            <div id="admin-success-stories-loading" class="hidden">Loading...</div>
            <div id="admin-success-stories-error" class="hidden"></div>
            
            <div class="admin-success-stories-table-container">
                <table class="admin-success-stories-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Career Path</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-success-stories-table-body">
                        <!-- Populated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div id="admin-success-stories-create-modal" class="admin-success-stories-modal hidden">
            <div class="admin-success-stories-modal-content">
                <span class="admin-success-stories-modal-close" data-modal="create">&times;</span>
                <h2>Create Success Story</h2>
                <form id="admin-success-stories-create-form" enctype="multipart/form-data">
                    <div class="admin-success-stories-form-group">
                        <label for="create-name">Name</label>
                        <input type="text" id="create-name" name="name" required>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="create-career_path">Career Path</label>
                        <input type="text" id="create-career_path" name="career_path" required>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="create-story">Story</label>
                        <textarea id="create-story" name="story" required></textarea>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="create-image">Image</label>
                        <input type="file" id="create-image" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="admin-success-stories-submit-btn">Create Story</button>
                    <button type="button" class="admin-success-stories-cancel-btn" data-modal="create">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="admin-success-stories-edit-modal" class="admin-success-stories-modal hidden">
            <div class="admin-success-stories-modal-content">
                <span class="admin-success-stories-modal-close" data-modal="edit">&times;</span>
                <h2>Edit Success Story</h2>
                <form id="admin-success-stories-edit-form" enctype="multipart/form-data">
                    <div class="admin-success-stories-form-group">
                        <label for="edit-name">Name</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="edit-career_path">Career Path</label>
                        <input type="text" id="edit-career_path" name="career_path" required>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="edit-story">Story</label>
                        <textarea id="edit-story" name="story" required></textarea>
                    </div>
                    <div class="admin-success-stories-form-group">
                        <label for="edit-image">Image</label>
                        <img id="edit-image-preview" src="" alt="Story Image" style="max-width: 100%; height: auto; margin-bottom: 1rem; display: none;" />
                        <input type="file" id="edit-image" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="admin-success-stories-submit-btn">Update Story</button>
                    <button type="button" class="admin-success-stories-cancel-btn" data-modal="edit">Cancel</button>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div id="admin-success-stories-view-modal" class="admin-success-stories-modal hidden">
            <div class="admin-success-stories-modal-content">
                <span class="admin-success-stories-modal-close" data-modal="view">&times;</span>
                <h2>Story Details</h2>
                <div id="admin-success-stories-view-content">
                    <p><strong>Name:</strong> <span id="view-name"></span></p>
                    <p><strong>Career Path:</strong> <span id="view-career_path"></span></p>
                    <p><strong>Story:</strong> <span id="view-story"></span></p>
                    <p><strong>Image:</strong></p>
                    <img id="view-image" src="" alt="Story Image" style="max-width: 100%; height: auto; margin-bottom: 1rem; display: none;" />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for success stories');

    const loading = document.getElementById('admin-success-stories-loading');
    const error = document.getElementById('admin-success-stories-error');
    const tableBody = document.getElementById('admin-success-stories-table-body');
    const createBtn = document.getElementById('admin-success-stories-create-btn');
    console.log('Create button:', createBtn);
    console.log('Create modal:', document.getElementById('admin-success-stories-create-modal'));

    const modals = {
        create: document.getElementById('admin-success-stories-create-modal'),
        edit: document.getElementById('admin-success-stories-edit-modal'),
        view: document.getElementById('admin-success-stories-view-modal')
    };
    console.log('Modals:', modals);

    if (!createBtn) {
        console.error('Create button not found!');
        error.textContent = 'Create button not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    if (!modals.create || !modals.edit || !modals.view) {
        console.error('One or more modals not found!', modals);
        error.textContent = 'Modal elements not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    let storyData = [];

    async function fetchStories() {
        console.log('Fetching success stories');
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        tableBody.innerHTML = '';

        try {
            const response = await fetch('/api/admin/success-stories', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('API Response:', JSON.stringify(result, null, 2));
            if (response.ok && result.success) {
                storyData = result.data;
                console.log('Story Data:', storyData);
                storyData.forEach(story => {
                    console.log('Story Image URL:', story.image_url);
                });
                if (storyData.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="admin-success-stories-no-data">No success stories found.</td></tr>';
                } else {
                    storyData.forEach(story => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${story.name || 'N/A'}</td>
                            <td>${story.career_path || 'N/A'}</td>
                            <td>${new Date(story.created_at).toLocaleDateString()}</td>
                            <td>
                                <div class="admin-success-stories-actions-wrapper">
                                    <button class="admin-success-stories-action-btn admin-success-stories-view-btn" data-uuid="${story.uuid}" title="View Details"></button>
                                    <button class="admin-success-stories-action-btn admin-success-stories-edit-btn" data-uuid="${story.uuid}" title="Edit"></button>
                                    <button class="admin-success-stories-action-btn admin-success-stories-delete-btn" data-uuid="${story.uuid}" title="Delete"></button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
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

    function openStoryModal(story = null, mode = 'view') {
        console.log('Opening modal in mode:', mode, 'with story:', story);
        // Hide all modals
        Object.values(modals).forEach(modal => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        });

        const modal = modals[mode];
        if (!modal) {
            console.error(`Modal for mode ${mode} not found!`);
            error.textContent = `Modal for ${mode} not found.`;
            error.classList.remove('hidden');
            return;
        }

        // Populate fields for edit and view modes
        if (mode === 'edit' && story) {
            let hiddenUuidInput = document.getElementById('edit-uuid');
            if (!hiddenUuidInput) {
                hiddenUuidInput = document.createElement('input');
                hiddenUuidInput.type = 'hidden';
                hiddenUuidInput.id = 'edit-uuid';
                hiddenUuidInput.name = 'uuid';
                document.getElementById('admin-success-stories-edit-form').appendChild(hiddenUuidInput);
            }
            hiddenUuidInput.value = story.uuid || '';
            console.log('Populating edit form with:', {
                uuid: story.uuid,
                name: story.name,
                career_path: story.career_path,
                story: story.story
            });
            document.getElementById('edit-name').value = story.name || '';
            document.getElementById('edit-career_path').value = story.career_path || '';
            document.getElementById('edit-story').value = story.story || '';
            const imagePreview = document.getElementById('edit-image-preview');
            if (story.image_url) {
                imagePreview.src = story.image_url;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
            }
        } else if (mode === 'view' && story) {
            console.log('View modal image URL:', story.image_url);
            document.getElementById('view-name').textContent = story.name || 'N/A';
            document.getElementById('view-career_path').textContent = story.career_path || 'N/A';
            document.getElementById('view-story').textContent = story.story || 'N/A';
            const image = document.getElementById('view-image');
            if (story.image_url) {
                const baseUrl = window.location.origin;
                image.src = baseUrl + story.image_url;
                image.style.display = 'block';
                image.onerror = () => {
                    console.error('Image failed to load:', image.src);
                };
                image.onload = () => {
                    console.log('Image loaded successfully:', image.src);
                };
            } else {
                image.style.display = 'none';
            }
        } else if (mode === 'create') {
            const createForm = document.getElementById('admin-success-stories-create-form');
            if (createForm) {
                createForm.reset();
            } else {
                console.error('Create form not found!');
                error.textContent = 'Create form not found.';
                error.classList.remove('hidden');
                return;
            }
        }

        // Show the selected modal
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        console.log('Modal opened:', mode, 'Display:', modal.style.display, 'Classes:', modal.className);
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async function handleFormSubmit(form, url, method) {
        const formData = new FormData(form);
        if (method === 'PUT') {
            formData.append('_method', 'PUT'); // Ensure Laravel recognizes the PUT request
        }
        try {
            const response = await fetch(url, {
                method: method === 'PUT' ? 'POST' : method, // Use POST for PUT requests
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData,
                credentials: 'include'
            });
            const result = await response.json();
            console.log('Form Response:', JSON.stringify(result, null, 2));
            if (response.ok && result.success) {
                await fetchStories();
                Object.values(modals).forEach(modal => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                });
                alert(result.message);
                if (method === 'PUT' && result.data) {
                    openStoryModal(result.data, 'edit');
                }
            } else {
                error.textContent = result.message || `Failed to ${method === 'POST' ? 'create' : 'update'} success story.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Form error:', err);
            error.textContent = 'Unable to save success story. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    }

    window.handleDelete = async function(uuid) {
        if (!confirm('Are you sure you want to delete this success story? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/api/admin/success-stories/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Delete Response:', JSON.stringify(result, null, 2));

            if (response.ok && result.success) {
                fetchStories();
                alert(result.message);
            } else {
                error.textContent = result.message || 'Failed to delete success story. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Delete error:', err);
            error.textContent = 'Unable to delete success story. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // Close modals
    document.querySelectorAll('.admin-success-stories-modal-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            const modalId = closeBtn.getAttribute('data-modal');
            const modal = modals[modalId];
            modal.classList.add('hidden');
            modal.style.display = 'none';
        });
    });

    // Create form submission
    document.getElementById('admin-success-stories-create-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleFormSubmit(e.target, '/api/admin/success-stories', 'POST');
    });

    // Edit form submission
    document.getElementById('admin-success-stories-edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const uuid = document.getElementById('edit-uuid')?.value;
        console.log('Submitting edit form with UUID:', uuid);
        if (uuid) {
            await handleFormSubmit(e.target, `/api/admin/success-stories/${uuid}`, 'PUT');
        } else {
            error.textContent = 'Story UUID not found.';
            error.classList.remove('hidden');
        }
    });

    // Create new story
    if (createBtn) {
        console.log('Attaching click event listener to create button');
        createBtn.addEventListener('click', (e) => {
            console.log('Create button clicked', e);
            openStoryModal(null, 'create');
        });
        createBtn.addEventListener('mouseover', () => {
            console.log('Create button mouseover');
        });
        createBtn.addEventListener('mouseout', () => {
            console.log('Create button mouseout');
        });
    } else {
        console.error('Create button not found for event listener!');
    }

    // Delegate click event for action buttons
    tableBody.addEventListener('click', function (e) {
        const target = e.target.closest('.admin-success-stories-action-btn');
        if (!target) return;

        const uuid = target.getAttribute('data-uuid');
        const story = storyData.find(s => s.uuid === uuid);

        if (target.classList.contains('admin-success-stories-view-btn')) {
            if (story) openStoryModal(story, 'view');
        } else if (target.classList.contains('admin-success-stories-edit-btn')) {
            if (story) openStoryModal(story, 'edit');
        } else if (target.classList.contains('admin-success-stories-delete-btn')) {
            handleDelete(uuid);
        }
    });

    fetchStories();
});
</script>
@endsection