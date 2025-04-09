<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Route;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Route>
 */
class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Route::class;

    public function definition()
    {
        $cities = ['Owerri', 'Abuja', 'Enugu', 'Onitsha', 'Benin'];
        $origin = $this->faker->randomElement($cities);
        $destination = $this->faker->randomElement(array_diff($cities, [$origin]));

        return [
            'route_code' => strtoupper($this->faker->bothify('RT###')),
            'name' => "{$origin} to {$destination}",
            'origin' => $origin,
            'destination' => $destination,
            'waypoints' => json_encode($this->faker->randomElements($cities, 2)),
            'distance' => $this->faker->randomFloat(2, 5, 500),
            'estimated_duration' => $this->faker->numberBetween(15, 360),
            'type' => $this->faker->randomElement(['city', 'intercity', 'airport']),
            'base_price' => $this->faker->randomFloat(2, 100, 5000),

            'first_departure_time' => $this->faker->time('H:i:s', '06:00:00'),
            'last_departure_time' => $this->faker->time('H:i:s', '22:00:00'),
            'is_active' => true,
        ];
    }

    public function cityRoute()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'city',
                'distance' => $this->faker->randomFloat(2, 5, 50),
            ];
        });
    }

    public function intercityRoute()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'intercity',
                'distance' => $this->faker->randomFloat(2, 50, 500),
            ];
        });
    }
}
