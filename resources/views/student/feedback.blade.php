@extends('layouts.app')

@section('content')
    <div class="container student-feedback-container">
        <h1 class="section-title">Give Feedback to Mentors</h1>

        <!-- Mentors Grid -->
        <div class="student-feedback-mentors-grid">
            <h2 class="student-feedback-title">Mentors</h2>
            <div id="mentors-list" class="mentors-grid">
                <p class="student-feedback-loading">Loading mentors...</p>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div id="feedback-modal" class="student-feedback-modal hidden">
            <div class="student-feedback-modal-content">
                <span id="modal-close" class="student-feedback-modal-close">&times;</span>
                <h2 class="student-feedback-title">Submit Feedback for <span id="modal-mentor-name"></span></h2>
                <form id="feedback-form" class="student-feedback-form-content">
                    @csrf
                    <input type="hidden" id="mentor_id" name="mentor_id">
                    <div class="form-group">
                        <label for="feedback">Feedback</label>
                        <textarea id="feedback" name="feedback" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating (1-5)</label>
                        <select id="rating" name="rating" class="form-control">
                            <option value="">No rating</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </form>
                <div id="feedback-message" class="student-feedback-message hidden"></div>
            </div>
        </div>

        <!-- Your Feedback Section -->
        <div class="student-feedback-list">
            <h2 class="student-feedback-title">Your Feedback</h2>
            <div id="feedback-list" class="student-feedback-list-content">
                <p class="student-feedback-loading">Loading feedback...</p>
            </div>
        </div>
    </div>

    <style>
        .student-feedback-container {
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .student-feedback-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            background: var(--primary);
            -webkit-background-clip: text;
            color: transparent;
            background-clip: text;
        }

        /* Mentors Grid */
        .student-feedback-mentors-grid {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .mentors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .mentor-card {
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mentor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px var(--glow);
        }

        .mentor-card-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c2c54;
            margin-bottom: 0.5rem;
        }

        .mentor-card-detail {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .mentor-card-contact {
            font-size: 0.85rem;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .mentor-card-contact:hover {
            color: #a454ff;
        }

        .mentor-card-button {
            background: var(--primary);
            color: #ffffff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 1rem;
            width: 100%;
            transition: background 0.3s ease;
        }

        .mentor-card-button:hover {
            background: #a454ff;
        }

        /* Modal */
        .student-feedback-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .student-feedback-modal-content {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .student-feedback-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #2c2c54;
        }

        .student-feedback-modal-close:hover {
            color: #ef4444;
        }

        /* Form */
        .student-feedback-form-content {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #2c2c54;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid var(--light);
            border-radius: 6px;
            font-size: 0.95rem;
            color: #2c2c54;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 5px var(--glow);
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #a454ff;
        }

        .student-feedback-message {
            text-align: center;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 6px;
        }

        .student-feedback-message.success {
            background: var(--success);
            color: #ffffff;
        }

        .student-feedback-message.error {
            background: #ef4444;
            color: #ffffff;
        }

        /* Feedback List */
        .student-feedback-list {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .student-feedback-list-content {
            min-height: 150px;
        }

        .student-feedback-item {
            padding: 1rem;
            border-bottom: 1px solid var(--light);
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .student-feedback-item:last-child {
            border-bottom: none;
        }

        .student-feedback-item strong {
            color: #2c2c54;
            font-weight: 600;
        }

        .student-feedback-item .rating {
            color: var(--success);
            font-weight: 500;
        }

        .student-feedback-loading {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
        }

        .student-feedback-error {
            text-align: center;
            color: #ef4444;
            font-size: 1rem;
        }

        .student-feedback-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-edit {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-edit:hover {
            background: #a454ff;
        }

        .btn-delete {
            background: #ef4444;
            color: #ffffff;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            .student-feedback-container {
                padding: 1rem;
            }

            .student-feedback-title {
                font-size: 1.3rem;
            }

            .mentors-grid {
                grid-template-columns: 1fr;
            }

            .mentor-card {
                padding: 0.75rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-primary {
                padding: 0.5rem 1rem;
            }

            .student-feedback-modal-content {
                width: 95%;
                padding: 1rem;
            }
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const studentId = '{{ $studentId }}';
            const token = '{{ csrf_token() }}';
            const feedbackForm = document.getElementById('feedback-form');
            const feedbackMessage = document.getElementById('feedback-message');
            const feedbackList = document.getElementById('feedback-list');
            const mentorsList = document.getElementById('mentors-list');
            const modal = document.getElementById('feedback-modal');
            const modalClose = document.getElementById('modal-close');
            const modalMentorName = document.getElementById('modal-mentor-name');
            const mentorIdInput = document.getElementById('mentor_id');

            // Helper function to show messages
            function showMessage(message, type) {
                feedbackMessage.textContent = message;
                feedbackMessage.className = `student-feedback-message ${type}`;
                feedbackMessage.classList.remove('hidden');
                setTimeout(() => feedbackMessage.classList.add('hidden'), 3000);
            }

            // Fetch mentors for grid
            async function fetchMentors() {
                mentorsList.innerHTML = '<p class="student-feedback-loading">Loading mentors...</p>';
                try {
                    const response = await fetch('/api/student/mentors', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        mentorsList.innerHTML = renderMentors(result.data);
                    } else {
                        mentorsList.innerHTML = `<p class="student-feedback-error">${result.message || 'Failed to load mentors.'}</p>`;
                    }
                } catch (error) {
                    console.error('Fetch mentors error:', error);
                    mentorsList.innerHTML = '<p class="student-feedback-error">Error loading mentors.</p>';
                }
            }

            // Render mentors grid
            function renderMentors(data) {
                if (!data || data.length === 0) {
                    return '<p class="student-feedback-error">No mentors available.</p>';
                }
                return data.map(mentor => `
                    <div class="mentor-card">
                        <div class="mentor-card-name">${mentor.name}</div>
                        <div class="mentor-card-detail"><strong>Profession:</strong> ${mentor.profession_title || 'N/A'}</div>
                        <div class="mentor-card-detail"><strong>Industry:</strong> ${mentor.industry || 'N/A'}</div>
                        <div class="mentor-card-detail"><strong>Expertise:</strong> ${mentor.areas_of_expertise?.join(', ') || 'N/A'}</div>
                        <div class="mentor-card-detail"><strong>Contact:</strong> 
                            <a href="mailto:${mentor.email}" class="mentor-card-contact">${mentor.email}</a>
                            ${mentor.linkedin_url ? `<br><a href="${mentor.linkedin_url}" class="mentor-card-contact" target="_blank">LinkedIn</a>` : ''}
                        </div>
                        <button class="mentor-card-button" onclick="openFeedbackModal('${mentor.uuid}', '${mentor.name}')">Give Feedback</button>
                    </div>
                `).join('');
            }

            // Open feedback modal
            window.openFeedbackModal = function(mentorId, mentorName) {
                mentorIdInput.value = mentorId;
                modalMentorName.textContent = mentorName;
                modal.classList.remove('hidden');
                document.getElementById('feedback').value = '';
                document.getElementById('rating').value = '';
                feedbackForm.onsubmit = async (e) => {
                    e.preventDefault();
                    const formData = new FormData(feedbackForm);
                    try {
                        const response = await fetch('/api/student/feedback', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });
                        const result = await response.json();
                        if (response.ok && result.success) {
                            showMessage(result.message, 'success');
                            feedbackForm.reset();
                            modal.classList.add('hidden');
                            fetchFeedback();
                        } else {
                            showMessage(result.message || 'Failed to submit feedback.', 'error');
                        }
                    } catch (error) {
                        console.error('Submit feedback error:', error);
                        showMessage('Error submitting feedback.', 'error');
                    }
                };
            };

            // Close modal
            modalClose.addEventListener('click', () => {
                modal.classList.add('hidden');
                feedbackForm.reset();
                feedbackForm.onsubmit = feedbackForm.__proto__.onsubmit;
            });

            // Fetch feedback
            async function fetchFeedback() {
                feedbackList.innerHTML = '<p class="student-feedback-loading">Loading feedback...</p>';
                try {
                    const response = await fetch('/api/student/feedback', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        feedbackList.innerHTML = renderFeedback(result.data);
                    } else {
                        feedbackList.innerHTML = `<p class="student-feedback-error">${result.message || 'Failed to load feedback.'}</p>`;
                    }
                } catch (error) {
                    console.error('Fetch feedback error:', error);
                    feedbackList.innerHTML = '<p class="student-feedback-error">Error loading feedback.</p>';
                }
            }

            // Render feedback
            function renderFeedback(data) {
                if (!data || data.length === 0) {
                    return '<p class="student-feedback-error">No feedback submitted yet.</p>';
                }
                return data.map(feedback => `
                    <div class="student-feedback-item" data-uuid="${feedback.uuid}">
                        <strong>Mentor:</strong> ${feedback.mentor?.name || 'Unknown'}<br>
                        <p>${feedback.feedback}</p>
                        ${feedback.rating ? `<span><strong>Rating:</strong> <span class="rating">${feedback.rating}/5</span></span>` : ''}
                        <small>Submitted on: ${new Date(feedback.created_at).toLocaleDateString()}</small>
                        <div class="student-feedback-actions">
                            <button class="btn-edit" onclick="editFeedback('${feedback.uuid}', '${feedback.feedback.replace(/'/g, "\\'").replace(/"/g, '&quot;')}', '${feedback.rating || ''}', '${feedback.mentor_id}')">Edit</button>
                            <button class="btn-delete" onclick="deleteFeedback('${feedback.uuid}')">Delete</button>
                        </div>
                    </div>
                `).join('');
            }

            // Edit feedback
            window.editFeedback = function(uuid, feedbackText, rating, mentorId) {
                openFeedbackModal(mentorId, document.querySelector(`.mentor-card[data-uuid="${mentorId}"] .mentor-card-name`)?.textContent || 'Mentor');
                const feedbackField = document.getElementById('feedback');
                feedbackField.value = feedbackText; // Set feedback text
                document.getElementById('rating').value = rating || '';
                feedbackForm.onsubmit = async (e) => {
                    e.preventDefault();
                    const formData = new FormData(feedbackForm); // Use form's FormData
                    formData.delete('mentor_id'); // Remove mentor_id since update doesn't need it

                    // Debug FormData contents
                    console.log('FormData Contents:');
                    for (const [key, value] of formData.entries()) {
                        console.log(`${key}: ${value} (length: ${value.length})`);
                    }

                    if (!formData.get('feedback') || formData.get('feedback').trim() === '') {
                        showMessage('Feedback is required.', 'error');
                        return;
                    }

                    try {
                        const response = await fetch(`/api/student/feedback/${uuid}`, {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: formData
                        });
                        console.log('Response Status:', response.status);
                        console.log('Response OK:', response.ok);
                        const result = await response.json();
                        console.log('Response JSON:', result);
                        if (response.status === 422) {
                            showMessage(result.message || 'Validation failed.', 'error');
                            console.log('Validation Errors:', result.errors);
                        } else if (response.status === 500) {
                            showMessage(result.message || 'Server error.', 'error');
                        } else if (response.ok && result.success) {
                            showMessage(result.message, 'success');
                            feedbackForm.reset();
                            modal.classList.add('hidden');
                            feedbackForm.onsubmit = feedbackForm.__proto__.onsubmit;
                            fetchFeedback();
                        } else {
                            showMessage(result.message || 'Failed to update feedback.', 'error');
                        }
                    } catch (error) {
                        console.error('Update feedback error:', error);
                        showMessage('Error updating feedback.', 'error');
                    }
                };
            };

            // Delete feedback
            window.deleteFeedback = async function(uuid) {
                if (!confirm('Are you sure you want to delete this feedback?')) return;
                try {
                    const response = await fetch(`/api/student/feedback/${uuid}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    const result = await response.json();
                    if (response.ok && result.success) {
                        showMessage(result.message, 'success');
                        fetchFeedback();
                    } else {
                        showMessage(result.message || 'Failed to delete feedback.', 'error');
                    }
                } catch (error) {
                    console.error('Delete feedback error:', error);
                    showMessage('Error deleting feedback.', 'error');
                }
            };

            // Initialize
            fetchMentors();
            fetchFeedback();
        });
    </script>
@endsection