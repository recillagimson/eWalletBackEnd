<?php

namespace App\Http\Requests\Report;

use App\Rules\ImageableRule;
use Illuminate\Foundation\Http\FormRequest;

class PrintRequest extends FormRequest
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
            "RSBSA" => 'required',
            "WalletNo" => 'required',
            "FullName" => 'required',
            "QRCode" => [
                'required',
                new ImageableRule(),
            ],
            "Photo" => [
                'required',
                new ImageableRule()
            ]
        ];
    }
}
