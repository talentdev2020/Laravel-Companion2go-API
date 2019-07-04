<?php

namespace App\Http\Requests;


/**
 * Class UpdateSettingsRequest
 * @package App\Http\Requests
 */
class UpdateSettingsRequest extends Request
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
            'first_name' => 'string',
            'last_name' => 'string',
            'birth_date' => 'date_format:d.m.Y',
            'home_address' => 'string',
            'home_address_latlng.lat' => 'nullable|required_with:home_address_latlng.lng|numeric|between:-90.00,90.00',
            'home_address_latlng.lng' => 'nullable|required_with:home_address_latlng.lat|numeric|between:-180.00,180.00',
            'postcode' => 'integer|min:1',
        ];
    }
}
