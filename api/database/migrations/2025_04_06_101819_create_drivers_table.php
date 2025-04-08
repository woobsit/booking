<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            // Identification
            $table->id();
            $table->uuid('driver_uuid')->unique();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('national_id_number');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('postal_code');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');

            // License Information
            $table->string('license_number')->unique();
            $table->string('license_class');
            $table->date('license_issue_date');
            $table->date('license_expiry_date');
            $table->string('license_issuing_authority');
            $table->text('license_restrictions')->nullable();

            // Professional Information
            $table->enum('status', ['available', 'on_trip', 'on_leave', 'terminated'])->default('available');
            $table->date('hire_date');
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('employment_type')->default('contractor'); // contractor, full-time, part-time

            // Vehicle Assignment
            $table->foreignId('current_vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');

            // Documents
            $table->string('insurance_provider')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('medical_certificate_number')->nullable();
            $table->date('medical_certificate_expiry')->nullable();

            // Statistics
            $table->integer('total_trips_completed')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();

            // Availability
            //$table->json('regular_availability')->nullable()->comment('JSON of regular working hours');

            // Additional Information
            $table->text('notes')->nullable();
            $table->text('special_skills')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'current_vehicle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
