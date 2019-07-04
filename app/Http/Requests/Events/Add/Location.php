<?php

namespace App\Http\Requests\Events\Add;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class Location extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'event_id.unique' => 'You already have proposal for the event.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public static function rules()
    {
        return [
            'event_destination' => 'required|string',
            'event_dispatch' => 'required|string',
            'event_dispatch_latlng.lat' => 'nullable|required_with:event_destination_latlng.lng|numeric|between:-90.00,90.00',
            'event_dispatch_latlng.lng' => 'nullable|required_with:event_destination_latlng.lat|numeric|between:-180.00,180.00',
            'event_destination_latlng.lat' => 'nullable|required_with:event_destination_latlng.lng|numeric|between:-90.00,90.00',
            'event_destination_latlng.lng' => 'nullable|required_with:event_destination_latlng.lat|numeric|between:-180.00,180.00',
            'changes' => 'required|numeric|min:1'
        ];
    }
}
