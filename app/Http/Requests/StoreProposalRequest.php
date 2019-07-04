<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('store-proposal');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'url' => 'required|url',
            'category_id' => 'required|numeric|exists:categories,id',
            'message' => 'required|max:255',
            'place' => 'required|max:255',
            'place_latlng' => 'required|max:255',
            'date' => 'required|date|date_format:d.m.Y'
        ];
    }
}
