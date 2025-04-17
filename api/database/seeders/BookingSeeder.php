<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Create 15 normal bookings
        Booking::factory()
            ->count(15)
            ->create();

        // Create 3 cancelled bookings
        Booking::factory()
            ->count(3)
            ->cancelled()
            ->create();

        // Create 2 failed payment bookings
        Booking::factory()
            ->count(2)
            ->state([
                'payment_status' => Booking::PAYMENT_FAILED
            ])
            ->create();
    }
}
