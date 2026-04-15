<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'contact_number' => '09'.$this->faker->numerical('#########'),
            'address' => 'Philippines',
            'status' => 'active',
            'created_by' => User::factory(),
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_number' => '09111111111',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
}
