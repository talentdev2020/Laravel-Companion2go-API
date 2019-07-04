<?php

namespace App\Http\Requests;


/**
 * Class UpdateUserProgressRequest
 * @package App\Http\Requests
 */
class VoteRequest extends Request
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
            'mark' => 'required|integer|min:1|max:5',
        ];
    }
}
