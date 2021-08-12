<?php

namespace App\Http\Requests\Disbursement;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'acccountNumber' => 'required',
            'rsbsaNumber' => 'required',
            'name' => 'nullable',
            'amount' => 'required|numeric|min:1',
        ];
    }
}
