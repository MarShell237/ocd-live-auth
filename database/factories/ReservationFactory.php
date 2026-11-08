<?php

namespace Database\Factories;

use App\Models\Mode;
use App\Models\Package;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->name(),
            'business' => fake()->company(),
            'mode_id' => Mode::factory(),
            'package_id' => Package::factory(),
            'creneau' => fake()->dateTimeBetween('now', '+3 months'),
            'is_confirm' => false,
            'is_cancelled' => false,
        ];
    }
}
