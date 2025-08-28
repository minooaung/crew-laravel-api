<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make(env('SEED_EMPLOYEE_PASSWORD', 'Platinum@123')), // Uses env password for consistency
            'role' => $this->faker->randomElement(['ADMIN', 'EMPLOYEE']), // Randomly assigns ADMIN or EMPLOYEE roles
        ];
    }

    /**
     * Define state for explicitly creating ADMIN users.
     */
    public function admin(): static
    {
        return $this->state([
            'role' => 'ADMIN',
            'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'Platinum@123')), // Matches seeder password handling
        ]);
    }

    /**
     * Define state for explicitly creating EMPLOYEE users.
     */
    public function employee(): static
    {
        return $this->state([
            'role' => 'EMPLOYEE',
            'password' => Hash::make(env('SEED_EMPLOYEE_PASSWORD', 'Platinum@123')),
        ]);
    }
}
