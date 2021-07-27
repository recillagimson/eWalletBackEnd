<?php

namespace App\Http\Requests\UserPhoto;

use App\Models\IdType;
use Illuminate\Foundation\Http\FormRequest;

class ManualIDUploadRequest extends FormRequest
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
        $required_fields = [
            "tier_approval_id" => 'required|exists:tier_approvals,id',
            "id_photos"    => "required|array|min:1",
            'id_photos.*' => 'required|max:1024|mimes:jpeg,png',
            "id_type_id" => 'required|exists:id_types,id'
        ];
        
        // $inputs = request()->input();
        // if(isset($inputs['id_type_id'])) {
        //     $id_type = IdType::findOrFail($inputs['id_type_id']);
        //     if($id_type->is_primary == 1) {
        //         $required_fields['id_number'] = 'required';
        //     }
        // }

        return $required_fields;
    }
}
