<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lease>
 */
class LeaseFactory extends Factory
{
    public function definition(): array
    {
        $startDate = Carbon::now()->subMonths($this->faker->numberBetween(1, 12));
        $duration = $this->faker->randomElement([6, 12, 24]);

        return [
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addMonths($duration)->subDay(),
            'duration_months' => $duration,
            'monthly_rent' => $this->faker->numberBetween(4500, 6000),
            'status' => 'active',
            'pending_electric_debt' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
        ]);
    }
}
