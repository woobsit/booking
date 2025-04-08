<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->route('booking'));
    }

    public function rules()
    {
        return [
            'pickup_location' => 'sometimes|string|max:255',
            'dropoff_location' => 'sometimes|string|max:255',
            'pickup_time' => 'sometimes|date|after:now',
            'passenger_count' => 'sometimes|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled'
        ];
    }
}