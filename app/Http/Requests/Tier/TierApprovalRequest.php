<?php

namespace App\Http\Requests\Tier;

use Illuminate\Foundation\Http\FormRequest;

class TierApprovalRequest extends FormRequest
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
        $required_fields =  [
            'status' => 'required'
        ];

        $inputs = request()->input();

        if($inputs && isset($inputs['status'])) {
            $required_fields['remarks'] = 'required';
        }

        return $required_fields;
    }
}
