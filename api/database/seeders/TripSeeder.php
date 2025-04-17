<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trip;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 normal trips
        Trip::factory()
            ->count(15)
            ->create();

        // Create 3 completed trips
        Trip::factory()
            ->count(3)
            ->completed()
            ->create();

        // Create 2 cancelled trips
        Trip::factory()
            ->count(2)
            ->cancelled()
            ->create();
    }
}
