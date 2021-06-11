<?php

namespace App\Http\Requests\DrcrMemo;

use Illuminate\Foundation\Http\FormRequest;

class DrcrMemoRequest extends FormRequest
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
            "customerId" => "required",
            "customerName" => "required",
            "availableBalance" => "required",
            "typeOfMemo" => "required",
            "category" => "required",
            "currency" => "required",
            "amount" => "required",
            "description" => "required",
        ];
    }
}
