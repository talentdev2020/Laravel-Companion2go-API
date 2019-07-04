<?php

namespace App\Http\Requests;


class AuthRegistrationRequest extends Request
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
            'method' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'requiredif:email,EMAIL|confirmed|min:6',
            'birth_date' => 'required|string',
            'home_address' => 'required|string',
            'is_subscribed' => 'nullable|bool',
            'location' => 'required|array',
        ];
    }
}
