<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_reference' => $this->booking_reference,
            'booking_status' => $this->booking_status,
            'customer' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone
            ],
            'trip_details' => [
                'pickup_location' => $this->pickup_location,
                'dropoff_location' => $this->dropoff_location,
                'pickup_time' => $this->pickup_time->format('Y-m-d H:i:s'),
                'passenger_count' => $this->passenger_count,
                'special_requests' => $this->special_requests
            ],
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'type' => $this->vehicle->type,
                    'make' => $this->vehicle->make,
                    'model' => $this->vehicle->model
                ];
            }),
            'pricing' => [
                'total_amount' => $this->total_amount,
                'currency' => $this->currency
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
