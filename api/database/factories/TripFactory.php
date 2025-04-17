<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate departure time (next 30 days)
        $departure = $this->faker->dateTimeBetween('+1 day', '+30 days');

        // Estimated arrival (1-6 hours after departure)
        $arrival = (clone $departure)->modify('+' . rand(1, 6) . ' hours');

        return [
            'route_id' => Route::inRandomOrder()->first()->id,
            'vehicle_id' => Vehicle::inRandomOrder()->first()->id,
            'driver_id' => Driver::inRandomOrder()->first()->id,
            'departure_time' => $departure,
            'estimated_arrival_time' => $arrival,
            'available_seats' => fn(array $attributes) =>
            Vehicle::find($attributes['vehicle_id'])->seat_capacity,
            'price' => $this->faker->numberBetween(1000, 5000), // In base currency (e.g., cents)
            'status' => $this->faker->randomElement([
                Trip::STATUS_SCHEDULED,
                Trip::STATUS_BOARDING,
                Trip::STATUS_DEPARTED
            ]),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
        ];
    }

    // State for completed trips
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Trip::STATUS_COMPLETED,
                'actual_arrival_time' => (clone $attributes['estimated_arrival_time'])
                    ->modify('+' . rand(-30, 30) . ' minutes'),
            ];
        });
    }

    // State for cancelled trips
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Trip::STATUS_CANCELLED,
                'cancellation_reason' => $this->faker->randomElement([
                    'Mechanical issues',
                    'Weather conditions',
                    'Operator request'
                ]),
            ];
        });
    }
}
