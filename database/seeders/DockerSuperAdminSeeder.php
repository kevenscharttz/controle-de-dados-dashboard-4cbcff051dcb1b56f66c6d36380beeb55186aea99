<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DockerSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates a super-admin user (idempotent).
     *
     * @return void
     */
    public function run(): void
    {
        // Fixed admin credentials to avoid env dependency during development
        $email = 'admin@example.com';
        $password = 'admin123';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => $email,
                // Let Laravel "hashed" cast handle hashing
                'password' => $password,
            ]);
        } else {
            // Ensure the password is updated to the fixed value
            $user->update(['password' => $password]);
        }

        // Ensure roles exist and assign them
        try {
            $roleSnake = Role::firstOrCreate(['name' => 'super_admin']);
            $roleKebab = Role::firstOrCreate(['name' => 'super-admin']);

            // Assign both role name variants for maximum compatibility
            if (! $user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
            }
            if (! $user->hasRole('super-admin')) {
                $user->assignRole('super-admin');
            }

            // Grant ALL existing permissions to both super admin roles
            try {
                $permissionNames = Permission::pluck('name');
                if ($permissionNames && $permissionNames->count() > 0) {
                    $roleSnake->syncPermissions($permissionNames);
                    $roleKebab->syncPermissions($permissionNames);
                }
            } catch (\Throwable $e) {
                // Ignore if permissions table not ready
            }
        } catch (\Throwable $e) {
            // Spatie tables may not be present or package not installed; ignore gracefully
            // Logging would help, but we keep it silent for bootstrap
        }
    }
}
