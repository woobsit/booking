<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        $licenseClasses = ['A', 'B', 'C', 'D', 'E'];
        $employmentTypes = ['full-time', 'part-time', 'contractor'];
        $issuingAuthorities = [
            'FRSC Lagos',
            'FRSC Abuja',
            'FRSC Port Harcourt',
            'FRSC Kano'
        ];

        return [
            'driver_uuid' => Str::uuid(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
            'national_id_number' => 'NG' . $this->faker->unique()->randomNumber(8),
            'phone' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'country' => 'Nigeria',
            'postal_code' => $this->faker->postcode,
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => $this->faker->phoneNumber,

            // License Information
            'license_number' => 'DL' . $this->faker->unique()->randomNumber(8),
            'license_class' => $this->faker->randomElement($licenseClasses),
            'license_issue_date' => $this->faker->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),
            'license_expiry_date' => $this->faker->dateTimeBetween('+1 year', '+5 years')->format('Y-m-d'),
            'license_issuing_authority' => $this->faker->randomElement($issuingAuthorities),
            'license_restrictions' => $this->faker->boolean(20) ? $this->faker->sentence : null,

            // Professional Information
            'status' => 'available',
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'monthly_salary' => $this->faker->randomFloat(2, 50000, 300000),
            'employment_type' => $this->faker->randomElement($employmentTypes),

            // Vehicle Assignment
            'current_vehicle_id' => Vehicle::factory(),

            // Documents
            'insurance_provider' => $this->faker->company,
            'insurance_expiry' => $this->faker->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
            'medical_certificate_number' => $this->faker->boolean(80) ? 'MC' . $this->faker->unique()->randomNumber(6) : null,
            'medical_certificate_expiry' => $this->faker->boolean(80) ? $this->faker->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d') : null,

            // Statistics
            'total_trips_completed' => $this->faker->numberBetween(0, 500),
            'average_rating' => $this->faker->randomFloat(2, 3, 5),

            // Additional Information
            'notes' => $this->faker->boolean(30) ? $this->faker->paragraph : null,
            'special_skills' => $this->faker->boolean(40) ? implode(', ', $this->faker->words(3)) : null,
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'available',
                'current_vehicle_id' => null
            ];
        });
    }

    public function onTrip()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'on_trip',
                'current_vehicle_id' => Vehicle::factory()
            ];
        });
    }

    public function onLeave()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'on_leave',
                'current_vehicle_id' => null
            ];
        });
    }
}
