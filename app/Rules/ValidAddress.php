<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidAddress implements Rule
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
        if (
            substr($value, 0, 1) != '4' ||
            !preg_match('/([0-9]|[A-B])/', substr($value, 1, 1)) ||
            strlen($value) != 95
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The address is invalid.';
    }
}
