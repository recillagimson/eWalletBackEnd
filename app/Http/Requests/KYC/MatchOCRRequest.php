<?php

namespace App\Http\Requests\KYC;

use Illuminate\Foundation\Http\FormRequest;

class MatchOCRRequest extends FormRequest
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
            'manual_input' => 'required',
            'manual_input.first_name' => 'required',
            'manual_input.last_name' => 'required',
            'ocr_response' => 'required',
            'ocr_response.first_name' => 'required',
            'ocr_response.last_name' => 'required',
        ];
    }
}
