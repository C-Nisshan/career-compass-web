<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MentorProfile;
use App\Models\StudentProfile;
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
            $firstName = ucfirst($role);
            $lastName = 'User';

            // Create user
            $user = User::create([
                'uuid' => $uuid,
                'email' => "$role@example.com",
                'password' => Hash::make('password123'),
                'role' => $role,
                'status' => 'approved',
                'is_active' => true,
                'email_verified_at' => now(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => '07' . rand(10000000, 99999999),
                'address' => '123 Main St',
                'nic_number' => strtoupper(Str::random(10)),
                'profile_picture' => null,
            ]);

            // If mentor, seed mentor profile
            if ($role === 'mentor') {
                MentorProfile::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $uuid,
                    'profession_title' => 'Senior Software Engineer',
                    'industry' => 'Information Technology',
                    'experience_years' => 8,
                    'bio' => 'Passionate mentor helping students explore tech careers.',
                    'areas_of_expertise' => ['AI', 'Web Development'],
                    'linkedin_url' => 'https://linkedin.com/in/mentor',
                    'portfolio_url' => 'https://mentorportfolio.com',
                    'availability' => 'Weekdays 6PM–9PM',
                ]);
            }

            // If student, seed student profile
            if ($role === 'student') {
                StudentProfile::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $uuid,
                    'date_of_birth' => '2006-08-15',
                    'school' => 'Riverdale High School',
                    'grade_level' => 'O-Level',
                    'learning_style' => 'visual',
                    'subjects_interested' => ['Math', 'Science', 'English'],
                    'career_goals' => 'To become a Software Engineer',
                    'location' => 'Colombo',
                ]);
            }
        }
    }
}
