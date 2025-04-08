<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
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
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::with(['vehicle', 'driver', 'route'])
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
                // Get available vehicle and driver
                $vehicle = Vehicle::available()
                    ->where('type', $request->vehicle_type)
                    ->firstOrFail();

                $driver = Driver::available()
                    ->whereHas('vehicle', fn($q) => $q->where('id', $vehicle->id))
                    ->firstOrFail();

                // Create booking
                $booking = Booking::create([
                    'booking_reference' => Booking::generateBookingReference(),
                    'user_id' => auth()->id(),
                    'customer_name' => auth()->user()->name,
                    'customer_email' => auth()->user()->email,
                    'customer_phone' => auth()->user()->phone,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'vehicle_type' => $request->vehicle_type,
                    'pickup_location' => $request->pickup_location,
                    'dropoff_location' => $request->dropoff_location,
                    'pickup_time' => $request->pickup_time,
                    'passenger_count' => $request->passenger_count,
                    'special_requests' => $request->special_requests,
                    'status' => 'confirmed',
                    'base_fare' => $vehicle->base_fare,
                    'distance_fare' => $this->calculateDistanceFare($request->distance, $vehicle),
                    'total_amount' => $this->calculateTotalAmount($request->distance, $vehicle),
                ]);

                // Update resources
                $vehicle->update(['status' => 'unavailable']);
                $driver->update(['status' => 'on_trip']);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'data' => new BookingResource($booking->load(['vehicle', 'driver']))
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
     * Get single booking
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking->load(['vehicle', 'driver', 'route']))
        ]);
    }

    /**
     * Update booking
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $this->authorize('update', $booking);

            $booking->update($request->validated());

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

            $booking->update(['status' => 'cancelled']);

            // Free up resources
            if ($booking->vehicle) {
                $booking->vehicle->update(['status' => 'available']);
            }

            if ($booking->driver) {
                $booking->driver->update(['status' => 'available']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => new BookingResource($booking)
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
     * Get user bookings
     */
    public function userBookings(Request $request)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['vehicle', 'driver'])
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
