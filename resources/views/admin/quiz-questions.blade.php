@extends('layouts.app')

@section('content')
<div class="admin-quiz-management-container">
    <div class="admin-quiz-management-content">
        <h1 class="admin-quiz-management-title">Quiz Management Dashboard</h1>
        
        <div class="admin-quiz-management-card">
            <div class="admin-quiz-management-header">
                <h2 class="admin-quiz-management-subtitle">All Quiz Questions</h2>
                <div class="admin-quiz-management-filter-group">
                    <label for="admin-quiz-management-filter-quiz">Filter by Quiz:</label>
                    <select id="admin-quiz-management-filter-quiz">
                        <option value="">All Quizzes</option>
                        <!-- Populated dynamically -->
                    </select>
                </div>
                <div class="admin-quiz-management-button-group">
                    <button id="admin-quiz-management-create-quiz-btn" class="admin-quiz-management-create-btn">
                        Create New Quiz
                    </button>
                    <button id="admin-quiz-management-create-question-btn" class="admin-quiz-management-create-btn">
                        Create New Question
                    </button>
                </div>
            </div>
            <div id="admin-quiz-management-loading" class="hidden">Loading...</div>
            <div id="admin-quiz-management-error" class="hidden"></div>
            
            <div class="admin-quiz-management-table-container">
                <table class="admin-quiz-management-table">
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-quiz-management-table-body">
                        <!-- Populated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Quiz Modal -->
        <div id="admin-quiz-management-create-quiz-modal" class="admin-quiz-management-modal hidden">
            <div class="admin-quiz-management-modal-content">
                <span class="admin-quiz-management-modal-close" data-modal="create-quiz">&times;</span>
                <h2>Create Quiz</h2>
                <form id="admin-quiz-management-create-quiz-form">
                    <div class="admin-quiz-management-form-group">
                        <label for="create-quiz-title">Title</label>
                        <input type="text" id="create-quiz-title" name="title" required>
                    </div>
                    <div class="admin-quiz-management-form-group">
                        <label for="create-quiz-description">Description</label>
                        <textarea id="create-quiz-description" name="description"></textarea>
                    </div>
                    <button type="submit" class="admin-quiz-management-submit-btn">Create Quiz</button>
                    <button type="button" class="admin-quiz-management-cancel-btn" data-modal="create-quiz">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Create Question Modal -->
        <div id="admin-quiz-management-create-question-modal" class="admin-quiz-management-modal hidden">
            <div class="admin-quiz-management-modal-content">
                <span class="admin-quiz-management-modal-close" data-modal="create-question">&times;</span>
                <h2>Create Quiz Question</h2>
                <form id="admin-quiz-management-create-question-form">
                    <div class="admin-quiz-management-form-group">
                        <label for="create-question-quiz_id">Quiz</label>
                        <select id="create-question-quiz_id" name="quiz_id" required>
                            <option value="">Select a Quiz</option>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="admin-quiz-management-form-group">
                        <label for="create-question">Question</label>
                        <textarea id="create-question" name="question" required></textarea>
                    </div>
                    <div class="admin-quiz-management-form-group">
                        <label for="create-answer">Answer</label>
                        <input type="text" id="create-answer" name="answer" required>
                    </div>
                    <button type="submit" class="admin-quiz-management-submit-btn">Create Question</button>
                    <button type="button" class="admin-quiz-management-cancel-btn" data-modal="create-question">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Edit Question Modal -->
        <div id="admin-quiz-management-edit-question-modal" class="admin-quiz-management-modal hidden">
            <div class="admin-quiz-management-modal-content">
                <span class="admin-quiz-management-modal-close" data-modal="edit-question">&times;</span>
                <h2>Edit Quiz Question</h2>
                <form id="admin-quiz-management-edit-question-form">
                    <div class="admin-quiz-management-form-group">
                        <label for="edit-question-quiz_id">Quiz</label>
                        <select id="edit-question-quiz_id" name="quiz_id" required>
                            <option value="">Select a Quiz</option>
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                    <div class="admin-quiz-management-form-group">
                        <label for="edit-question">Question</label>
                        <textarea id="edit-question" name="question" required></textarea>
                    </div>
                    <div class="admin-quiz-management-form-group">
                        <label for="edit-answer">Answer</label>
                        <input type="text" id="edit-answer" name="answer" required>
                    </div>
                    <button type="submit" class="admin-quiz-management-submit-btn">Update Question</button>
                    <button type="button" class="admin-quiz-management-cancel-btn" data-modal="edit-question">Cancel</button>
                </form>
            </div>
        </div>

        <!-- View Question Modal -->
        <div id="admin-quiz-management-view-question-modal" class="admin-quiz-management-modal hidden">
            <div class="admin-quiz-management-modal-content">
                <span class="admin-quiz-management-modal-close" data-modal="view-question">&times;</span>
                <h2>Question Details</h2>
                <div id="admin-quiz-management-view-content">
                    <p><strong>Quiz:</strong> <span id="view-quiz_title"></span></p>
                    <p><strong>Question:</strong> <span id="view-question"></span></p>
                    <p><strong>Answer:</strong> <span id="view-answer"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    console.log('DOMContentLoaded fired for quiz management');

    const loading = document.getElementById('admin-quiz-management-loading');
    const error = document.getElementById('admin-quiz-management-error');
    const tableBody = document.getElementById('admin-quiz-management-table-body');
    const createQuizBtn = document.getElementById('admin-quiz-management-create-quiz-btn');
    const createQuestionBtn = document.getElementById('admin-quiz-management-create-question-btn');
    const filterQuizSelect = document.getElementById('admin-quiz-management-filter-quiz');
    const subtitle = document.querySelector('.admin-quiz-management-subtitle');
    const modals = {
        'create-quiz': document.getElementById('admin-quiz-management-create-quiz-modal'),
        'create-question': document.getElementById('admin-quiz-management-create-question-modal'),
        'edit-question': document.getElementById('admin-quiz-management-edit-question-modal'),
        'view-question': document.getElementById('admin-quiz-management-view-question-modal')
    };

    if (!createQuizBtn || !createQuestionBtn || !modals['create-quiz'] || !modals['create-question'] || !modals['edit-question'] || !modals['view-question'] || !filterQuizSelect) {
        console.error('Required elements not found!');
        error.textContent = 'Required elements not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    let questionData = [];
    let quizzes = [];

    // Fetch quizzes for dropdowns
    async function fetchQuizzes() {
        try {
            const response = await fetch('/api/admin/quizzes', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });
            const result = await response.json();
            if (response.ok && result.success) {
                quizzes = result.data;
                populateQuizDropdowns();
            } else {
                error.textContent = result.message || 'Failed to load quizzes.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch quizzes error:', err);
            error.textContent = 'Unable to fetch quizzes.';
            error.classList.remove('hidden');
        }
    }

    function populateQuizDropdowns() {
        const createSelect = document.getElementById('create-question-quiz_id');
        const editSelect = document.getElementById('edit-question-quiz_id');
        const filterSelect = document.getElementById('admin-quiz-management-filter-quiz');
        createSelect.innerHTML = '<option value="">Select a Quiz</option>';
        editSelect.innerHTML = '<option value="">Select a Quiz</option>';
        filterSelect.innerHTML = '<option value="">All Quizzes</option>';
        quizzes.forEach(quiz => {
            const option = `<option value="${quiz.uuid}">${quiz.title}</option>`;
            createSelect.innerHTML += option;
            editSelect.innerHTML += option;
            filterSelect.innerHTML += option;
        });
    }

    async function fetchQuestions(quizId = '') {
        console.log('Fetching quiz questions', quizId ? `for quiz ${quizId}` : '');
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        tableBody.innerHTML = '';

        let url = '/api/admin/quiz-questions';
        if (quizId) {
            url += `?quiz_id=${quizId}`;
        }

        try {
            const response = await fetch(url, {
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
                questionData = result.data;
                if (questionData.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="admin-quiz-management-no-data">No quiz questions found.</td></tr>';
                } else {
                    questionData.forEach(question => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${question.quiz?.title || 'N/A'}</td>
                            <td>${question.question || 'N/A'}</td>
                            <td>${question.answer || 'N/A'}</td>
                            <td>${new Date(question.created_at).toLocaleDateString()}</td>
                            <td>
                                <div class="admin-quiz-management-actions-wrapper">
                                    <button class="admin-quiz-management-action-btn admin-quiz-management-view-btn" data-uuid="${question.uuid}" title="View Details"></button>
                                    <button class="admin-quiz-management-action-btn admin-quiz-management-edit-btn" data-uuid="${question.uuid}" title="Edit"></button>
                                    <button class="admin-quiz-management-action-btn admin-quiz-management-delete-btn" data-uuid="${question.uuid}" title="Delete"></button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }

                // Update subtitle based on filter
                if (quizId) {
                    const selectedQuiz = quizzes.find(q => q.uuid === quizId);
                    subtitle.textContent = selectedQuiz ? `Questions for ${selectedQuiz.title}` : 'All Quiz Questions';
                } else {
                    subtitle.textContent = 'All Quiz Questions';
                }
            } else {
                error.textContent = result.message || 'Failed to load quiz questions.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch quiz questions.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    function openModal(modalType, question = null) {
        console.log('Opening modal in mode:', modalType, 'with question:', question);
        Object.values(modals).forEach(modal => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        });

        const modal = modals[modalType];
        if (!modal) {
            console.error(`Modal for mode ${modalType} not found!`);
            error.textContent = `Modal for ${modalType} not found.`;
            error.classList.remove('hidden');
            return;
        }

        if (modalType === 'edit-question' && question) {
            let hiddenUuidInput = document.getElementById('edit-question-uuid');
            if (!hiddenUuidInput) {
                hiddenUuidInput = document.createElement('input');
                hiddenUuidInput.type = 'hidden';
                hiddenUuidInput.id = 'edit-question-uuid';
                hiddenUuidInput.name = 'uuid';
                document.getElementById('admin-quiz-management-edit-question-form').appendChild(hiddenUuidInput);
            }
            hiddenUuidInput.value = question.uuid || '';
            document.getElementById('edit-question-quiz_id').value = question.quiz_id || '';
            document.getElementById('edit-question').value = question.question || '';
            document.getElementById('edit-answer').value = question.answer || '';
        } else if (modalType === 'view-question' && question) {
            document.getElementById('view-quiz_title').textContent = question.quiz?.title || 'N/A';
            document.getElementById('view-question').textContent = question.question || 'N/A';
            document.getElementById('view-answer').textContent = question.answer || 'N/A';
        } else if (modalType === 'create-quiz' || modalType === 'create-question') {
            const form = document.getElementById(`admin-quiz-management-${modalType}-form`);
            if (form) {
                form.reset();
            } else {
                console.error(`${modalType} form not found!`);
                error.textContent = `${modalType} form not found.`;
                error.classList.remove('hidden');
                return;
            }
        }

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async function handleFormSubmit(form, url, method) {
        const formData = new FormData(form);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        try {
            const response = await fetch(url, {
                method: method === 'PUT' ? 'POST' : method,
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
                if (method === 'POST' && url.includes('quizzes')) {
                    await fetchQuizzes();
                }
                await fetchQuestions(filterQuizSelect.value);
                Object.values(modals).forEach(modal => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                });
                alert(result.message);
                if (method === 'PUT' && result.data) {
                    openModal('edit-question', result.data);
                }
            } else {
                error.textContent = result.message || `Failed to ${method === 'POST' ? 'create' : 'update'} ${url.includes('quizzes') ? 'quiz' : 'quiz question'}.`;
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Form error:', err);
            error.textContent = `Unable to save ${url.includes('quizzes') ? 'quiz' : 'quiz question'}.`;
            error.classList.remove('hidden');
        }
    }

    window.handleDelete = async function(uuid) {
        if (!confirm('Are you sure you want to delete this quiz question? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/api/admin/quiz-questions/${uuid}`, {
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
                fetchQuestions(filterQuizSelect.value);
                alert(result.message);
            } else {
                error.textContent = result.message || 'Failed to delete quiz question.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Delete error:', err);
            error.textContent = 'Unable to delete quiz question.';
            error.classList.remove('hidden');
        }
    };

    document.querySelectorAll('.admin-quiz-management-modal-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            const modalId = closeBtn.getAttribute('data-modal');
            const modal = modals[modalId];
            modal.classList.add('hidden');
            modal.style.display = 'none';
        });
    });

    document.getElementById('admin-quiz-management-create-quiz-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleFormSubmit(e.target, '/api/admin/quizzes', 'POST');
    });

    document.getElementById('admin-quiz-management-create-question-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        await handleFormSubmit(e.target, '/api/admin/quiz-questions', 'POST');
    });

    document.getElementById('admin-quiz-management-edit-question-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const uuid = document.getElementById('edit-question-uuid')?.value;
        if (uuid) {
            await handleFormSubmit(e.target, `/api/admin/quiz-questions/${uuid}`, 'PUT');
        } else {
            error.textContent = 'Question UUID not found.';
            error.classList.remove('hidden');
        }
    });

    createQuizBtn.addEventListener('click', () => {
        console.log('Create quiz button clicked');
        openModal('create-quiz');
    });

    createQuestionBtn.addEventListener('click', () => {
        console.log('Create question button clicked');
        openModal('create-question');
    });

    tableBody.addEventListener('click', function (e) {
        const target = e.target.closest('.admin-quiz-management-action-btn');
        if (!target) return;

        const uuid = target.getAttribute('data-uuid');
        const question = questionData.find(q => q.uuid === uuid);

        if (target.classList.contains('admin-quiz-management-view-btn')) {
            if (question) openModal('view-question', question);
        } else if (target.classList.contains('admin-quiz-management-edit-btn')) {
            if (question) openModal('edit-question', question);
        } else if (target.classList.contains('admin-quiz-management-delete-btn')) {
            handleDelete(uuid);
        }
    });

    filterQuizSelect.addEventListener('change', (e) => {
        fetchQuestions(e.target.value);
    });

    await fetchQuizzes();
    await fetchQuestions();
});
</script>
@endsection
