<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PersonNameRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $attribute;
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
        $this->attribute = $attribute;
        return preg_match(config('blog.regexes.person_name'), $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return [trans('validation.custom.' . $this->attribute . '.regex')];
    }
}
