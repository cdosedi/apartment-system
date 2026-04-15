<?php

namespace Database\Factories;

use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeasePayment>
 */
class LeasePaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'due_date' => Carbon::now()->addMonth(),
            'amount' => $this->faker->numberBetween(4500, 6000),
            'electric_bill_amount' => 0,
            'carried_over_debt' => 0,
            'electric_bill_id' => null,
            'status' => 'pending',
            'paid_at' => null,
            'notes' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => Carbon::now()->subMonth(),
        ]);
    }
}
