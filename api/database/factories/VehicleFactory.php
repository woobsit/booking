<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehicle;
//use App\Models\Driver;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Vehicle::class;

    public function definition(): array
    {
        $types = ['sedan', 'suv', 'van', 'bus', 'luxury', 'eco'];

        return [
            'registration_number' => strtoupper($this->faker->bothify('???###')),
            'make' => $this->faker->randomElement(['Toyota', 'Ford', 'Mercedes', 'BMW', 'Honda']),
            'model' => $this->faker->word,
            'year' => $this->faker->year,
            'color' => $this->faker->colorName,
            'type' => $this->faker->randomElement($types),
            'seat_capacity' => $this->faker->numberBetween(2, 16),
            'luggage_capacity' => $this->faker->randomFloat(2, 1, 20),
            //'driver_id' => Driver::factory(),
            'status' => 'available',
            'current_location' => $this->faker->city,
            'insurance_provider' => $this->faker->company,
            'insurance_expiry' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            'next_service_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
            
            'has_ac' => true,
            'has_wifi' => $this->faker->boolean(30),
            
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'available',
            ];
        });
    }

    public function unavailable()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'unavailable',
            ];
        });
    }
}
