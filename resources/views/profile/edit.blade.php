@extends('layouts.app')

@section('content')
<div class="container min-vh-90 d-flex align-items-center justify-content-center" style="background-color: #f0f2f5;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 600px;">
        <h3 class="text-center mb-4">Your {{ auth()->user()->role === 'mentor' ? 'Mentor' : 'Student' }} Profile</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- General Profile Fields -->
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $user->address ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="nic_number" class="form-label">NIC Number</label>
                <input type="text" name="nic_number" id="nic_number" class="form-control" value="{{ old('nic_number', $user->nic_number ?? '') }}">
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                @if ($user->profile_picture)
                    <small class="form-text text-muted">Current: <a href="{{ Storage::url($user->profile_picture) }}" target="_blank">View Picture</a></small>
                @endif
            </div>

            @if (auth()->user()->role === 'student')
                <!-- Student Profile Fields -->
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $roleProfile->date_of_birth ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="school" class="form-label">School</label>
                    <input type="text" name="school" id="school" class="form-control" value="{{ old('school', $roleProfile->school ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="grade_level" class="form-label">Grade Level</label>
                    <input type="text" name="grade_level" id="grade_level" class="form-control" value="{{ old('grade_level', $roleProfile->grade_level ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="learning_style" class="form-label">Learning Style</label>
                    <select name="learning_style" id="learning_style" class="form-select">
                        <option value="" {{ old('learning_style', $roleProfile->learning_style ?? '') === '' ? 'selected' : '' }}>Select</option>
                        <option value="visual" {{ old('learning_style', $roleProfile->learning_style ?? '') === 'visual' ? 'selected' : '' }}>Visual</option>
                        <option value="auditory" {{ old('learning_style', $roleProfile->learning_style ?? '') === 'auditory' ? 'selected' : '' }}>Auditory</option>
                        <option value="kinesthetic" {{ old('learning_style', $roleProfile->learning_style ?? '') === 'kinesthetic' ? 'selected' : '' }}>Kinesthetic</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subjects_interested" class="form-label">Subjects Interested</label>
                    <select name="subjects_interested[]" id="subjects_interested" class="form-select" multiple>
                        <option value="Math" {{ in_array('Math', old('subjects_interested', $roleProfile->subjects_interested ?? [])) ? 'selected' : '' }}>Math</option>
                        <option value="Science" {{ in_array('Science', old('subjects_interested', $roleProfile->subjects_interested ?? [])) ? 'selected' : '' }}>Science</option>
                        <option value="Literature" {{ in_array('Literature', old('subjects_interested', $roleProfile->subjects_interested ?? [])) ? 'selected' : '' }}>Literature</option>
                        <option value="Art" {{ in_array('Art', old('subjects_interested', $roleProfile->subjects_interested ?? [])) ? 'selected' : '' }}>Art</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="career_goals" class="form-label">Career Goals</label>
                    <textarea name="career_goals" id="career_goals" class="form-control" rows="5">{{ old('career_goals', $roleProfile->career_goals ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $roleProfile->location ?? '') }}">
                </div>
            @elseif (auth()->user()->role === 'mentor')
                <!-- Mentor Profile Fields -->
                <div class="mb-3">
                    <label for="profession_title" class="form-label">Profession Title</label>
                    <input type="text" name="profession_title" id="profession_title" class="form-control" value="{{ old('profession_title', $roleProfile->profession_title ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="industry" class="form-label">Industry</label>
                    <input type="text" name="industry" id="industry" class="form-control" value="{{ old('industry', $roleProfile->industry ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="experience_years" class="form-label">Years of Experience</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control" value="{{ old('experience_years', $roleProfile->experience_years ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="5">{{ old('bio', $roleProfile->bio ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="areas_of_expertise" class="form-label">Areas of Expertise</label>
                    <select name="areas_of_expertise[]" id="areas_of_expertise" class="form-select" multiple>
                        <option value="AI" {{ in_array('AI', old('areas_of_expertise', $roleProfile->areas_of_expertise ?? [])) ? 'selected' : '' }}>AI</option>
                        <option value="HR" {{ in_array('HR', old('areas_of_expertise', $roleProfile->areas_of_expertise ?? [])) ? 'selected' : '' }}>HR</option>
                        <option value="Engineering" {{ in_array('Engineering', old('areas_of_expertise', $roleProfile->areas_of_expertise ?? [])) ? 'selected' : '' }}>Engineering</option>
                        <option value="Medicine" {{ in_array('Medicine', old('areas_of_expertise', $roleProfile->areas_of_expertise ?? [])) ? 'selected' : '' }}>Medicine</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" id="linkedin_url" class="form-control" value="{{ old('linkedin_url', $roleProfile->linkedin_url ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="portfolio_url" class="form-label">Portfolio URL</label>
                    <input type="url" name="portfolio_url" id="portfolio_url" class="form-control" value="{{ old('portfolio_url', $roleProfile->portfolio_url ?? '') }}">
                </div>
                <div class="mb-3">
                    <label for="availability" class="form-label">Availability</label>
                    <input type="text" name="availability" id="availability" class="form-control" value="{{ old('availability', $roleProfile->availability ?? '') }}">
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100">Save Profile</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const progressBar = document.querySelector('.progress-bar');
            const inputs = form.querySelectorAll('input, select, textarea');

            function updateProgress() {
                let filled = 0;
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length > 0) filled++;
                    } else if (input.type === 'checkbox') {
                        if (input.checked) filled++;
                    } else if (input.tagName === 'SELECT' && input.multiple) {
                        if (input.selectedOptions.length > 0) filled++;
                    } else if (input.value.trim() !== '') {
                        filled++;
                    }
                });
                const progress = Math.min((filled / inputs.length) * 100, 100);
                progressBar.style.width = `${progress}%`;
                progressBar.setAttribute('aria-valuenow', progress);
                progressBar.textContent = `${Math.round(progress)}% Complete`;
            }

            inputs.forEach(input => {
                input.addEventListener('input', updateProgress);
                input.addEventListener('change', updateProgress);
            });
            updateProgress();
        });
    </script>
@endpush