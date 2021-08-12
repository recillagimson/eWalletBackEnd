<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RSBSARule implements Rule
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
        return strlen(preg_replace("/[^0-9]/", "", $value)) == 13;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The RSBSA Number is invalid.';
    }
}
