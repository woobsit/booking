<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Vehicle;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display all trips (for admin dashboard)
     */
    public function index(): JsonResponse
    {
        $trips = Trip::with(['route', 'vehicle', 'driver', 'booking'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => TripResource::collection($trips),
            'meta' => [
                'current_page' => $trips->currentPage(),
                'total_pages' => $trips->lastPage(),
            ]
        ]);
    }

    /**
     * Create a new trip (admin only)
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        try {
            $trip = DB::transaction(function () use ($request) {
                $vehicle = Vehicle::findOrFail($request->vehicle_id);

                $trip = Trip::create([
                    'route_id' => $request->route_id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $request->driver_id,
                    'departure_time' => $request->departure_time,
                    'estimated_arrival_time' => $request->estimated_arrival_time,
                    'available_seats' => $vehicle->seat_capacity, // Initialize with full capacity
                    'price' => $request->price,
                    'status' => Trip::STATUS_SCHEDULED,
                ]);

                return $trip->load(['route', 'vehicle', 'driver']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Trip created successfully',
                'data' => new TripResource($trip)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trip creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display single trip details
     */
    public function show(Trip $trip): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new TripResource($trip->load([
                'route.stops',
                'vehicle.driver',
                'bookings.user'
            ]))
        ]);
    }

    /**
     * Update trip details (admin only)
     */
    public function update(UpdateTripRequest $request, Trip $trip): JsonResponse
    {
        // Prevent updates if trip has departed
        if ($trip->hasDepartured()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify a trip that has already departed'
            ], 422);
        }

        $trip->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Trip updated successfully',
            'data' => new TripResource($trip->fresh())
        ]);
    }

    /**
     * Cancel a trip (admin only)
     */
    public function cancel(Request $request, Trip $trip): JsonResponse
    {
        if (!$trip->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Trip cannot be cancelled in its current state'
            ], 422);
        }

        $trip->cancelTrip($request->input('reason', 'Administrative decision'));

        return response()->json([
            'success' => true,
            'message' => 'Trip cancelled successfully',
            'data' => new TripResource($trip)
        ]);
    }

    /**
     * Get available trips for booking
     */
    public function available(): JsonResponse
    {
        $trips = Trip::available()
            ->with(['route', 'vehicle.type'])
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => TripResource::collection($trips)
        ]);
    }

    /**
     * Change trip status (admin only)
     */
    public function updateStatus(Trip $trip, string $status): JsonResponse
    {
        $validStatuses = [
            'boarding' => Trip::STATUS_BOARDING,
            'depart' => Trip::STATUS_DEPARTED,
            'complete' => Trip::STATUS_COMPLETED
        ];

        if (!array_key_exists($status, $validStatuses)) {
            return response()->json(['message' => 'Invalid status action'], 422);
        }

        $method = 'markAs' . ucfirst($status);
        $trip->$method();

        return response()->json([
            'success' => true,
            'message' => "Trip status updated to {$validStatuses[$status]}",
            'data' => new TripResource($trip)
        ]);
    }
}
