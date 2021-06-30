<?php

namespace App\Http\Requests\DrcrMemo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemoRequest extends FormRequest
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
            "referenceNumber" => "required",
            "typeOfMemo"  =>  "nullable",
            "amount"  =>  "nullable",
            "category"  =>  "nullable",
            "description"  =>  "nullable",
            "status"  =>  "nullable"
        ];
    }
}
