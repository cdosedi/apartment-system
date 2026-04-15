<?php

namespace Database\Factories;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectricBill>
 */
class ElectricBillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'billing_month' => Carbon::now()->startOfMonth(),
            'total_amount' => $this->faker->numberBetween(2000, 4000),
        ];
    }

    public function forRoom(Room $room): static
    {
        return $this->state(fn (array $attributes) => [
            'room_id' => $room->id,
        ]);
    }

    public function forMonth(Carbon $month): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_month' => $month->startOfMonth(),
        ]);
    }
}
