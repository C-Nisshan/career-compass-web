// assets/js/student/student-quiz.js

let currentQuestionIndex = 0;
let questions = [];
let answers = {};
let progressChart;

function setupEventListeners() {
    const viewHistoryButton = document.getElementById('student-quiz-view-history');
    if (viewHistoryButton) {
        console.log('View History button found, attaching event listener');
        viewHistoryButton.addEventListener('click', () => {
            console.log('View History button clicked');
            document.getElementById('student-quiz-list').style.display = 'none';
            document.getElementById('student-quiz-history').style.display = 'block';
            fetchHistory();
        });
    } else {
        console.error('View History button not found in DOM');
    }

    const backButton = document.getElementById('student-quiz-back');
    if (backButton) {
        backButton.addEventListener('click', () => {
            console.log('Back to Quizzes button clicked from results');
            document.getElementById('student-quiz-results').style.display = 'none';
            document.getElementById('student-quiz-list').style.display = 'block';
            document.getElementById('student-quiz-display').style.display = 'none';
            currentQuestionIndex = 0;
            questions = [];
            answers = {};
        });
    }

    const backToListButton = document.getElementById('student-quiz-back-to-list');
    if (backToListButton) {
        backToListButton.addEventListener('click', () => {
            console.log('Back to Quizzes button clicked from history');
            document.getElementById('student-quiz-history').style.display = 'none';
            document.getElementById('student-quiz-list').style.display = 'block';
            if (progressChart) {
                progressChart.destroy();
                progressChart = null;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded, setting up event listeners');
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
    } else {
        console.log('Chart.js is loaded');
    }
    setupEventListeners();
    fetchQuizzes();
});

async function fetchQuizzes() {
    try {
        console.log('Fetching quizzes');
        const response = await fetch('/api/student/quizzes', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        if (data.success) {
            console.log('Quizzes fetched successfully:', data.data);
            displayQuizzes(data.data);
        } else {
            console.error('Failed to fetch quizzes:', data.message);
        }
    } catch (error) {
        console.error('Error fetching quizzes:', error);
    }
}

function displayQuizzes(quizzes) {
    const list = document.getElementById('student-quiz-list');
    const existingButton = document.getElementById('student-quiz-view-history');
    list.innerHTML = existingButton ? existingButton.outerHTML : '';
    quizzes.forEach(quiz => {
        const item = document.createElement('div');
        item.classList.add('student-quiz-item');
        item.innerHTML = `
            <h3>${quiz.title}</h3>
            <p>${quiz.description || 'No description available'}</p>
            <button class="student-quiz-start" onclick="startQuiz('${quiz.uuid}', '${quiz.title}', '${quiz.description || ''}')">Start Quiz</button>
        `;
        list.appendChild(item);
    });
    setupEventListeners();
}

async function startQuiz(uuid, title, description) {
    try {
        console.log('Starting quiz:', uuid);
        const response = await fetch(`/api/student/quizzes/${uuid}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        if (data.success) {
            questions = data.data.questions;
            displayQuiz(data.data, uuid);
        } else {
            console.error('Failed to fetch quiz:', data.message);
        }
    } catch (error) {
        console.error('Error fetching quiz:', error);
    }
}

function displayQuiz(quiz, quizUuid) {
    document.getElementById('student-quiz-name').textContent = quiz.title;
    document.getElementById('student-quiz-description').textContent = quiz.description || '';
    document.getElementById('student-quiz-progress').textContent = `Question ${currentQuestionIndex + 1} of ${questions.length}`;

    const form = document.getElementById('student-quiz-form');
    form.innerHTML = '';
    
    const question = questions[currentQuestionIndex];
    const questionDiv = document.createElement('div');
    questionDiv.classList.add('student-quiz-question');
    questionDiv.setAttribute('data-question-uuid', question.uuid);
    questionDiv.innerHTML = `
        <label>${question.question}</label>
        <input type="text" name="${question.uuid}" value="${answers[question.uuid] || ''}" required>
        <div class="student-quiz-question-feedback" style="display: none;"></div>
    `;
    form.appendChild(questionDiv);

    document.getElementById('student-quiz-list').style.display = 'none';
    document.getElementById('student-quiz-display').style.display = 'block';

    const input = form.querySelector('input');
    input.addEventListener('input', debounce(async () => {
        const questionUuid = input.name;
        const answer = input.value.trim();
        answers[questionUuid] = answer;

        if (answer) {
            try {
                console.log('Checking answer for question:', questionUuid);
                const response = await fetch(`/api/student/quizzes/${quizUuid}/questions/${questionUuid}/check`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ answer })
                });
                const result = await response.json();
                if (result.success) {
                    const feedbackDiv = input.parentElement.querySelector('.student-quiz-question-feedback');
                    feedbackDiv.style.display = 'block';
                    feedbackDiv.className = `student-quiz-question-feedback ${result.is_correct ? 'correct' : 'incorrect'}`;
                    feedbackDiv.textContent = result.is_correct ? 'Correct!' : 'Incorrect';

                    if (result.is_correct && currentQuestionIndex < questions.length - 1) {
                        setTimeout(() => {
                            currentQuestionIndex++;
                            displayQuiz(quiz, quizUuid);
                        }, 1000);
                    }
                } else {
                    console.error('Failed to check answer:', result.message);
                }
            } catch (error) {
                console.error('Error checking answer:', error);
            }
        }
    }, 500));

    const previousButton = document.getElementById('student-quiz-previous');
    previousButton.style.display = currentQuestionIndex > 0 ? 'block' : 'none';
    previousButton.removeEventListener('click', handlePrevious);
    previousButton.addEventListener('click', handlePrevious);

    function handlePrevious() {
        if (currentQuestionIndex > 0) {
            console.log('Navigating to previous question:', currentQuestionIndex - 1);
            currentQuestionIndex--;
            displayQuiz(quiz, quizUuid);
        }
    }

    const submitButton = document.getElementById('student-quiz-submit');
    submitButton.removeEventListener('click', handleSubmit);
    submitButton.addEventListener('click', handleSubmit);

    async function handleSubmit(event) {
        event.preventDefault();
        try {
            console.log('Submitting quiz:', quizUuid);
            const response = await fetch(`/api/student/quizzes/${quizUuid}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ answers })
            });
            const result = await response.json();
            if (result.success) {
                displayResults(result);
            } else {
                console.error('Failed to submit quiz:', result.message);
            }
        } catch (error) {
            console.error('Error submitting quiz:', error);
        }
    }
}

