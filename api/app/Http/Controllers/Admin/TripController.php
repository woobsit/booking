<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    public function startTrip(StartTripRequest $request, Booking $booking)
    {
        // Validate booking can be started
        if (!$booking->isConfirmable()) {
            return response()->json([
                'message' => 'Only confirmed bookings can start trips'
            ], 422);
        }

        DB::transaction(function () use ($booking, $request) {
            // Update booking
            $booking->update([
                'trip_status' => Booking::ON_TRIP,
                'driver_id' => $request->driver_id,
                'actual_start_time' => now()
            ]);

            // Update vehicle
            $booking->vehicle()->update([
                'status' => 'on_trip',
                'current_location' => $booking->pickup_location
            ]);
        });

        return response()->json([
            'message' => 'Trip started successfully',
            'data' => $booking->fresh()
        ]);
    }

    public function completeTrip(Booking $booking)
    {
        if ($booking->trip_status !== Booking::ON_TRIP) {
            return response()->json([
                'message' => 'Only active trips can be completed'
            ], 422);
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'trip_status' => Booking::COMPLETED,
                'actual_end_time' => now()
            ]);

            $booking->vehicle()->update([
                'status' => 'available',
                'passenger_count' => 0,
                'current_location' => $booking->dropoff_location
            ]);
        });

        return response()->json([
            'message' => 'Trip completed successfully',
            'data' => $booking->fresh()
        ]);
    }
}
