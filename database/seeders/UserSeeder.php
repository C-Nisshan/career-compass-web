<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['student', 'admin', 'mentor'];

        foreach ($roles as $role) {
            User::create([
                'uuid' => Str::uuid(),
                'email' => "$role@example.com",
                'password' => Hash::make('password123'),
                'role' => $role,
                'status' => 'approved',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
