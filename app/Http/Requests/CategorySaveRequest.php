<?php

namespace App\Http\Requests;


/**
 * Class ChangeEmailRequest
 * @package App\Http\Requests
 */
class CategorySaveRequest extends Request
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
            'id' => 'numeric',
            'name' => 'required',
            'color' => 'required',
            'cover_photo' => 'required',
            'is_active' => 'required|boolean'
        ];
    }
}
