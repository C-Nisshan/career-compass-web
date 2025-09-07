@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="career-recommendations-title">Student Career Recommendations</h1>

        <!-- Input Section -->
        <div class="student-career-input-section" id="studentCareerInputSection">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">CareerCompass AI Advisor</h5>
                </div>
                <div class="card-body">
                    <p class="lead">Describe your interests, personality, skills, GPA, experiences, and career goals for personalized recommendations.</p>
                    <textarea id="studentCareerUserInput" class="form-control mb-3" rows="5" placeholder="E.g., I enjoy painting, writing short stories, and designing posters for school events. My GPA is 3.6 and I volunteered to organize an art exhibition last year. I want a career where I can use my creativity."></textarea>
                    <button id="studentCareerSubmitBtn" class="btn btn-primary w-100 mb-3">Get Recommendations</button>
                    <div id="studentCareerInputError" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="student-career-results-section" id="studentCareerResultsSection" style="display: none;">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Your Career Recommendations</h5>
                    <button id="studentCareerDownloadBtn" class="btn btn-outline-light btn-sm" title="Download as PDF">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </div>
                <div class="card-body">
                    <div id="studentCareerResponseArea" class="d-flex flex-column"></div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn student-career-try-again-btn">Try Again</button>
                    <button type="button" class="btn student-career-close-btn">Close</button>
                </div>
            </div>
        </div>

        <!-- Hidden PDF Template -->
        <div id="pdfTemplate" style="display: none;">
            <div class="pdf-content">
                <h1 style="text-align: center; color: #2c2c54;">Career Recommendations</h1>
                <div id="pdfResponseArea"></div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/student/career-recommendations/career-recommendations.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/student/career-recommendations.js') }}"></script>
@endpush