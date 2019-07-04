<?php

namespace App\Http\Requests\Events\Add;

use App\Http\Requests\Request;

/**
 * Class DatePlaceRequest
 * @package App\Http\Requests\Events\Add
 */
class DatePlaceRequest extends Request
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
        return [
            'timestamp' => 'required|int',
            'event_destination' => 'min:3|required_if:isTravel,false',
            'event_destination_latlng.lat' => 'required_if:isTravel,false|nullable|required_with:event_destination_latlng.lng|numeric|between:-90.00,90.00',
            'event_destination_latlng.lng' => 'required_if:isTravel,false|nullable|required_with:event_destination_latlng.lat|numeric|between:-180.00,180.00',
//            'event_dispatch' => 'nullable|required_if:category_id,18|min:3',
//            'event_dispatch_latlng.lat' => 'nullable|required_with:event_dispatch_latlng.lng|numeric|between:-90.00,90.00',
//            'event_dispatch_latlng.lng' => 'nullable|required_with:event_dispatch_latlng.lat|numeric|between:-180.00,180.00',
//            'category_id' => 'required|int|exists:categories,id',
        ];
    }
}
