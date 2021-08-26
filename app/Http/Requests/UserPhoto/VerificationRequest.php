<?php

namespace App\Http\Requests\UserPhoto;

use App\Repositories\IdType\IIdTypeRepository;
use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
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

    private IIdTypeRepository $idTypeRepo;

    public function __construct(IIdTypeRepository $idTypeRepo)
    {
        $this->idTypeRepo = $idTypeRepo;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // return ['files.*' => "mimes:jpg,png,jpeg|max:20000"];
        $required_fields = [
            'id_type_id' => 'required',
            // Validate if photo in array is less that 1MB
            // "id_photos"    => "required|array|min:2",
            'id_photos.*' => 'required|max:1024|mimes:jpeg,png',
            'id_number' => 'max:50'
        ];

        $inputs = request()->input();
        $idType = $this->idTypeRepo->get($inputs['id_type_id']);

        if($idType && $idType->is_primary == 1) {
            $required_fields['id_photos'] = "required|array|min:2|max:2";
        } else {
            $required_fields['id_photos'] = "required|array|min:2|max:4";
        }

        return $required_fields;
    }
}
