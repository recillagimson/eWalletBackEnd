<?php

namespace App\Http\Requests\DrcrMemo;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
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
            'referenceNumber' => 'required',
            'status' => 'required',
            'remarks' => 'nullable'
        ];
    }
}
