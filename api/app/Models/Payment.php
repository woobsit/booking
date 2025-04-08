<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'booking_id',
        'user_id',
        'amount',
        
        'service_fee',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'gateway_response',
        'status',
        'paid_at',
        'refunded_at',
        'failure_reason',
        'notes',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the booking associated with this payment
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who made this payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include successful payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if payment was successful
     */
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment was refunded
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Generate a unique payment reference
     */
    public static function generatePaymentReference()
    {
        $prefix = 'PY';
        $unique = false;
        $reference = '';

        while (!$unique) {
            $reference = $prefix . strtoupper(substr(uniqid(), -8));
            $unique = !self::where('payment_reference', $reference)->exists();
        }

        return $reference;
    }

    /**
     * Process refund for this payment
     */
    public function processRefund()
    {
        // In a real app, this would call your payment gateway API
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }
}