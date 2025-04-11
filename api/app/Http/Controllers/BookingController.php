<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Route;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Get all bookings (admin only)
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', Booking::class);

        $bookings = Booking::with(['vehicle', 'driver', 'route', 'user'])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'total_pages' => $bookings->lastPage(),
                'total_items' => $bookings->total(),
            ]
        ]);
    }

    /**
     * Create new booking
     */
    public function store(StoreBookingRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                // 1. Find the route
                $route = Route::where('origin', $request->pickup_location)
                    ->where('destination', $request->dropoff_location)
                    ->firstOrFail();

                // 2. Get available vehicle with capacity
                $vehicle = Vehicle::available()
                    ->notFull()
                    ->where('type', $request->vehicle_type)
                    ->whereHas('routes', fn($q) => $q->where('id', $route->id))
                    ->firstOrFail();

                // 3. Check passenger count doesn't exceed capacity
                $availableSeats = $vehicle->seat_capacity - $vehicle->passenger_count;
                if ($request->passenger_count > $availableSeats) {
                    throw new \Exception("Not enough seats available. Only {$availableSeats} remaining.");
                }

                // 4. Create booking
                $booking = Booking::create([
                    'booking_reference' => Booking::generateBookingReference(),
                    'user_id' => 5, //auth()->id(),
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $route->id,
                    'vehicle_type' => $request->vehicle_type,
                    'pickup_location' => $request->pickup_location,
                    'dropoff_location' => $request->dropoff_location,
                    'pickup_time' => $request->pickup_time,
                    'passenger_count' => $request->passenger_count,
                    'total_amount' => $route->base_price * $request->passenger_count,
                ]);

                // 5. Update vehicle passenger count (but don't mark unavailable)
                $vehicle->increment('passenger_count', $request->passenger_count);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'data' => new BookingResource($booking)
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get single booking with all relationships
     */
    public function show(Booking $booking)
    {
        // $this->authorize('view', $booking); // Uncomment when policies are ready

        return response()->json([
            'success' => true,
            'data' => new BookingResource(
                $booking->load([
                    'vehicle.driver', // Vehicle with its assigned driver
                    'route.stops',    // Route with all stops
                    'user',           // Booking user
                    'driver'          // Trip driver (may differ from vehicle's driver)
                ])
            ),
            'meta' => [
                'cancelable' => $booking->isCancellable(),
                'modifiable' => $booking->isModifiable()
            ]
        ]);
    }
    /**
     * Update booking (admin only)
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $this->authorize('update', $booking);

            if (!$booking->isModifiable()) {
                throw new \Exception('Only pending bookings can be modified');
            }

            $validated = $request->validated();

            DB::transaction(function () use ($booking, $validated) {
                // Handle passenger count changes
                if (isset($validated['passenger_count'])) {
                    $diff = $validated['passenger_count'] - $booking->passenger_count;
                    $booking->vehicle()->increment('passenger_count', $diff);
                }

                $booking->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => new BookingResource($booking->fresh()->load(['vehicle', 'driver']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        try {
            $this->authorize('cancel', $booking);

            if (!$booking->isCancellable()) {
                throw new \Exception('Booking cannot be cancelled in its current state');
            }

            DB::transaction(function () use ($booking) {
                // Update booking status
                $booking->update([
                    'booking_status' => Booking::STATUS_CANCELLED,
                    'cancelled_at' => now()
                ]);

                // Refund logic if paid
                if ($booking->payment_status === 'paid') {
                    $booking->update(['payment_status' => 'refund_pending']);
                    // $this->initiateRefund($booking); // Uncomment when refund system is ready
                }

                // Free up vehicle capacity
                $booking->vehicle()->decrement(
                    'passenger_count',
                    $booking->passenger_count
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => new BookingResource($booking->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cancellation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get authenticated user's bookings
     */
    public function userBookings(Request $request)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['vehicle', 'route', 'driver'])
            ->filter($request->only([
                'booking_status',
                'trip_status',
                'payment_status',
                'from_date',
                'to_date'
            ]))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'status_counts' => $this->getUserStatusCounts(auth()->id())
            ]
        ]);
    }

    // Add this helper method to the controller
    protected function getUserStatusCounts($userId)
    {
        return Booking::where('user_id', $userId)
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when booking_status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when booking_status = 'confirmed' then 1 end) as confirmed")
            ->selectRaw("count(case when trip_status = 'on_trip' then 1 end) as active_trips")
            ->first();
    }
    /**
     * Check availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|in:sedan,suv,van,bus,luxury,eco',
            'pickup_time' => 'required|date|after:now',
        ]);

        $available = Vehicle::available()
            ->where('type', $request->vehicle_type)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'available' => $available,
                'vehicle_type' => $request->vehicle_type,
                'pickup_time' => $request->pickup_time
            ]
        ]);
    }

    // Helper methods
    protected function calculateDistanceFare($distance, Vehicle $vehicle)
    {
        return $distance * $vehicle->per_km_rate;
    }

    protected function calculateTotalAmount($distance, Vehicle $vehicle)
    {
        return $vehicle->base_fare + ($distance * $vehicle->per_km_rate);
    }
}
