<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\UserAccount;

class RSBSAExistsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $rsbsa = preg_replace("/[^0-9]/", "", $value);

        return UserAccount::where('rsbsa_number', $rsbsa)->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute is not existing.';
    }
}
