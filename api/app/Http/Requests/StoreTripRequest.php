<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'departure_time' => 'required|date|after:now',
            'estimated_arrival_time' => 'required|date|after:departure_time',
            'notes' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
        ];
    }
}
