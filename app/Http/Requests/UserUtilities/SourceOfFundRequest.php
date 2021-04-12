<?php

namespace App\Http\Requests\UserUtilities;

use Illuminate\Foundation\Http\FormRequest;

class SourceOfFundRequest extends FormRequest
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
            "description"=>"required",
            "status"=>"required",
        ];
    }
}
