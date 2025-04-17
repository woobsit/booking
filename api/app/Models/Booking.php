<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    // Statuses are clearly defined
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment statuses cover all cases
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // Fillable fields are secure
    protected $fillable = [
        'trip_id',
        'user_id',
        'booking_reference',
        'seat_count',
        'total_amount',
        'status',
        'payment_status',
        'special_requests'
    ];

    // Relationships are optimal
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function isCancellable(): bool
    {
        return $this->status === self::STATUS_CONFIRMED &&
            !$this->trip->hasDepartured();
    }

    public static function generateReference()
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
