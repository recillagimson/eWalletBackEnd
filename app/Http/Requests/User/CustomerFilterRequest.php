<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CustomerFilterRequest extends FormRequest
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
            'from' => [
                'required_with:to',
                'date',
                'date_format:Y-m-d'
            ],
            'to' => [
                'required_with:from',
                'date',
                'date_format:Y-m-d'
            ],
            'filter_by' => [
                'nullable',
                'in:CUSTOMER_ID, CUSTOMER_NAME, TIER, STATUS'
            ],
            'filter_value' => [
                'required_with:filter_by',            ]

        ];
    }
}
