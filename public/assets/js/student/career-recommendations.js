document.addEventListener('DOMContentLoaded', () => {
    const submitBtn = document.getElementById('studentCareerSubmitBtn');
    const inputSection = document.getElementById('studentCareerInputSection');
    const resultsSection = document.getElementById('studentCareerResultsSection');
    const tryAgainBtn = document.querySelector('.student-career-try-again-btn');
    const closeBtn = document.querySelector('.student-career-close-btn');

    // Handle submit
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const userInput = document.getElementById('studentCareerUserInput');
            const inputError = document.getElementById('studentCareerInputError');
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
                // Prepare results in vertical layout
                const responseArea = document.getElementById('studentCareerResponseArea');
                let html = '';
                if (data.length === 0) {
                    html = '<div class="alert alert-info">No recommendations found. Try providing more details!</div>';
                } else {
                    data.forEach((item, index) => {
                        html += `
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-light font-weight-bold">
                                    ${index + 1}. ${item.career.charAt(0).toUpperCase() + item.career.slice(1)} <span class="text-muted">(${item.field})</span>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Confidence:</strong> ${(item.confidence * 100).toFixed(2)}%</p>
                                    <p class="text-secondary">${item.explanation}</p>
                                </div>
                            </div>
                        `;
                    });
                }
                responseArea.innerHTML = html;

                // Hide input section and show results section
                inputSection.style.display = 'none';
                resultsSection.style.display = 'block';
                window.scrollTo({ top: resultsSection.offsetTop, behavior: 'smooth' });

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

    // Handle Try Again button
    if (tryAgainBtn) {
        tryAgainBtn.addEventListener('click', () => {
            resultsSection.style.display = 'none';
            inputSection.style.display = 'block';
            window.scrollTo({ top: inputSection.offsetTop, behavior: 'smooth' });
        });
    }

    // Handle Close button
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            resultsSection.style.display = 'none';
            inputSection.style.display = 'block';
            window.scrollTo({ top: inputSection.offsetTop, behavior: 'smooth' });
        });
    }
});