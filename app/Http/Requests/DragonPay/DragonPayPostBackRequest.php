<?php

namespace App\Http\Requests\DragonPay;

use Illuminate\Foundation\Http\FormRequest;

class DragonPayPostBackRequest extends FormRequest
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
            'txnid' => 'required',
            'refno' => 'required',
            'status' => 'required',
            'message' => 'required',
            'digest' => 'required',
        ];
    }
}
