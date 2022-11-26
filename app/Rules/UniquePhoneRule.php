<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UniquePhoneRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $error_message;

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
        if(User::where('phone', $value)->count())
        {
            $this->error_message = trans('validation.custom.phone.unique');
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
        return [$this->error_message];
    }
}
