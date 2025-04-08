<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 customer users
        User::factory()
            ->count(5)
            ->create();

        // Create 1 admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@booking.com',
            'password' => Hash::make('admin123'),
            'phone' => '1234567890',
            'is_active' => true,
            'is_verified' => true,
        ]);
    }
}
