<?php

namespace App\Http\Requests\BuyLoad;

use App\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class GetProductsByProviderRequest extends FormRequest
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
            'mobile_number' => [
                'required',
                new MobileNumber()
            ]
        ];
    }
}
