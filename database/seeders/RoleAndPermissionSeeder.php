<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Core Permissions
        $permissions = [
            'create innovations',
            'edit own innovations',
            'submit innovations',
            'view reviewer dashboard',
            'review innovations',
            'rate innovations',
            'post reviewer comments',
            'manage users',
            'manage portal settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles and Assign Permissions
        $innovatorRole = Role::findOrCreate('innovator');
        $innovatorRole->syncPermissions([
            'create innovations',
            'edit own innovations',
            'submit innovations',
        ]);

        $reviewerRole = Role::findOrCreate('reviewer');
        $reviewerRole->syncPermissions([
            'view reviewer dashboard',
            'review innovations',
            'rate innovations',
            'post reviewer comments',
        ]);

        $adminRole = Role::findOrCreate('admin');
        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // 3. Create Default Demonstration Users for KSG
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@ksg.ac.ke')],
            [
                'name' => env('ADMIN_NAME', 'KSG Portal Administrator'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'department' => env('ADMIN_DEPARTMENT', 'ICT & Innovation Directorate'),
                'phone' => env('ADMIN_PHONE', '+254700000001'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@ksg.ac.ke'],
            [
                'name' => 'Dr. Jane Reviewer',
                'password' => Hash::make('password'),
                'department' => 'Academic & Research Evaluation Committee',
                'phone' => '+254700000002',
                'email_verified_at' => now(),
            ]
        );
        $reviewer->syncRoles(['reviewer']);

        $innovator = User::firstOrCreate(
            ['email' => 'innovator@ksg.ac.ke'],
            [
                'name' => 'John Innovator',
                'password' => Hash::make('password'),
                'department' => 'Public Service Transformation Wing',
                'phone' => '+254700000003',
                'email_verified_at' => now(),
            ]
        );
        $innovator->syncRoles(['innovator']);
    }
}
