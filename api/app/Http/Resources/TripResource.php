<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'departure_time' => $this->departure_time,
            'estimated_arrival_time' => $this->estimated_arrival_time,
            'available_seats' => $this->available_seats,
            'price' => $this->price,
            'status' => $this->status,
            'route' => [
                'origin' => $this->route->origin,
                'destination' => $this->route->destination,
                'distance' => $this->route->distance,
            ],
            'vehicle' => [
                'make' => $this->vehicle->make,
                'model' => $this->vehicle->model,
                'seat_capacity' => $this->vehicle->seat_capacity,
            ],
            'occupancy_rate' => $this->getOccupancyPercentage(),
            'can_cancel' => $this->canBeCancelled(),

            'route' => [
                'origin' => $this->route->origin,
                'destination' => $this->route->destination,
                'stops' => $this->whenLoaded('route.stops', function () {
                    return $this->route->stops->map(function ($stop) {
                        return [
                            'name' => $stop->name,
                            'arrival_time' => $stop->arrival_time,
                            'departure_time' => $stop->departure_time
                        ];
                    });
                })
            ],
        ];
    }
}
