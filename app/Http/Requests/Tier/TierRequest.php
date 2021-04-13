<?php

namespace App\Http\Requests\Tier;

use Illuminate\Foundation\Http\FormRequest;

class TierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => 'required',
            "daily_limit" => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,2})?$/',
            "daily_threshold" => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,2})?$/',
            "monthly_limit" => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,2})?$/',
            "monthly_threshold" => 'required|regex:/^(-)?[0-9]+(\.[0-9]{1,2})?$/',
        ];
    }
}
