<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'System owner with full access',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with management access',
            ],
            [
                'name' => 'Instructor',
                'slug' => 'instructor',
                'description' => 'Course instructor/teacher',
            ],
            [
                'name' => 'Student',
                'slug' => 'student',
                'description' => 'Student enrolled in courses',
            ],
        ];

        $permissions = [
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Create, update, delete users'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Create, update, delete roles'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Create, update, delete permissions'],
            ['name' => 'Manage Courses', 'slug' => 'manage-courses', 'description' => 'Create, update, delete courses'],
            ['name' => 'View Courses', 'slug' => 'view-courses', 'description' => 'View all courses'],
            ['name' => 'Create Courses', 'slug' => 'create-courses', 'description' => 'Create new courses'],
            ['name' => 'Edit Courses', 'slug' => 'edit-courses', 'description' => 'Edit existing courses'],
            ['name' => 'Delete Courses', 'slug' => 'delete-courses', 'description' => 'Delete courses'],
            ['name' => 'Manage Chapters', 'slug' => 'manage-chapters', 'description' => 'Create, update, delete chapters'],
            ['name' => 'Manage Lessons', 'slug' => 'manage-lessons', 'description' => 'Create, update, delete lessons'],
            ['name' => 'Manage Enrollments', 'slug' => 'manage-enrollments', 'description' => 'Manage student enrollments'],
            ['name' => 'Manage Attendance', 'slug' => 'manage-attendance', 'description' => 'Manage student attendance'],
            ['name' => 'View Attendance', 'slug' => 'view-attendance', 'description' => 'View attendance records'],
            ['name' => 'Manage Media', 'slug' => 'manage-media', 'description' => 'Upload and manage media files'],
            ['name' => 'Manage Sessions', 'slug' => 'manage-sessions', 'description' => 'Manage user sessions'],
            ['name' => 'Ban Users', 'slug' => 'ban-users', 'description' => 'Ban or unban users'],
            ['name' => 'Access Admin Panel', 'slug' => 'access-admin', 'description' => 'Access admin dashboard'],
            ['name' => 'Access Instructor Panel', 'slug' => 'access-instructor', 'description' => 'Access instructor dashboard'],
            ['name' => 'Access Student Panel', 'slug' => 'access-student', 'description' => 'Access student dashboard'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        $owner = Role::where('slug', 'owner')->first();
        $admin = Role::where('slug', 'admin')->first();
        $instructor = Role::where('slug', 'instructor')->first();
        $student = Role::where('slug', 'student')->first();

        $allPermissions = Permission::all();
        $owner->permissions()->sync($allPermissions->pluck('id'));

        $adminPermissions = Permission::whereIn('slug', [
            'manage-users',
            'manage-courses',
            'view-courses',
            'create-courses',
            'edit-courses',
            'delete-courses',
            'manage-chapters',
            'manage-lessons',
            'manage-enrollments',
            'manage-attendance',
            'view-attendance',
            'manage-media',
            'manage-sessions',
            'ban-users',
            'access-admin',
        ])->get();
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        $instructorPermissions = Permission::whereIn('slug', [
            'manage-courses',
            'view-courses',
            'create-courses',
            'edit-courses',
            'manage-chapters',
            'manage-lessons',
            'manage-enrollments',
            'manage-attendance',
            'view-attendance',
            'manage-media',
            'access-instructor',
        ])->get();
        $instructor->permissions()->sync($instructorPermissions->pluck('id'));

        $studentPermissions = Permission::whereIn('slug', [
            'view-courses',
            'view-attendance',
            'access-student',
        ])->get();
        $student->permissions()->sync($studentPermissions->pluck('id'));
    }
}
