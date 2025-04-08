<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'booking_date',
        'status',
        'user_id',
        'vehicle_id',
        'route_id',
        'pickup_location',
        'dropoff_location',
        'pickup_time',
        'estimated_dropoff_time',
        'passenger_count',
        'special_requests',
        'total_amount',
        'currency',
        'payment_status',
        'payment_method',
        'transaction_id',
        'driver_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'datetime',
        'pickup_time' => 'datetime',
        'dropoff_time' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle assigned to the booking.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the route for the booking.
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the driver assigned to the booking.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the payment associated with the booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if booking is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking is confirmed.
     */
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if booking is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Calculate the duration of the booking in minutes.
     */
    public function getDurationInMinutes()
    {
        return $this->pickup_time->diffInMinutes($this->dropoff_time);
    }

    /**
     * Generate a unique booking reference.
     */
    public static function generateBookingReference()
    {
        $prefix = 'BK';
        $unique = false;
        $reference = '';

        while (!$unique) {
            $reference = $prefix . strtoupper(substr(uniqid(), -8));
            $unique = !self::where('booking_reference', $reference)->exists();
        }

        return $reference;
    }
}
