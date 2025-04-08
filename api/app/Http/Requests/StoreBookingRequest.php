<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization handled at controller level
    }

    public function rules()
    {
        return [
            'vehicle_type' => 'required|in:sedan,suv,van,bus,luxury,eco',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'pickup_time' => 'required|date|after:now',
            'passenger_count' => 'required|integer|min:1|max:20',
            'distance' => 'required|numeric|min:0.1',
            'special_requests' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'pickup_time.after' => 'Pickup time must be in the future',
            'passenger_count.max' => 'Maximum of 20 passengers allowed'
        ];
    }
}