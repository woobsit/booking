<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{
    use HasFactory;

    // Trip statuses
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_BOARDING = 'boarding';
    const STATUS_DEPARTED = 'departed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'driver_id',
        'departure_time',
        'estimated_arrival_time',
        'available_seats',
        'price',
        'status',
        'notes'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'available_seats' => 'integer',
        'price' => 'decimal:2',
    ];

    /* ===================== */
    /* === RELATIONSHIPS === */
    /* ===================== */

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    /* ================= */
    /* === SCOPES === */
    /* ================= */

    public function scopeAvailable(Builder $query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now());
    }

    public function scopeUpcoming(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_BOARDING])
            ->where('departure_time', '>', now()->subHours(1));
    }

    public function scopeByRoute(Builder $query, $origin, $destination)
    {
        return $query->whereHas('route', function ($q) use ($origin, $destination) {
            $q->where('origin', $origin)
                ->where('destination', $destination);
        });
    }

    public function scopeBetweenDates(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('departure_time', [$startDate, $endDate]);
    }

    /* ====================== */
    /* === STATUS METHODS === */
    /* ====================== */

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_SCHEDULED &&
            $this->available_seats > 0 &&
            $this->departure_time > now();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_SCHEDULED,
            self::STATUS_BOARDING
        ]);
    }

    public function markAsBoarding()
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            throw new \LogicException('Only scheduled trips can be marked as boarding');
        }

        $this->update(['status' => self::STATUS_BOARDING]);
    }

    public function markAsDeparted()
    {
        if ($this->status !== self::STATUS_BOARDING) {
            throw new \LogicException('Only boarding trips can depart');
        }

        $this->update([
            'status' => self::STATUS_DEPARTED,
            'actual_departure_time' => now()
        ]);
    }

    public function completeTrip()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'actual_arrival_time' => now()
        ]);
    }

    public function cancelTrip(string $reason)
    {
        DB::transaction(function () use ($reason) {
            $this->update([
                'status' => self::STATUS_CANCELLED,
                'cancellation_reason' => $reason
            ]);

            // Free up all booked seats
            $this->bookings()->update(['status' => Booking::STATUS_CANCELLED]);
            $this->increment('available_seats', $this->bookings()->sum('seat_count'));
        });
    }

    /* ===================== */
    /* === BUSINESS LOGIC === */
    /* ===================== */

    public function reserveSeats(int $count): bool
    {
        if (!$this->isAvailable() || $count > $this->available_seats) {
            return false;
        }

        $this->decrement('available_seats', $count);
        return true;
    }

    public function releaseSeats(int $count): void
    {
        $this->increment('available_seats', $count);
    }

    public function getOccupancyPercentage(): float
    {
        $totalSeats = $this->vehicle->seat_capacity;
        $bookedSeats = $totalSeats - $this->available_seats;

        return ($bookedSeats / $totalSeats) * 100;
    }

    public function getEstimatedDuration(): int
    {
        return $this->departure_time->diffInMinutes($this->arrival_time);
    }

    public function getActualDuration(): ?int
    {
        if ($this->actual_departure_time && $this->actual_arrival_time) {
            return $this->actual_departure_time->diffInMinutes($this->actual_arrival_time);
        }
        return null;
    }
}
