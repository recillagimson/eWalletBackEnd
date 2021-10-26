<?php

namespace App\Http\Requests\EcPayRequest;

use Illuminate\Foundation\Http\FormRequest;

class CommitPaymentRequest extends FormRequest
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
            'amount' => 'required|numeric|min:50|regex:/^-?[0-9]+(?:.[0-9]{1,3})?$/'
        ];
    }
}
