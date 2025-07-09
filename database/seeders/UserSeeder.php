<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\StudentProfile;
use App\Models\MentorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['student', 'admin', 'mentor'];

        foreach ($roles as $role) {
            $uuid = Str::uuid();

            $user = User::create([
                'uuid' => $uuid,
                'email' => "$role@example.com",
                'password' => Hash::make('password123'),
                'role' => $role,
                'status' => 'approved',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Common profile
            Profile::create([
                'uuid' => Str::uuid(),
                'user_id' => $uuid,
                'first_name' => ucfirst($role),
                'last_name' => 'User',
                'phone' => '07' . rand(10000000, 99999999),
                'address' => '123 Main St',
                'nic_number' => strtoupper(Str::random(10)),
                'profile_picture_path' => null,
                'verified_status' => 'approved',
                'completion_step' => 'complete',
            ]);

            // Role-specific profile
            if ($role === 'student') {
                StudentProfile::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $uuid,
                    'date_of_birth' => '2005-05-15',
                    'school' => 'Greenhill High School',
                    'grade_level' => 'O-Level',
                    'learning_style' => 'visual',
                    'subjects_interested' => json_encode(['Math', 'Science']),
                    'career_goals' => 'Become a Software Engineer',
                    'location' => 'Colombo',
                ]);
            }

            if ($role === 'mentor') {
                MentorProfile::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $uuid,
                    'profession_title' => 'Senior Software Engineer',
                    'industry' => 'Information Technology',
                    'experience_years' => 8,
                    'bio' => 'Passionate mentor helping students explore tech careers.',
                    'areas_of_expertise' => json_encode(['AI', 'Web Development']),
                    'linkedin_url' => 'https://linkedin.com/in/mentor',
                    'portfolio_url' => 'https://mentorportfolio.com',
                    'availability' => 'Weekdays 6PM-9PM',
                ]);
            }
        }
    }
}
