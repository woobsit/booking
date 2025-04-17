<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Trip;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'user_id' => User::factory(),
            'booking_reference' => Booking::generateReference(),
            'seat_count' => $this->faker->numberBetween(1, 4),
            'total_amount' => function (array $attributes) {
                $trip = Trip::find($attributes['trip_id']);
                return $trip->price * $attributes['seat_count']; // Dynamic pricing
            },
            'status' => $this->faker->randomElement([
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED
            ]),
            'payment_status' => function (array $attributes) {
                return $attributes['status'] === Booking::STATUS_CONFIRMED
                    ? Booking::PAYMENT_PAID
                    : Booking::PAYMENT_PENDING;
            },
            'special_requests' => $this->faker->boolean(30)
                ? $this->faker->sentence()
                : null
        ];
    }

    // States for testing edge cases
    public function cancelled(): static
    {
        return $this->state([
            'status' => Booking::STATUS_CANCELLED,
            'payment_status' => Booking::PAYMENT_REFUNDED
        ]);
    }
}
