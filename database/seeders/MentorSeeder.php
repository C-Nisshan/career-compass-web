<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MentorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $uuid = Str::uuid();
            $firstName = fake()->firstName;
            $lastName = fake()->lastName;

            // Create user with mentor role
            $user = User::create([
                'uuid' => $uuid,
                'email' => "mentor{$i}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'mentor',
                'status' => 'approved',
                'is_active' => true,
                'email_verified_at' => now(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => '07' . rand(10000000, 99999999),
                'address' => fake()->address,
                'nic_number' => strtoupper(Str::random(10)),
                'profile_picture' => null,
            ]);

            // Create mentor profile
            MentorProfile::create([
                'uuid' => Str::uuid(),
                'user_id' => $uuid,
                'profession_title' => fake()->jobTitle,
                'industry' => fake()->randomElement(['IT', 'Finance', 'Education', 'Healthcare']),
                'experience_years' => rand(3, 15),
                'bio' => fake()->paragraph(2),
                'areas_of_expertise' => ['AI', 'Web Development', 'Cybersecurity'],
                'linkedin_url' => "https://linkedin.com/in/mentor{$i}",
                'portfolio_url' => "https://mentor{$i}portfolio.com",
                'availability' => 'Weekdays 6PM–9PM',
            ]);
        }
    }
}
