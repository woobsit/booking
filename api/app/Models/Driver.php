<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_uuid',
        'first_name',
        'last_name',
        'date_of_birth',
        'national_id_number',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'license_number',
        'license_class',
        'license_issue_date',
        'license_expiry_date',
        'license_issuing_authority',
        'license_restrictions',
        'status',
        'hire_date',
        'monthly_salary',
        'employment_type',
        'current_vehicle_id',
        'insurance_provider',
        'insurance_expiry',
        'medical_certificate_number',
        'medical_certificate_expiry',
        'total_trips_completed',
        'average_rating',
        //'regular_availability',
        'notes',
        'special_skills'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'hire_date' => 'date',
        'insurance_expiry' => 'date',
        'medical_certificate_expiry' => 'date',
        'regular_availability' => 'array',
        'hourly_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'total_trips_completed' => 'integer'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'national_id_number',
        'license_number'
    ];

    /**
     * Get the driver's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the current assigned vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    /**
     * Get all bookings assigned to this driver
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope a query to only include available drivers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Check if driver is currently available
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    /**
     * Get the driver's photo URL
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset("storage/drivers/{$this->photo}")
            : asset('images/default-driver.png');
    }
}
