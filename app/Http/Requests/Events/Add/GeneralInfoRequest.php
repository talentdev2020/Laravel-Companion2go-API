<?php

namespace App\Http\Requests\Events\Add;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class GeneralInfoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    public function messages()
    {
        return [
            'event_id.unique' => 'You already have proposal for the event.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public static function rules()
    {
        return [
            'title' => 'required|string|min:3|max:120',
            'event_id' => 'nullable|int|exists:events,id|unique:event_proposals,event_id,NULL,id,user_id,' . Auth::user()->id,
            'description' => 'required|string|min:10|max:120',
            'url' => 'nullable|url',
        ];
    }
}
