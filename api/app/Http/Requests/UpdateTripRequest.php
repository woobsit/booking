<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'driver_id' => 'sometimes|exists:drivers,id',
            'departure_time' => 'sometimes|date|after:now',
            'estimated_arrival_time' => 'sometimes|date|after:departure_time',
            'price' => 'sometimes|numeric|min:0',
        ];
    }
}
