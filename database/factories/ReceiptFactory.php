<?php

namespace Database\Factories;

use App\Models\LeasePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lease_payment_id' => LeasePayment::factory(),
            'receipt_number' => 'REC-'.$this->faker->uuid(),
            'payment_method' => 'cash',
            'amount_paid' => $this->faker->numberBetween(4500, 8000),
        ];
    }

    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
        ]);
    }

    public function ecash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'e-cash',
        ]);
    }
}
