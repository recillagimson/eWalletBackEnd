<?php

namespace App\Http\Requests\BPI;

use Illuminate\Foundation\Http\FormRequest;

class BPIFundTopUpRequest extends FormRequest
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
            'token' => 'required',
            'amount' => 'required|min:10|integer',
            'accountNumberToken' => 'required',
            'remarks' => 'required'
        ];
    }
}
