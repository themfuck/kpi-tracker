<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super_admin role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'super_admin']);

        // Check if super admin already exists
        $superAdmin = User::where('email', 'muchramdan123@gmail.com')->first();

        if (!$superAdmin) {
            // Create super admin user
            $superAdmin = User::create([
                'name' => 'admin',
                'email' => 'muchramdan123@gmail.com',
                'password' => Hash::make('password'), // Change this!
            ]);

            $this->command->info('Super admin user created!');
        } else {
            $this->command->info('Super admin user already exists.');
        }

        // Assign super_admin role
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
            $this->command->info('Super admin role assigned!');
        } else {
            $this->command->info('User already has super_admin role.');
        }
    }
}
