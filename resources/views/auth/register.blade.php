@extends('layouts.app')

@section('content')
<div class="container min-vh-90 d-flex align-items-center justify-content-center" style="background-color: #f0f2f5;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 600px;">
        <h3 class="text-center mb-4">Sign Up</h3>

        <div id="sign-up-messages"></div>

        <form id="sign-up-form" method="POST" action="#" enctype="multipart/form-data">
            @csrf

            <!-- Role Selection -->
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="mentor">Mentor</option>
                </select>
            </div>

            <!-- General User Fields -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control">
            </div>
            <div class="mb-3">
                <label for="nic_number" class="form-label">NIC Number</label>
                <input type="text" name="nic_number" id="nic_number" class="form-control">
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control">
            </div>

            <!-- Student-Specific Fields -->
            <div class="provider-fields student-fields d-none">
                <div class="mb-3">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="school" class="form-label">School</label>
                    <input type="text" name="school" id="school" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="grade_level" class="form-label">Grade Level</label>
                    <input type="text" name="grade_level" id="grade_level" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="learning_style" class="form-label">Learning Style</label>
                    <select name="learning_style" id="learning_style" class="form-select">
                        <option value="">Select</option>
                        <option value="visual">Visual</option>
                        <option value="auditory">Auditory</option>
                        <option value="kinesthetic">Kinesthetic</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subjects_interested" class="form-label">Subjects Interested</label>
                    <select name="subjects_interested[]" id="subjects_interested" class="form-select" multiple>
                        <option value="Math">Math</option>
                        <option value="Science">Science</option>
                        <option value="Literature">Literature</option>
                        <option value="Art">Art</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="career_goals" class="form-label">Career Goals</label>
                    <textarea name="career_goals" id="career_goals" class="form-control" rows="5"></textarea>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control">
                </div>
            </div>

            <!-- Mentor-Specific Fields -->
            <div class="provider-fields mentor-fields d-none">
                <div class="mb-3">
                    <label for="profession_title" class="form-label">Profession Title</label>
                    <input type="text" name="profession_title" id="profession_title" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="industry" class="form-label">Industry</label>
                    <input type="text" name="industry" id="industry" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="experience_years" class="form-label">Years of Experience</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="5"></textarea>
                </div>
                <div class="mb-3">
                    <label for="areas_of_expertise" class="form-label">Areas of Expertise</label>
                    <select name="areas_of_expertise[]" id="areas_of_expertise" class="form-select" multiple>
                        <option value="AI">AI</option>
                        <option value="HR">HR</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Medicine">Medicine</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" id="linkedin_url" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="portfolio_url" class="form-label">Portfolio URL</label>
                    <input type="url" name="portfolio_url" id="portfolio_url" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="availability" class="form-label">Availability</label>
                    <input type="text" name="availability" id="availability" class="form-control">
                </div>
            </div>

            <button type="submit" id="sign-up-button" class="btn btn-primary w-100">
                Sign Up <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('config.js') }}"></script>
    <script src="{{ asset('assets/js/auth/register.js') }}"></script>
@endpush