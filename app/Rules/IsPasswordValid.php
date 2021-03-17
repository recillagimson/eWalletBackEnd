<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsPasswordValid implements Rule
{
    /**
     * Determines if the password have at least one upper case
     * character
     *
     * @var boolean
     */
    private $hasUpperCase = true;

    /**
     * Determines if the password have at least one lower case
     * character
     *
     * @var boolean
     */
    private $hasLowerCase = true;

    /**
     * Determines if the password have at least one numeric
     * character
     *
     * @var boolean
     */
    private $hasNumber = true;

    /**
     * Determines if the password have at least one special
     * character
     *
     * @var boolean
     */
    private $hasSpecialCharacter = true;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->hasUpperCase = ((bool) preg_match('/[A-Z]/', $value));
        if(!$this->hasUpperCase) return false;

        $this->hasLowerCase = ((bool) preg_match('/[a-z]/', $value));
        if(!$this->hasLowerCase) return false;

        $this->hasNumber = ((bool) preg_match('/[0-9]/', $value));
        if(!$this->hasNumber) return false;

        $this->hasSpecialCharacter = ((bool) preg_match('/[@$!%*#?&_]/', $value));
        if(!$this->hasSpecialCharacter) return false;

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if(!$this->hasUpperCase)
            return ':attribute must have at least one upper case character [A-Z].';

        if(!$this->hasLowerCase)
            return ':attribute must have at least one lower case character [a-z].';

        if(!$this->hasNumber)
            return ':attribute must have at least one numeric character [0-9].';

        if(!$this->hasSpecialCharacter)
            return ':attribute must have at least one of the ff. special characters [@$!%*#?&_].';

        return '';
    }
}
