<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    // Booking Statuses
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const CANCELLED = 'cancelled';

    // Trip Statuses
    const NOT_STARTED = 'not_started';
    const ON_TRIP = 'on_trip';
    const COMPLETED = 'completed';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    // Trip Statuses
    const TRIP_NOT_STARTED = 'not_started';
    const TRIP_ON_TRIP = 'on_trip';
    const TRIP_COMPLETED = 'completed';

    // Payment Statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_PARTIALLY_REFUNDED = 'partially_refunded';



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
        'booking_status' => 'string',
        'trip_status' => 'string',
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
        return $query->where('booking_status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', self::CONFIRMED);
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('booking_status', 'completed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('booking_status', 'cancelled');
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

    public function scopeActiveTrips($query)
    {
        return $query->where('trip_status', self::ON_TRIP);
    }


    public function isConfirmable()
    {
        return $this->booking_status === self::CONFIRMED &&
            $this->trip_status === self::NOT_STARTED;
    }

    public function isCancellable()
    {
        return $this->trip_status === self::NOT_STARTED;
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

    // public function isModifiable(): bool
    // {
    //     return $this->booking_status === self::STATUS_PENDING &&
    //         $this->trip_status === self::TRIP_NOT_STARTED;
    // }

    // public function isRefundable(): bool
    // {
    //     return $this->payment_status === 'paid' &&
    //         $this->booking_status === self::STATUS_CANCELLED &&
    //         $this->refunded_at === null;
    // }
}
