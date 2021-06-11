<?php

namespace App\Http\Requests\Tier;

use Illuminate\Foundation\Http\FormRequest;

class TierApprovalCommentRequest extends FormRequest
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
            'tier_approval_id' => 'required|exists:tier_approvals,id',
            'remarks' => 'required',
        ];
    }
}
