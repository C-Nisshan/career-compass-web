<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>{{ __('footer.brand_name') }}</h3>
            <p>{{ __('footer.brand_description') }}</p>
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.explore_title') }}</h3>
            @foreach(__('footer.explore_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.support_title') }}</h3>
            @foreach(__('footer.support_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.connect_title') }}</h3>
            @foreach(__('footer.connect_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
    </div>
    <div class="footer-bottom">
        <p>{{ __('footer.copyright') }}</p>
    </div>
</footer>

<!-- Input Modal -->
<div class="modal fade" id="careerInputModalUnique" tabindex="-1" aria-labelledby="careerInputModalUniqueLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg career-input-modal-unique">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="careerInputModalUniqueLabel">CareerCompass AI Advisor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">Describe your interests, personality, skills, GPA, experiences, and career goals for personalized recommendations.</p>
                <textarea id="careerUserInputUnique" class="form-control mb-3" rows="5" placeholder="E.g., I enjoy painting, writing short stories, and designing posters for school events. My GPA is 3.6 and I volunteered to organize an art exhibition last year. I want a career where I can use my creativity."></textarea>
                <button id="careerSubmitBtnUnique" class="btn btn-primary w-100 mb-3">Get Recommendations</button>
                <div id="careerInputErrorUnique" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary input-close-btn-unique" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="careerResultsModalUnique" tabindex="-1" aria-labelledby="careerResultsModalUniqueLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl career-results-modal-unique">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-center" id="careerResultsModalUniqueLabel">Your Career Recommendations</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="careerResponseAreaUnique" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn results-try-again-btn-unique" data-bs-dismiss="modal" data-bs-target="#careerInputModalUnique" data-bs-toggle="modal">Try Again</button>
                <button type="button" class="btn results-close-btn-unique" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="{{ __('home.chatbot_alt') }}">
</div>
