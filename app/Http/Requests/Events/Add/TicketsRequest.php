<?php

namespace App\Http\Requests\Events\Add;

use App\Http\Requests\Request;

class TicketsRequest extends Request
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
            'bought' => 'required|int|min:0|max:1',
            'price' => 'nullable|required_if:bought,1|regex:/^\d*(\.\d{1,2})?$/',
        ];
    }
}
