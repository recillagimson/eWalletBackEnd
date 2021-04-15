<?php

namespace App\Http\Requests\ServiceFee;

use Illuminate\Foundation\Http\FormRequest;

class ServiceFeeRequest extends FormRequest
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
            "tier_id" => 'required',
            "transaction_category_id" => 'required',
            "implementation_date" => 'required|date',
            "amount" => "required|min:0|integer"
        ];
    }
}
