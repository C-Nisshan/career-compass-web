@extends('layouts.app')

@section('content')
<div class="student-quiz-container">
    <h1 class="student-quiz-title">Skill Quizzes</h1>
    <div id="student-quiz-list" class="student-quiz-list">
        <button id="student-quiz-view-history" class="student-quiz-view-history">View History & Progress</button>
    </div>
    <div id="student-quiz-display" class="student-quiz-display" style="display: none;">
        <h2 id="student-quiz-name" class="student-quiz-name"></h2>
        <p id="student-quiz-description" class="student-quiz-description"></p>
        <div id="student-quiz-progress" class="student-quiz-progress"></div>
        <form id="student-quiz-form" class="student-quiz-form"></form>
        <div class="student-quiz-navigation">
            <button id="student-quiz-previous" class="student-quiz-previous" style="display: none;">Previous</button>
            <button id="student-quiz-submit" class="student-quiz-submit">Submit Quiz</button>
        </div>
    </div>
    <div id="student-quiz-results" class="student-quiz-results" style="display: none;">
        <h2 class="student-quiz-results-title">Quiz Results</h2>
        <div id="student-quiz-feedback" class="student-quiz-feedback"></div>
        <button id="student-quiz-back" class="student-quiz-back">Back to Quizzes</button>
    </div>
    <div id="student-quiz-history" class="student-quiz-history" style="display: none;">
        <h2 class="student-quiz-history-title">Quiz History & Progress</h2>
        <table class="student-quiz-history-table">
            <thead>
                <tr>
                    <th>Quiz Title</th>
                    <th>Score</th>
                    <th>Taken At</th>
                </tr>
            </thead>
            <tbody id="student-quiz-history-body"></tbody>
        </table>
        <div class="student-quiz-progress-chart-container">
            <canvas id="student-quiz-progress-chart"></canvas>
        </div>
        <button id="student-quiz-back-to-list" class="student-quiz-back-to-list">Back to Quizzes</button>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/student/skill-quiz/student-quiz.css') }}">
@endpush

@push('scripts')
<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/student/student-quiz.js') }}"></script>
@endpush