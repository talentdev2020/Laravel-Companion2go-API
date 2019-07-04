<?php

namespace App\Http\Requests;


/**
 * Class EventSaveRequest
 * @package App\Http\Requests
 */
class EventSaveRequest extends Request
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
    public function rules()
    {
        return [
            'name' => 'required',
            'category_id' => 'required|integer|exists:categories,id',
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:3',
            'description' => 'required|string|min:10',
            'event_location_human' => 'required|string|min:3',
            'event_location_latlng' => 'required|json',
            'event_destination_human' => 'nullable|string|min:3',
            'event_destination_latlng' => 'nullable|json',
            'date' => 'required|date_format:d.m.Y H:i',
            'price' => 'required|regex:/^\d{1,5}(\.\d{1,2})?$/',
            'is_active' => 'nullable|integer|min:0|max:1',
        ];
    }
}
