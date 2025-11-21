<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $ownerRole = Role::where('slug', 'owner')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $instructorRole = Role::where('slug', 'instructor')->first();
        $studentRole = Role::where('slug', 'student')->first();

        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'System Owner',
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->id,
                'is_banned' => false,
                'email_verified_at' => now(),
            ]
        );
        if ($owner->role_id !== $ownerRole->id) {
            $owner->update(['role_id' => $ownerRole->id]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_banned' => false,
                'email_verified_at' => now(),
            ]
        );
        if ($admin->role_id !== $adminRole->id) {
            $admin->update(['role_id' => $adminRole->id]);
        }

        $instructor = User::firstOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Instructor',
                'password' => Hash::make('password'),
                'role_id' => $instructorRole->id,
                'is_banned' => false,
                'email_verified_at' => now(),
            ]
        );
        if ($instructor->role_id !== $instructorRole->id) {
            $instructor->update(['role_id' => $instructorRole->id]);
        }

        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
                'is_banned' => false,
                'email_verified_at' => now(),
            ]
        );
        if ($student->role_id !== $studentRole->id) {
            $student->update(['role_id' => $studentRole->id]);
        }
    }
}
