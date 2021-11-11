<?php

namespace App\Http\Requests\BPI;

use Illuminate\Foundation\Http\FormRequest;

class BPITransactionLimitValidationRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1|max:50000'
        ];
    }
}