function displayResults(result) {
    const resultsDiv = document.getElementById('student-quiz-results');
    const feedbackDiv = document.getElementById('student-quiz-feedback');
    feedbackDiv.innerHTML = `<p class="student-quiz-score">Your Score: ${result.score.toFixed(2)}%</p>`;
    
    Object.keys(result.feedback).forEach(key => {
        const fb = result.feedback[key];
        const item = document.createElement('div');
        item.classList.add('student-quiz-feedback-item', fb.is_correct ? 'correct' : 'incorrect');
        item.innerHTML = `
            <p><strong>Question:</strong> ${fb.question}</p>
            <p><strong>Your Answer:</strong> ${fb.user_answer}</p>
            <p><strong>Correct Answer:</strong> ${fb.correct_answer}</p>
            <p><strong>Status:</strong> ${fb.is_correct ? 'Correct' : 'Incorrect'}</p>
        `;
        feedbackDiv.appendChild(item);
    });

    document.getElementById('student-quiz-display').style.display = 'none';
    resultsDiv.style.display = 'block';
}

async function fetchHistory() {
    try {
        console.log('Fetching quiz history');
        const response = await fetch('/api/student/quiz-history', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        const data = await response.json();
        console.log('Quiz history response:', data);
        if (data.success) {
            displayHistory(data.data);
        } else {
            console.error('Failed to fetch history:', data.message);
            const body = document.getElementById('student-quiz-history-body');
            body.innerHTML = '<tr><td colspan="3">Failed to load history: ' + data.message + '</td></tr>';
        }
    } catch (error) {
        console.error('Error fetching history:', error);
        const body = document.getElementById('student-quiz-history-body');
        body.innerHTML = '<tr><td colspan="3">Error loading history. Please check your connection.</td></tr>';
    }
}

function displayHistory(history) {
    console.log('Displaying history:', history);
    const body = document.getElementById('student-quiz-history-body');
    body.innerHTML = '';
    if (!history || history.length === 0) {
        console.log('No history data to display');
        body.innerHTML = '<tr><td colspan="3">No quiz history available.</td></tr>';
        return;
    }

    history.forEach(item => {
        console.log('Adding history item:', item);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.quiz_title}</td>
            <td>${item.score.toFixed(2)}%</td>
            <td>${item.taken_at}</td>
        `;
        body.appendChild(row);
    });

    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded, skipping chart rendering');
        const chartContainer = document.getElementById('student-quiz-progress-chart-container');
        chartContainer.innerHTML = '<p>Unable to render progress chart. Please try again later.</p>';
        return;
    }

    const ctx = document.getElementById('student-quiz-progress-chart');
    if (!ctx) {
        console.error('Canvas element student-quiz-progress-chart not found');
        return;
    }
    const context = ctx.getContext('2d');
    if (!context) {
        console.error('Failed to get 2D context for canvas');
        return;
    }

    const labels = history.map(item => item.taken_at);
    const scores = history.map(item => item.score);

    if (progressChart) {
        console.log('Destroying existing chart');
        progressChart.destroy();
        progressChart = null;
    }

    try {
        console.log('Creating new chart with labels:', labels, 'and scores:', scores);
        progressChart = new Chart(context, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Score Over Time',
                    data: scores,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.2)',
                    fill: true,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Score (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date Taken'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
        const chartContainer = document.getElementById('student-quiz-progress-chart-container');
        chartContainer.innerHTML = '<p>Unable to render progress chart. Please try again later.</p>';
    }
}

// Debounce function to limit API calls during typing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}