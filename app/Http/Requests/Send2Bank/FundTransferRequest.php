<?php

namespace App\Http\Requests\Send2Bank;

use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class FundTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'bank_code' => 'required',
            'account_number' => 'required|max:20',
            'account_name' => 'required|max:150',
            'amount' => 'required|numeric',
            'message' => 'required|max:60',
            'mobile_number' => [
                'sometimes',
                'max:20',
                new MobileNumber(),
            ],
            'email' => 'sometimes|max:50|email',
        ];
    }
}
