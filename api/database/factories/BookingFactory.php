<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate pickup time first (future date)
        $pickupTime = $this->faker->dateTimeBetween('+1 day', '+1 month');

        // Generate dropoff time (1-6 hours after pickup)
        $dropoffTime = (clone $pickupTime)->modify('+' . rand(1, 6) . ' hours');

        return [
            'booking_reference' => 'BK' . $this->faker->unique()->randomNumber(6),
            'booking_date' => $this->faker->dateTimeThisYear(),
            'status' => 'pending',
            'user_id' => 1,
            'vehicle_id' => 1,
            'route_id' => 1,
            'pickup_location' => $this->faker->city(),
            'dropoff_location' => $this->faker->city(),
            'pickup_time' => $pickupTime,
            'estimated_dropoff_time' => $dropoffTime,
            'passenger_count' => $this->faker->numberBetween(1, 8),
            'special_requests' => $this->faker->boolean(30) ? $this->faker->sentence() : null,
            'total_amount' => $this->faker->randomFloat(2, 150, 1500),
            'currency' => 'NG',
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'mpesa', 'cash']),
            'transaction_id' => $this->faker->uuid(),
            'driver_id' => 1,
        ];
    }
}
