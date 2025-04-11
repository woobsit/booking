<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'registration_number',
        'make',
        'model',
        'year',
        'color',
        'type',
        'seat_capacity',
        'luggage_capacity',
        'driver_id',
        'status',
        'current_location',
        'insurance_provider',
        'insurance_expiry',
        'next_service_date',
        'has_ac',
        'has_wifi',

    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'next_service_date' => 'date',
        'has_ac' => 'boolean',
        'has_wifi' => 'boolean',

    ];

    /**
     * Get the driver assigned to this vehicle
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get all bookings for this vehicle
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('seat_capacity', '>', 0); // Available seats
    }

    public function scopeNotFull($query)
    {
        return $query->whereColumn('passenger_count', '<', 'seat_capacity');
    }
    /**
     * Scope a query to only include vehicles of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if vehicle is currently available
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }
}
