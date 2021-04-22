<?php

namespace App\Http\Requests\DragonPay;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMoneyCancelRequest extends FormRequest
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
            'provider' => [
                'required',
                Rule::in([
                    'DragonPay',
                ]),
            ],
            'reference_number' => 'required|max:10'
        ];
    }
}
