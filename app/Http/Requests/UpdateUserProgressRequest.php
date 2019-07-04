<?php

namespace App\Http\Requests;


/**
 * Class UpdateUserProgressRequest
 * @package App\Http\Requests
 */
class UpdateUserProgressRequest extends Request
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
            'progress' => 'required|integer|min:2|max:6',
        ];
    }
}
