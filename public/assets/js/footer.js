document.addEventListener('DOMContentLoaded', () => {
    // Newsletter form (only attach listener if form exists)
    const newsletterForm = document.querySelector('.newsletter form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Subscribed! Thank you for joining!');
        });
    }

    // Chatbot toggle: Open input modal
    const chatbot = document.querySelector('.chatbot');
    if (chatbot) {
        chatbot.addEventListener('click', () => {
            const inputModal = new bootstrap.Modal(document.getElementById('careerInputModalUnique'));
            inputModal.show();
        });
    }

    // Handle submit in input modal
    const submitBtn = document.getElementById('careerSubmitBtnUnique');
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const userInput = document.getElementById('careerUserInputUnique');
            const inputError = document.getElementById('careerInputErrorUnique');
            const text = userInput.value.trim();

            if (!text) {
                inputError.innerHTML = '<div class="alert alert-warning">Please enter a description to get recommendations.</div>';
                return;
            }

            // Clear any previous error
            inputError.innerHTML = '';
            // Show loading spinner in button
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Analyzing...';
            submitBtn.disabled = true;

            fetch('/api/predict-career', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ text: text })
            })
            .then(response => {
                if (!response.ok) throw new Error('API error');
                return response.json();
            })
            .then(data => {
                // Close input modal
                const inputModal = bootstrap.Modal.getInstance(document.getElementById('careerInputModalUnique'));
                inputModal.hide();

                // Prepare results in three-column grid
                const responseArea = document.getElementById('careerResponseAreaUnique');
                let html = '';
                if (data.length === 0) {
                    html = '<div class="col-12"><div class="alert alert-info">No recommendations found. Try providing more details!</div></div>';
                } else {
                    data.forEach((item, index) => {
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-light font-weight-bold">
                                        ${index + 1}. ${item.career.charAt(0).toUpperCase() + item.career.slice(1)} <span class="text-muted">(${item.field})</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Confidence:</strong> ${(item.confidence * 100).toFixed(2)}%</p>
                                        <p class="text-secondary">${item.explanation}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                responseArea.innerHTML = html;

                // Open results modal
                const resultsModal = new bootstrap.Modal(document.getElementById('careerResultsModalUnique'));
                resultsModal.show();

                // Clear input
                userInput.value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                inputError.innerHTML = '<div class="alert alert-danger">An error occurred while fetching recommendations. Please try again.</div>';
            })
            .finally(() => {
                submitBtn.innerHTML = 'Get Recommendations';
                submitBtn.disabled = false;
            });
        });
    }
});
