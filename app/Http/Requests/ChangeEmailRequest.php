<?php

namespace App\Http\Requests;


/**
 * Class ChangeEmailRequest
 * @package App\Http\Requests
 */
class ChangeEmailRequest extends Request
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
            'email' => 'nullable|email|unique:users,email',
            'hash' => 'nullable|string|exists:email_change,hash',
        ];
    }
}
