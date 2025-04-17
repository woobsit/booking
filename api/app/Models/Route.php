<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'route_code',
        'name',
        'description',
        'origin',
        'destination',
        'waypoints',
        'distance',
        'estimated_duration',
        'type',
        'base_price',
        'first_departure_time',
        'last_departure_time',
        'is_active',
    ];

    protected $casts = [
        'waypoints' => 'array',
        'is_active' => 'boolean',
        'first_departure_time' => 'datetime:H:i',
        'last_departure_time' => 'datetime:H:i',
    ];

    /**
     * Get all bookings for this route
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


    public function stops()
    {
        return $this->hasMany(RouteStop::class); // or whatever your stop model is named
    }

    /**
     * Scope a query to only include active routes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include routes of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }


    /**
     * Get all waypoints as an array
     */
    public function getWaypointsArray()
    {
        return $this->waypoints ?? [];
    }

    /**
     * Check if route is active
     */
    public function isActive()
    {
        return $this->is_active;
    }
}
