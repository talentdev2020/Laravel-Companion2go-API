<?php

namespace App\Http\Requests\Events\Add;

use App\Http\Requests\Request;

class AddRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public static function rules()
    {
        $rules = array_merge(
            GeneralInfoRequest::rules(),
            TicketsRequest::rules(),
            [ 
                'telephone' => 'required|regex:/^(\+\d{1,3})?\d{10}$/',
                'meet_place' => 'required|string|min:10',
                'timestamp' => 'required_without:event_id|int',
                'event_destination' => 'required_without:event_id|min:3',
                'event_destination_latlng.lat' => 'nullable|required_with:event_destination_latlng.lng|numeric|between:-90.00,90.00',
                'event_destination_latlng.lng' => 'nullable|required_with:event_destination_latlng.lat|numeric|between:-180.00,180.00',
//                'event_dispatch' => 'nullable|required_if:category_id,18|min:3',
//                'event_dispatch_latlng.lat' => 'nullable|required_with:event_dispatch_latlng.lng|numeric|between:-90.00,90.00',
//                'event_dispatch_latlng.lng' => 'nullable|required_with:event_dispatch_latlng.lat|numeric|between:-180.00,180.00',
                'category_id' => 'required_without:event_id|int|exists:categories,id',
            ]
        );
        
        return $rules;
    }
}
