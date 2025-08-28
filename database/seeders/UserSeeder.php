<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $plainAdminPassword = env('SEED_ADMIN_PASSWORD');
        $plainEmployeePassword = env('SEED_EMPLOYEE_PASSWORD');

        // Validate presence
        if (empty($plainAdminPassword) || empty($plainEmployeePassword)) {
            throw new \Exception('Missing SEED_ADMIN_PASSWORD or SEED_EMPLOYEE_PASSWORD in .env file.');
        }        

        \Log::info('Seeding with admin password: ' . $plainAdminPassword);
        //$hashedAdminPassword = bcrypt($plainAdminPassword);
        $hashedAdminPassword = Hash::make($plainAdminPassword);

        // Custom Create 3 admin users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Admin User {$i}",
                'email' => "admin_{$i}@example.com",
                'password' => $hashedAdminPassword,
                'role' => 'ADMIN'
            ]);
        }
        
        \Log::info('Seeding with employee password: ' . $plainEmployeePassword);
        //$hashedEmployeePassword = bcrypt($plainEmployeePassword);
        $hashedEmployeePassword = Hash::make($plainEmployeePassword);

        // Custom Create 50 employee users
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => "Employee User {$i}",
                'email' => "employee_{$i}@example.com",
                'password' => $hashedEmployeePassword,
                'role' => 'EMPLOYEE'
            ]);
        }

        // Using factory from Userfactory.php to create 50 employee users
        // User::factory(50)->create([
        //     'role' => 'EMPLOYEE',
        // ]);
    }
}
